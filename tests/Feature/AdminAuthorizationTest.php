<?php

namespace Tests\Feature;

use App\Contracts\Payments\PaymentGateway;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LogicException;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_regular_customer_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/en/admin')
            ->assertForbidden();
    }

    public function test_role_permissions_gate_admin_sections(): void
    {
        $user = $this->userWithRole('support');

        $this->actingAs($user)
            ->get('/en/admin')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);

        $this->actingAs($user)
            ->get('/en/admin/orders')
            ->assertOk();

        $this->actingAs($user)
            ->get('/en/admin/products')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/en/admin/settings')
            ->assertForbidden();
    }

    public function test_super_admin_bootstrap_command_grants_full_access_without_hardcoded_credentials(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test']);

        $this->assertSame(0, Artisan::call('admin:grant-super', ['email' => $user->email]));
        $this->assertTrue($user->fresh()->hasRole('super-admin'));

        $this->actingAs($user)
            ->get('/en/admin/roles')
            ->assertOk();

        $this->actingAs($user)
            ->get('/en/admin/audit')
            ->assertOk();
    }

    public function test_administrator_cannot_grant_super_admin_role(): void
    {
        $administrator = $this->userWithRole('administrator');
        $customer = User::factory()->create();

        $this->actingAs($administrator)
            ->put('/en/admin/customers/'.$customer->uuid.'/roles', [
                'roles' => ['super-admin'],
            ])
            ->assertSessionHasErrors('roles');

        $this->assertFalse($customer->fresh()->hasRole('super-admin'));
    }

    public function test_super_admin_role_assignment_is_audited(): void
    {
        $admin = $this->userWithRole('super-admin');
        $customer = User::factory()->create();

        $this->actingAs($admin)
            ->put('/en/admin/customers/'.$customer->uuid.'/roles', [
                'roles' => ['support'],
            ])
            ->assertRedirect();

        $this->assertTrue($customer->fresh()->hasRole('support'));
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'customer.roles_updated',
        ]);
    }

    public function test_settings_update_changes_homepage_seo_and_creates_immutable_audit_log(): void
    {
        $admin = $this->userWithRole('super-admin');

        $this->actingAs($admin)
            ->put('/en/admin/settings', [
                'contact_email' => 'support@kabulfit.test',
                'titles' => [
                    'en' => 'KabulFit Admin SEO Test',
                    'fa' => 'عنوان آزمایشی کابل‌فیت',
                    'ps' => 'د کابل‌فټ ازمایښتي سرلیک',
                ],
                'descriptions' => [
                    'en' => 'English SEO description.',
                    'fa' => 'توضیحات آزمایشی.',
                    'ps' => 'ازمایښتي تشریح.',
                ],
            ])
            ->assertRedirect();

        $this->get('/en')
            ->assertOk()
            ->assertSee('<title>KabulFit Admin SEO Test</title>', false);

        $log = AuditLog::query()->where('action', 'settings.updated')->firstOrFail();

        $this->expectException(LogicException::class);
        $log->update(['action' => 'tampered']);
    }

    public function test_authorized_full_stripe_refund_updates_payment_order_event_and_audit(): void
    {
        $admin = $this->userWithRole('super-admin');
        $payment = $this->paidPayment();

        $this->app->instance(PaymentGateway::class, new class implements PaymentGateway
        {
            public function createIntent(Payment $payment): array
            {
                return ['id' => 'pi_test', 'client_secret' => 'secret', 'status' => 'requires_payment_method'];
            }

            public function cancel(Payment $payment): array
            {
                return ['id' => $payment->provider_payment_id, 'status' => 'canceled'];
            }

            public function refund(Payment $payment, ?int $amountMinor = null): array
            {
                return ['id' => 're_admin_test', 'status' => 'succeeded'];
            }
        });

        $this->actingAs($admin)
            ->post('/en/admin/payments/'.$payment->uuid.'/refund', [
                'reason' => 'Customer requested full refund.',
            ])
            ->assertRedirect();

        $this->assertSame('refunded', $payment->fresh()->status);
        $this->assertSame('refunded', $payment->order->fresh()->payment_status);
        $this->assertSame('refunded', $payment->order->fresh()->status);

        $this->assertDatabaseHas('payment_events', [
            'payment_id' => $payment->id,
            'provider_event_id' => 'admin-refund:re_admin_test',
            'type' => 'refund.admin',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'payment.refund_requested',
        ]);
    }

    public function test_user_with_payment_view_but_without_refund_permission_cannot_refund(): void
    {
        $support = $this->userWithRole('support');
        $payment = $this->paidPayment();

        $this->actingAs($support)
            ->get('/en/admin/payments/'.$payment->uuid)
            ->assertOk();

        $this->actingAs($support)
            ->post('/en/admin/payments/'.$payment->uuid.'/refund')
            ->assertForbidden();
    }

    public function test_payment_screen_exposes_provider_event_audit_trail(): void
    {
        $admin = $this->userWithRole('super-admin');
        $payment = $this->paidPayment();

        PaymentEvent::query()->create([
            'payment_id' => $payment->id,
            'provider' => 'stripe',
            'provider_event_id' => 'evt_test_admin_audit',
            'type' => 'payment_intent.succeeded',
            'payload' => ['id' => 'evt_test_admin_audit'],
            'processed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/en/admin/payments/'.$payment->uuid)
            ->assertOk()
            ->assertSee('evt_test_admin_audit')
            ->assertSee('payment_intent.succeeded');
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $role = Role::query()->where('slug', $slug)->firstOrFail();
        $user->roles()->attach($role);

        return $user;
    }

    private function paidPayment(): Payment
    {
        $customer = User::factory()->create();

        $order = Order::query()->create([
            'uuid' => (string) Str::uuid(),
            'number' => 'KF-ADMIN-'.Str::upper(Str::random(8)),
            'user_id' => $customer->id,
            'status' => 'paid',
            'payment_status' => 'succeeded',
            'currency' => 'AFN',
            'subtotal_minor' => 15000,
            'discount_minor' => 0,
            'shipping_minor' => 0,
            'total_minor' => 15000,
            'shipping_method_code' => 'standard-af',
            'shipping_address' => ['city' => 'Kabul'],
            'paid_at' => now(),
        ]);

        $order->statusHistory()->create([
            'uuid' => (string) Str::uuid(),
            'status' => 'paid',
            'source' => 'test',
            'occurred_at' => now(),
        ]);

        return Payment::query()->create([
            'uuid' => (string) Str::uuid(),
            'order_id' => $order->id,
            'provider' => 'stripe',
            'provider_payment_id' => 'pi_admin_refund_test',
            'status' => 'succeeded',
            'currency' => 'AFN',
            'amount_minor' => 15000,
            'idempotency_key' => 'admin-refund-test-'.$order->uuid,
        ]);
    }
}
