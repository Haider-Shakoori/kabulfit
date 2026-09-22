<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use App\Notifications\ShipmentStatusChanged;
use App\Services\Orders\OrderLifecycleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use LogicException;
use Tests\TestCase;

class OrderShippingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_order_transition_records_immutable_history_event_and_queued_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['preferred_locale' => 'fa']);
        $order = $this->order($user);

        $result = app(OrderLifecycleService::class)->transition($order, 'paid', 'stripe');

        $history = $result->statusHistory()->firstOrFail();

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('customer_events', [
            'user_id' => $user->id,
            'type' => 'order.status_changed',
        ]);

        Notification::assertSentTo(
            $user,
            OrderStatusChanged::class,
            fn (OrderStatusChanged $notification) => $notification->status === 'paid'
                && $notification instanceof ShouldQueue,
        );

        $this->expectException(LogicException::class);
        $history->update(['status' => 'cancelled']);
    }

    public function test_invalid_order_transition_is_rejected(): void
    {
        $order = $this->order(User::factory()->create());

        $this->expectException(ValidationException::class);

        app(OrderLifecycleService::class)->transition($order, 'delivered');
    }

    public function test_mobile_order_history_detail_and_tracking_are_owner_scoped_without_internal_ids(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->order($user, 'paid');

        $order->items()->create([
            'sku' => 'KF-TEST-ITEM',
            'name' => 'Test item',
            'unit_price_minor' => 10000,
            'quantity' => 1,
            'line_total_minor' => 10000,
        ]);

        $shipment = app(OrderLifecycleService::class)->createShipment($order, [
            'carrier' => 'KabulFit Delivery',
            'service' => 'Standard',
            'tracking_number' => 'KFTRACK001',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/en/orders')
            ->assertOk()
            ->assertJsonPath('data.0.uuid', $order->uuid)
            ->assertJsonMissingPath('data.0.id');

        $this->getJson('/api/v1/en/orders/'.$order->uuid)
            ->assertOk()
            ->assertJsonPath('data.uuid', $order->uuid)
            ->assertJsonPath('data.items.0.sku', 'KF-TEST-ITEM')
            ->assertJsonPath('data.shipments.0.uuid', $shipment->uuid)
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.shipments.0.id');

        $this->getJson('/api/v1/en/orders/'.$order->uuid.'/tracking')
            ->assertOk()
            ->assertJsonPath('data.order_uuid', $order->uuid)
            ->assertJsonPath('data.shipments.0.tracking_number', 'KFTRACK001');

        Sanctum::actingAs($other);

        $this->getJson('/api/v1/en/orders/'.$order->uuid)->assertNotFound();
        $this->getJson('/api/v1/en/orders/'.$order->uuid.'/tracking')->assertNotFound();
    }

    public function test_shipment_lifecycle_syncs_order_and_sends_queued_localized_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['preferred_locale' => 'ps']);
        $order = $this->order($user, 'paid');

        $shipment = app(OrderLifecycleService::class)->createShipment($order, [
            'carrier' => 'KabulFit Delivery',
            'tracking_number' => 'KFTRACK002',
            'location' => 'Kabul',
        ]);

        $this->assertSame('ready', $shipment->status);
        $this->assertSame('ready', $order->fresh()->status);

        $shipment = app(OrderLifecycleService::class)->shipmentStatus(
            $shipment,
            'shipped',
            'Kabul',
            'Handed to delivery service.',
        );

        $this->assertSame('shipped', $shipment->status);
        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertDatabaseHas('shipment_events', [
            'shipment_id' => $shipment->id,
            'status' => 'shipped',
        ]);
        $this->assertDatabaseHas('customer_events', [
            'user_id' => $user->id,
            'type' => 'shipment.status_changed',
        ]);

        Notification::assertSentTo(
            $user,
            ShipmentStatusChanged::class,
            fn (ShipmentStatusChanged $notification) => $notification->status === 'shipped'
                && $notification instanceof ShouldQueue,
        );
    }

    public function test_invalid_shipment_transition_is_rejected(): void
    {
        $order = $this->order(User::factory()->create(), 'paid');
        $shipment = app(OrderLifecycleService::class)->createShipment($order, [
            'tracking_number' => 'KFTRACK003',
        ]);

        $this->expectException(ValidationException::class);

        app(OrderLifecycleService::class)->shipmentStatus($shipment, 'delivered');
    }

    public function test_mobile_event_cursor_uses_public_uuid_and_can_resume_without_database_ids(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user);
        $service = app(OrderLifecycleService::class);

        $first = $service->event($order, 'order.created', ['status' => 'pending_payment']);

        Sanctum::actingAs($user);

        $initial = $this->getJson('/api/v1/en/events')
            ->assertOk()
            ->assertJsonPath('data.0.uuid', $first->uuid)
            ->assertJsonPath('meta.next_cursor', $first->uuid)
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.cursor');

        $this->assertTrue(Str::isUuid($initial->json('meta.next_cursor')));

        $second = $service->event($order, 'order.test', ['status' => 'pending_payment']);

        $this->getJson('/api/v1/en/events?after='.$first->uuid)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', $second->uuid)
            ->assertJsonPath('meta.next_cursor', $second->uuid);
    }

    public function test_event_cursor_from_another_customer_is_rejected(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $event = app(OrderLifecycleService::class)->event(
            $this->order($owner),
            'order.created',
            ['status' => 'pending_payment'],
        );

        Sanctum::actingAs($other);

        $this->getJson('/api/v1/en/events?after='.$event->uuid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('after');
    }

    public function test_order_pages_are_private_localized_and_rtl_safe(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user);

        $this->actingAs($user)
            ->get('/en/orders')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
            ->assertSee('Orders');

        $this->actingAs($user)
            ->get('/fa/orders/'.$order->uuid)
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('سفارش');
    }

    private function order(User $user, string $status = 'pending_payment'): Order
    {
        $order = Order::query()->create([
            'uuid' => (string) Str::uuid(),
            'number' => 'KF-TEST-'.Str::upper(Str::random(8)),
            'user_id' => $user->id,
            'status' => $status,
            'payment_status' => $status === 'paid' ? 'succeeded' : 'pending',
            'currency' => 'AFN',
            'subtotal_minor' => 10000,
            'discount_minor' => 0,
            'shipping_minor' => 0,
            'total_minor' => 10000,
            'shipping_method_code' => 'standard-af',
            'shipping_address' => ['city' => 'Kabul'],
        ]);

        $order->statusHistory()->create([
            'uuid' => (string) Str::uuid(),
            'status' => $status,
            'source' => 'test',
            'occurred_at' => now(),
        ]);

        return $order;
    }
}
