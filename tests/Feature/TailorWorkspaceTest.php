<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\MeasurementDefinition;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\ShippingMethod;
use App\Models\TailorAssignment;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use App\Services\Measurements\MeasurementProfileService;
use App\Services\Measurements\TailoringService;
use App\Services\Tailoring\TailorWorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use LogicException;
use Tests\TestCase;

class TailorWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_regular_customer_cannot_access_tailor_workspace(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/en/tailor')
            ->assertForbidden();
    }

    public function test_admin_can_assign_paid_ordered_tailoring_request_to_authorized_tailor(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        [$tailoring] = $this->orderedTailoring(User::factory()->create());

        $this->actingAs($admin)
            ->post('/en/admin/tailoring/'.$tailoring->uuid.'/assignment', [
                'tailor_uuid' => $tailor->uuid,
            ])
            ->assertRedirect();

        $assignment = TailorAssignment::query()->firstOrFail();

        $this->assertSame($tailor->id, $assignment->tailor_id);
        $this->assertSame('assigned', $assignment->status);
        $this->assertDatabaseHas('tailor_assignment_events', [
            'tailor_assignment_id' => $assignment->id,
            'type' => 'assigned',
            'to_status' => 'assigned',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'tailoring.assignment_created',
        ]);
    }

    public function test_unpaid_order_cannot_be_assigned_to_tailor(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        [$tailoring] = $this->orderedTailoring(
            User::factory()->create(),
            'pending',
        );

        $this->actingAs($admin)
            ->post('/en/admin/tailoring/'.$tailoring->uuid.'/assignment', [
                'tailor_uuid' => $tailor->uuid,
            ])
            ->assertSessionHasErrors('tailoring');

        $this->assertDatabaseCount('tailor_assignments', 0);
    }

    public function test_tailor_can_only_view_own_assignments(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        $otherTailor = $this->userWithRole('tailor');

        [$firstTailoring] = $this->orderedTailoring(User::factory()->create());
        [$secondTailoring] = $this->orderedTailoring(User::factory()->create());

        $first = app(TailorWorkspaceService::class)->assign($admin, $firstTailoring, $tailor);
        $second = app(TailorWorkspaceService::class)->assign($admin, $secondTailoring, $otherTailor);

        $this->actingAs($tailor)
            ->get('/en/tailor')
            ->assertOk()
            ->assertSee($first->uuid)
            ->assertDontSee($second->uuid);

        $this->actingAs($tailor)
            ->get('/en/tailor/assignments/'.$second->uuid)
            ->assertForbidden();
    }

    public function test_tailor_workflow_enforces_transitions_and_records_append_only_activity(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        [$tailoring] = $this->orderedTailoring(User::factory()->create());

        $assignment = app(TailorWorkspaceService::class)->assign($admin, $tailoring, $tailor);

        $this->actingAs($tailor)
            ->post('/en/tailor/assignments/'.$assignment->uuid.'/status', [
                'status' => 'completed',
            ])
            ->assertSessionHasErrors('status');

        $this->actingAs($tailor)
            ->post('/en/tailor/assignments/'.$assignment->uuid.'/status', [
                'status' => 'accepted',
                'note' => 'Measurements reviewed before cutting.',
            ])
            ->assertRedirect();

        $this->actingAs($tailor)
            ->post('/en/tailor/assignments/'.$assignment->uuid.'/status', [
                'status' => 'in_progress',
            ])
            ->assertRedirect();

        $this->actingAs($tailor)
            ->post('/en/tailor/assignments/'.$assignment->uuid.'/notes', [
                'body' => 'Sleeves cut and marked for final fitting.',
            ])
            ->assertRedirect();

        $this->assertSame('in_progress', $assignment->fresh()->status);
        $this->assertDatabaseHas('tailor_assignment_notes', [
            'tailor_assignment_id' => $assignment->id,
            'body' => 'Measurements reviewed before cutting.',
        ]);
        $this->assertDatabaseHas('tailor_assignment_notes', [
            'tailor_assignment_id' => $assignment->id,
            'body' => 'Sleeves cut and marked for final fitting.',
        ]);
        $this->assertDatabaseHas('tailor_assignment_events', [
            'tailor_assignment_id' => $assignment->id,
            'type' => 'status_changed',
            'from_status' => 'accepted',
            'to_status' => 'in_progress',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $tailor->id,
            'action' => 'tailoring.assignment_note_added',
        ]);
    }

    public function test_tailor_api_is_assignment_scoped_and_exposes_only_public_identifiers(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        $otherTailor = $this->userWithRole('tailor');

        [$tailoring] = $this->orderedTailoring(User::factory()->create());
        [$otherTailoring] = $this->orderedTailoring(User::factory()->create());

        $assignment = app(TailorWorkspaceService::class)->assign($admin, $tailoring, $tailor);
        $other = app(TailorWorkspaceService::class)->assign($admin, $otherTailoring, $otherTailor);

        Sanctum::actingAs($tailor);

        $this->getJson('/api/v1/en/tailor/assignments')
            ->assertOk()
            ->assertJsonPath('data.0.uuid', $assignment->uuid)
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.tailor_id')
            ->assertJsonMissingPath('data.0.tailoring.customer.id');

        $this->getJson('/api/v1/en/tailor/assignments/'.$assignment->uuid)
            ->assertOk()
            ->assertJsonPath('data.uuid', $assignment->uuid)
            ->assertJsonPath('data.tailoring.uuid', $tailoring->uuid)
            ->assertJsonPath('data.tailoring.product.sku', $tailoring->product->sku)
            ->assertJsonStructure([
                'data' => [
                    'measurements',
                    'notes',
                    'events',
                    'allowed_statuses',
                ],
            ]);

        $this->getJson('/api/v1/en/tailor/assignments/'.$other->uuid)
            ->assertNotFound();

        $this->postJson('/api/v1/en/tailor/assignments/'.$assignment->uuid.'/status', [
            'status' => 'accepted',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->postJson('/api/v1/en/tailor/assignments/'.$assignment->uuid.'/notes', [
            'body' => 'API workmanship note.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.body', 'API workmanship note.')
            ->assertJsonMissingPath('data.id');
    }

    public function test_tailor_workspace_reads_immutable_order_measurement_snapshot(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        [$tailoring, $profile, , $item] = $this->orderedTailoring(User::factory()->create());

        $snapshot = $item->measurements()->firstOrFail();
        $original = (float) $snapshot->value_cm;

        $profile->values()
            ->whereHas('definition', fn ($query) => $query->where('code', $snapshot->definition_code))
            ->firstOrFail()
            ->update(['value_cm' => $original + 9]);

        $assignment = app(TailorWorkspaceService::class)->assign($admin, $tailoring, $tailor);

        Sanctum::actingAs($tailor);

        $response = $this->getJson('/api/v1/en/tailor/assignments/'.$assignment->uuid)
            ->assertOk();

        $measurement = collect($response->json('data.measurements'))
            ->firstWhere('code', $snapshot->definition_code);

        $this->assertNotNull($measurement);
        $this->assertEquals($original, $measurement['value_cm']);
        $this->assertEquals($original, (float) $snapshot->fresh()->value_cm);
    }

    public function test_tailor_notes_and_events_are_immutable(): void
    {
        $admin = $this->userWithRole('administrator');
        $tailor = $this->userWithRole('tailor');
        [$tailoring] = $this->orderedTailoring(User::factory()->create());

        $assignment = app(TailorWorkspaceService::class)->assign($admin, $tailoring, $tailor);
        $note = app(TailorWorkspaceService::class)->addNote($tailor, $assignment, 'Permanent workmanship note.');
        $event = $assignment->events()->latest('id')->firstOrFail();

        try {
            $note->update(['body' => 'Tampered']);
            $this->fail('Expected immutable tailor note update to fail.');
        } catch (LogicException $exception) {
            $this->assertSame('Tailor assignment notes are immutable.', $exception->getMessage());
        }

        try {
            $event->delete();
            $this->fail('Expected immutable tailor event deletion to fail.');
        } catch (LogicException $exception) {
            $this->assertSame('Tailor assignment events are immutable.', $exception->getMessage());
        }

        $this->assertDatabaseHas('tailor_assignment_notes', [
            'id' => $note->id,
            'body' => 'Permanent workmanship note.',
        ]);
        $this->assertDatabaseHas('tailor_assignment_events', [
            'id' => $event->id,
        ]);
    }

    public function test_tailor_workspace_is_localized_for_english_dari_and_pashto_with_rtl(): void
    {
        $tailor = $this->userWithRole('tailor');

        $this->actingAs($tailor)
            ->get('/en/tailor')
            ->assertOk()
            ->assertSee('Tailor Dashboard')
            ->assertSee('dir="ltr"', false);

        $this->actingAs($tailor)
            ->get('/fa/tailor')
            ->assertOk()
            ->assertSee('محیط کاری خیاط')
            ->assertSee('dir="rtl"', false);

        $this->actingAs($tailor)
            ->get('/ps/tailor')
            ->assertOk()
            ->assertSee('د خیاط کاري ساحه')
            ->assertSee('dir="rtl"', false);
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $role = Role::query()->where('slug', $slug)->firstOrFail();
        $user->roles()->attach($role);

        return $user;
    }

    private function orderedTailoring(User $customer, string $paymentStatus = 'succeeded'): array
    {
        $profile = $this->profile($customer);
        $product = Product::query()
            ->where('sku', 'KF-M-PT-001')
            ->with(['translations', 'variants.inventory'])
            ->firstOrFail();
        $variant = ProductVariant::query()
            ->where('sku', 'KF-M-PT-001-M-BLACK')
            ->firstOrFail();

        $result = app(TailoringService::class)->addToCart(
            $customer,
            $product,
            $variant,
            $profile,
            'Keep the traditional silhouette.',
        );

        $order = app(CheckoutService::class)->create(
            $customer,
            app(CartService::class)->forUser($customer),
            $this->address($customer),
            ShippingMethod::query()->where('code', 'standard-af')->firstOrFail(),
        );

        $order->update([
            'status' => $paymentStatus === 'succeeded' ? 'paid' : 'pending_payment',
            'payment_status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'succeeded' ? now() : null,
        ]);

        $tailoring = TailoringRequest::query()
            ->where('uuid', $result['tailoring']->uuid)
            ->with(['product', 'orderItem.measurements'])
            ->firstOrFail();

        return [
            $tailoring,
            $profile->fresh('values.definition'),
            $order->fresh(),
            $tailoring->orderItem,
        ];
    }

    private function profile(User $user)
    {
        $definitions = MeasurementDefinition::query()
            ->where('garment_type', 'perahan_tunban')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return app(MeasurementProfileService::class)->save($user, [
            'name' => 'Tailor workspace fit',
            'garment_type' => 'perahan_tunban',
            'display_unit' => 'cm',
            'is_default' => true,
            'measurements' => $definitions->map(fn ($definition) => [
                'code' => $definition->code,
                'value' => ((float) $definition->min_cm + (float) $definition->max_cm) / 2,
            ])->all(),
        ]);
    }

    private function address(User $user): Address
    {
        return $user->addresses()->create([
            'uuid' => (string) Str::uuid(),
            'label' => 'Workshop test address',
            'recipient_name' => $user->name,
            'phone' => '+93700000000',
            'country_code' => 'AF',
            'province' => 'Kabul',
            'city' => 'Kabul',
            'address_line1' => 'Tailor workspace test address',
            'is_default' => true,
        ]);
    }
}
