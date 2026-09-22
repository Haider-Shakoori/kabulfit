<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\MeasurementDefinition;
use App\Models\ShippingMethod;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use App\Services\Measurements\MeasurementProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TailoringFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_product_api_exposes_tailoring_capability(): void
    {
        $this->getJson('/api/v1/en/products/classic-afghan-perahan-tunban')
            ->assertOk()
            ->assertJsonPath('data.tailoring.enabled', true)
            ->assertJsonPath('data.tailoring.garment_type', 'perahan_tunban');
    }

    public function test_mobile_tailoring_request_adds_custom_line_to_shared_cart(): void
    {
        $user = User::factory()->create();
        $profile = $this->profile($user, 'perahan_tunban');
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
            'notes' => 'Slightly relaxed fit.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'ready')
            ->assertJsonStructure(['data' => ['uuid', 'cart_uuid', 'cart_item_uuid']]);

        $cart = app(CartService::class)->load(app(CartService::class)->forUser($user));
        $this->assertCount(1, $cart->items);
        $this->assertNotNull($cart->items->first()->tailoring_request_id);
        $this->assertStringStartsWith('tailor:', $cart->items->first()->line_key);

        $this->getJson('/api/v1/en/cart')
            ->assertOk()
            ->assertJsonPath('data.items.0.tailoring.measurement_profile', $profile->name);
    }

    public function test_removing_tailored_cart_line_cancels_tailoring_request(): void
    {
        $user = User::factory()->create();
        $profile = $this->profile($user, 'perahan_tunban');
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
        ])->assertCreated();

        $tailoringUuid = $response->json('data.uuid');
        $itemUuid = $response->json('data.cart_item_uuid');

        $this->deleteJson('/api/v1/en/cart/items/'.$itemUuid)->assertOk();

        $this->assertSame('cancelled', TailoringRequest::where('uuid', $tailoringUuid)->firstOrFail()->status);
    }

    public function test_active_tailoring_profile_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $profile = $this->profile($user, 'perahan_tunban');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
        ])->assertCreated();

        $this->deleteJson('/api/v1/en/measurement-profiles/'.$profile->uuid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('profile');
    }

    public function test_checkout_snapshots_measurements_profile_name_and_notes_immutably(): void
    {
        $user = User::factory()->create(['preferred_locale' => 'en']);
        $profile = $this->profile($user, 'perahan_tunban');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
            'notes' => 'Keep the cuffs narrow.',
        ])->assertCreated();

        $address = $this->address($user);
        $order = app(CheckoutService::class)->create(
            $user,
            app(CartService::class)->forUser($user),
            $address,
            ShippingMethod::where('code', 'standard-af')->firstOrFail(),
        );

        $item = $order->items()->with('measurements')->firstOrFail();
        $snapshot = $item->measurements->firstOrFail();
        $originalSnapshotValue = $snapshot->value_cm;

        $this->assertTrue($item->is_custom_tailored);
        $this->assertSame($profile->name, $item->measurement_profile_name);
        $this->assertSame('Keep the cuffs narrow.', $item->tailoring_notes);
        $this->assertSame($profile->values()->count(), $item->measurements->count());

        $profile->values()->firstOrFail()->update(['value_cm' => (float) $originalSnapshotValue + 1]);

        $this->assertSame($originalSnapshotValue, $snapshot->fresh()->value_cm);
        $this->assertSame(
            'ordered',
            TailoringRequest::where('uuid', $item->tailoring_request_uuid)->firstOrFail()->status,
        );
    }

    public function test_customer_can_view_only_their_tailoring_history_on_web_and_api(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $profile = $this->profile($user, 'perahan_tunban');

        Sanctum::actingAs($user);
        $created = $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
            'notes' => 'History visibility test.',
        ])->assertCreated();

        $uuid = $created->json('data.uuid');

        $this->getJson('/api/v1/en/tailoring')
            ->assertOk()
            ->assertJsonPath('data.0.uuid', $uuid)
            ->assertJsonMissingPath('data.0.id');

        $this->getJson('/api/v1/en/tailoring/'.$uuid)
            ->assertOk()
            ->assertJsonPath('data.uuid', $uuid)
            ->assertJsonPath('data.product.sku', 'KF-M-PT-001')
            ->assertJsonMissingPath('data.id');

        $this->actingAs($user)
            ->get('/en/tailoring')
            ->assertOk()
            ->assertSee($uuid)
            ->assertSee('Tailoring History');

        $this->actingAs($user)
            ->get('/en/tailoring/'.$uuid)
            ->assertOk()
            ->assertSee('History visibility test.')
            ->assertSee('Current profile measurements');

        Sanctum::actingAs($other);
        $this->getJson('/api/v1/en/tailoring/'.$uuid)->assertNotFound();

        $this->actingAs($other)
            ->get('/en/tailoring/'.$uuid)
            ->assertNotFound();
    }

    public function test_tailoring_history_uses_order_snapshot_after_profile_changes(): void
    {
        $user = User::factory()->create(['preferred_locale' => 'en']);
        $profile = $this->profile($user, 'perahan_tunban');
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/en/tailoring', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'measurement_profile_uuid' => $profile->uuid,
            'notes' => 'Snapshot history test.',
        ])->assertCreated();

        $address = $this->address($user);
        $order = app(CheckoutService::class)->create(
            $user,
            app(CartService::class)->forUser($user),
            $address,
            ShippingMethod::where('code', 'standard-af')->firstOrFail(),
        );

        $item = $order->items()->with('measurements')->firstOrFail();
        $snapshot = $item->measurements->firstOrFail();
        $original = (float) $snapshot->value_cm;

        $profile->values()->whereHas('definition', fn ($query) => $query->where('code', $snapshot->code))
            ->firstOrFail()
            ->update(['value_cm' => $original + 7]);

        $this->getJson('/api/v1/en/tailoring/'.$created->json('data.uuid'))
            ->assertOk()
            ->assertJsonPath('data.status', 'ordered')
            ->assertJsonPath('data.order.uuid', $order->uuid)
            ->assertJsonPath('data.measurements.0.value_cm', $original);

        $this->actingAs($user)
            ->get('/en/tailoring/'.$created->json('data.uuid'))
            ->assertOk()
            ->assertSee('Measurements at order time')
            ->assertSee('immutable order-time snapshot');
    }

    public function test_tailoring_pages_have_complete_english_dari_and_pashto_copy(): void
    {
        $user = User::factory()->create();
        $this->profile($user, 'perahan_tunban');

        $this->actingAs($user)
            ->get('/en/products/classic-afghan-perahan-tunban/tailor')
            ->assertOk()
            ->assertSee('Choose a compatible saved measurement profile')
            ->assertDontSee('measurements.tailoring_intro');

        $this->actingAs($user)
            ->get('/fa/tailoring')
            ->assertOk()
            ->assertSee('تاریخچه خیاطی');

        $this->actingAs($user)
            ->get('/ps/tailoring')
            ->assertOk()
            ->assertSee('د خیاطۍ تاریخچه');
    }

    private function profile(User $user, string $garmentType)
    {
        $definitions = MeasurementDefinition::query()
            ->where('garment_type', $garmentType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return app(MeasurementProfileService::class)->save($user, [
            'name' => 'Primary fit',
            'garment_type' => $garmentType,
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
            'label' => 'Home',
            'recipient_name' => 'Customer',
            'phone' => '+93700000000',
            'country_code' => 'AF',
            'province' => 'Kabul',
            'city' => 'Kabul',
            'address_line1' => 'Test address',
            'is_default' => true,
        ]);
    }
}
