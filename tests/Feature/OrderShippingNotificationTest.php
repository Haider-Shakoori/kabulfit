<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use App\Services\Orders\OrderLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderShippingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_order_transition_records_history_event_and_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['preferred_locale' => 'fa']);
        $order = $this->order($user);

        app(OrderLifecycleService::class)->transition($order, 'paid', 'stripe');

        $this->assertDatabaseHas('order_status_histories', ['order_id' => $order->id, 'status' => 'paid']);
        $this->assertDatabaseHas('customer_events', ['user_id' => $user->id, 'type' => 'order.status_changed']);
        Notification::assertSentTo($user, OrderStatusChanged::class);
    }

    public function test_mobile_order_history_is_owner_scoped(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->order($user);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/en/orders')->assertOk()
            ->assertJsonPath('data.0.uuid', $order->uuid)
            ->assertJsonMissingPath('data.0.id');

        Sanctum::actingAs($other);
        $this->getJson('/api/v1/en/orders/'.$order->uuid)->assertNotFound();
    }

    public function test_shipment_status_creates_tracking_event_and_mobile_cursor(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user);
        $shipment = Shipment::create([
            'uuid' => (string) Str::uuid(),
            'order_id' => $order->id,
            'carrier' => 'KabulFit Delivery',
            'tracking_number' => 'KFTRACK001',
            'status' => 'ready',
        ]);

        app(OrderLifecycleService::class)->shipmentStatus($shipment, 'shipped', 'Kabul', 'Handed to delivery service.');

        $this->assertDatabaseHas('shipment_events', ['shipment_id' => $shipment->id, 'status' => 'shipped']);
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/en/events?after=0')->assertOk()
            ->assertJsonPath('data.0.type', 'shipment.status_changed')
            ->assertJsonStructure(['meta' => ['next_cursor']]);
    }

    private function order(User $user): Order
    {
        return Order::create([
            'uuid' => (string) Str::uuid(),
            'number' => 'KF-TEST-'.Str::upper(Str::random(8)),
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'payment_status' => 'pending',
            'currency' => 'AFN',
            'subtotal_minor' => 10000,
            'discount_minor' => 0,
            'shipping_minor' => 0,
            'total_minor' => 10000,
            'shipping_method_code' => 'standard-af',
            'shipping_address' => ['city' => 'Kabul'],
        ]);
    }
}
