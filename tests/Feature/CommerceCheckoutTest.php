<?php

namespace Tests\Feature;

use App\Contracts\Payments\PaymentGateway;
use App\Models\{Address,InventoryItem,Order,Payment,Product,ProductVariant,ShippingMethod,User};
use App\Services\Commerce\{CartService,CheckoutService};
use App\Services\Payments\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommerceCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->app->bind(PaymentGateway::class, fn () => new class implements PaymentGateway
        {
            public function createIntent(Payment $payment): array
            {
                return ['id' => 'pi_test_'.$payment->uuid, 'client_secret' => 'pi_secret_test', 'status' => 'requires_payment_method'];
            }

            public function refund(Payment $payment, ?int $amountMinor = null): array
            {
                return ['id' => 're_test', 'status' => 'succeeded'];
            }
        });
    }

    public function test_authenticated_customer_can_add_variant_to_cart_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/en/cart/items', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'quantity' => 2,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.0.sku', 'KF-M-PT-001-M-BLACK')
            ->assertJsonPath('data.subtotal_minor', 1300000);
    }

    public function test_checkout_reserves_stock_and_returns_stripe_client_secret(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $address = $user->addresses()->create([
            'uuid' => (string) Str::uuid(), 'label' => 'Home', 'recipient_name' => 'Customer',
            'phone' => '+93700000000', 'country_code' => 'AF', 'province' => 'Kabul', 'city' => 'Kabul',
            'address_line1' => 'Test address', 'is_default' => true,
        ]);
        $this->postJson('/api/v1/en/cart/items', [
            'product_slug' => 'classic-afghan-perahan-tunban',
            'variant_sku' => 'KF-M-PT-001-M-BLACK',
            'quantity' => 2,
        ])->assertCreated();

        $before = InventoryItem::whereHas('variant', fn ($q) => $q->where('sku', 'KF-M-PT-001-M-BLACK'))->firstOrFail()->quantity_reserved;

        $response = $this->postJson('/api/v1/en/checkout', [
            'address_uuid' => $address->uuid,
            'shipping_method' => 'standard-af',
            'coupon' => 'WELCOME10',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.stripe.client_secret', 'pi_secret_test');

        $inventory = InventoryItem::whereHas('variant', fn ($q) => $q->where('sku', 'KF-M-PT-001-M-BLACK'))->firstOrFail();
        $this->assertSame($before + 2, $inventory->quantity_reserved);
        $this->assertDatabaseHas('payments', ['provider' => 'stripe', 'status' => 'processing']);
    }

    public function test_succeeded_payment_event_is_idempotent_and_captures_reserved_stock_once(): void
    {
        $user = User::factory()->create();
        $address = $user->addresses()->create([
            'uuid' => (string) Str::uuid(), 'label' => 'Home', 'recipient_name' => 'Customer',
            'phone' => '+93700000000', 'country_code' => 'AF', 'province' => 'Kabul', 'city' => 'Kabul',
            'address_line1' => 'Test address', 'is_default' => true,
        ]);
        $product = Product::where('sku', 'KF-M-PT-001')->firstOrFail();
        $variant = ProductVariant::where('sku', 'KF-M-PT-001-M-BLACK')->firstOrFail();
        $cart = app(CartService::class)->forUser($user);
        app(CartService::class)->add($cart, $product->load('translations'), $variant->load(['inventory','product']), 1);
        $order = app(CheckoutService::class)->create($user, $cart, $address, ShippingMethod::where('code', 'standard-af')->firstOrFail());
        $result = app(PaymentService::class)->initiate($order);
        $payment = $result['payment'];
        $inventory = InventoryItem::where('product_variant_id', $variant->id)->firstOrFail();
        $onHand = $inventory->quantity_on_hand;

        $payload = ['id' => 'evt_success_1', 'type' => 'payment_intent.succeeded'];
        app(PaymentService::class)->handleStripeEvent('evt_success_1', 'payment_intent.succeeded', ['id' => $payment->provider_payment_id], $payload);
        app(PaymentService::class)->handleStripeEvent('evt_success_1', 'payment_intent.succeeded', ['id' => $payment->provider_payment_id], $payload);

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame($onHand - 1, $inventory->fresh()->quantity_on_hand);
        $this->assertSame(1, \App\Models\PaymentEvent::where('provider_event_id', 'evt_success_1')->count());
    }
}
