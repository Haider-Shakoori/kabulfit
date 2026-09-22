<?php

namespace App\Services\Orders;

use App\Models\CustomerEvent;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderLifecycleService
{
    public function transition(Order $order, string $status, string $source = 'system', ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $status, $source, $note): Order {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === $status) {
                return $order;
            }

            $order->update(['status' => $status]);
            $order->statusHistory()->create([
                'uuid' => (string) Str::uuid(),
                'status' => $status,
                'source' => $source,
                'note' => $note,
                'occurred_at' => now(),
            ]);
            $this->event($order, 'order.status_changed', ['status' => $status]);
            $order->user->notify((new OrderStatusChanged($order, $status))->locale($order->user->preferredLocale()));

            return $order->fresh(['statusHistory', 'shipments.events']);
        }, 3);
    }

    public function shipmentStatus(Shipment $shipment, string $status, ?string $location = null, ?string $description = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $status, $location, $description): Shipment {
            $shipment = Shipment::whereKey($shipment->id)->lockForUpdate()->firstOrFail();
            $shipment->update([
                'status' => $status,
                'shipped_at' => $status === 'shipped' && ! $shipment->shipped_at ? now() : $shipment->shipped_at,
                'delivered_at' => $status === 'delivered' ? now() : $shipment->delivered_at,
            ]);
            $shipment->events()->create([
                'uuid' => (string) Str::uuid(),
                'status' => $status,
                'location' => $location,
                'description' => $description,
                'occurred_at' => now(),
            ]);
            $this->event($shipment->order, 'shipment.status_changed', [
                'shipment_uuid' => $shipment->uuid,
                'status' => $status,
                'tracking_number' => $shipment->tracking_number,
            ]);

            return $shipment->fresh('events');
        }, 3);
    }

    public function event(Order $order, string $type, array $payload): CustomerEvent
    {
        return CustomerEvent::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'type' => $type,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);
    }
}
