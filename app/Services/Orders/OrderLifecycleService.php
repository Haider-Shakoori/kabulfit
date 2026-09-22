<?php

namespace App\Services\Orders;

use App\Models\CustomerEvent;
use App\Models\Order;
use App\Models\Shipment;
use App\Notifications\OrderStatusChanged;
use App\Notifications\ShipmentStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderLifecycleService
{
    private const ORDER_TRANSITIONS = [
        'pending_payment' => ['paid', 'payment_failed', 'cancelled'],
        'payment_failed' => ['paid', 'cancelled'],
        'paid' => ['processing', 'ready', 'shipped', 'cancelled', 'refunded'],
        'processing' => ['ready', 'shipped', 'cancelled', 'refunded'],
        'ready' => ['shipped', 'cancelled', 'refunded'],
        'shipped' => ['delivered', 'returned', 'refunded'],
        'delivered' => ['returned', 'refunded'],
        'returned' => ['refunded'],
        'cancelled' => ['refunded'],
        'refunded' => [],
    ];

    private const SHIPMENT_TRANSITIONS = [
        'pending' => ['ready', 'shipped', 'exception'],
        'ready' => ['shipped', 'exception'],
        'shipped' => ['in_transit', 'out_for_delivery', 'delivered', 'exception', 'returned'],
        'in_transit' => ['out_for_delivery', 'delivered', 'exception', 'returned'],
        'out_for_delivery' => ['delivered', 'exception', 'returned'],
        'exception' => ['in_transit', 'out_for_delivery', 'delivered', 'returned'],
        'delivered' => ['returned'],
        'returned' => [],
    ];

    public function transition(
        Order $order,
        string $status,
        string $source = 'system',
        ?string $note = null,
        bool $notify = true,
    ): Order {
        return DB::transaction(function () use ($order, $status, $source, $note, $notify): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === $status) {
                return $order;
            }

            $allowed = self::ORDER_TRANSITIONS[$order->status] ?? [];
            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => __('orders.invalid_transition', [
                        'from' => $order->status,
                        'to' => $status,
                    ]),
                ]);
            }

            $order->update(['status' => $status]);
            $order->statusHistory()->create([
                'uuid' => (string) Str::uuid(),
                'status' => $status,
                'source' => $source,
                'note' => $note,
                'occurred_at' => now(),
            ]);

            $this->event($order, 'order.status_changed', [
                'status' => $status,
                'source' => $source,
            ]);

            if ($notify) {
                $order->user->notify(
                    (new OrderStatusChanged($order, $status))
                        ->locale($order->user->preferredLocale()),
                );
            }

            return $order->fresh(['statusHistory', 'shipments.events']);
        }, 3);
    }

    public function createShipment(Order $order, array $attributes): Shipment
    {
        return DB::transaction(function () use ($order, $attributes): Shipment {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! in_array($order->status, ['paid', 'processing', 'ready'], true)) {
                throw ValidationException::withMessages([
                    'shipment' => __('orders.shipment_not_allowed'),
                ]);
            }

            $shipment = $order->shipments()->create([
                'uuid' => (string) Str::uuid(),
                'carrier' => $attributes['carrier'] ?? null,
                'service' => $attributes['service'] ?? null,
                'tracking_number' => $attributes['tracking_number'] ?? null,
                'tracking_url' => $attributes['tracking_url'] ?? null,
                'status' => 'ready',
            ]);

            $shipment->events()->create([
                'uuid' => (string) Str::uuid(),
                'status' => 'ready',
                'location' => $attributes['location'] ?? null,
                'description' => $attributes['description'] ?? null,
                'occurred_at' => now(),
            ]);

            if ($order->status !== 'ready') {
                $this->transition(
                    $order,
                    'ready',
                    'shipment',
                    __('orders.shipment_prepared_note'),
                    false,
                );
            }

            $this->event($order, 'shipment.created', [
                'shipment_uuid' => $shipment->uuid,
                'status' => 'ready',
                'tracking_number' => $shipment->tracking_number,
            ]);

            $order->user->notify(
                (new ShipmentStatusChanged($order, $shipment, 'ready'))
                    ->locale($order->user->preferredLocale()),
            );

            return $shipment->fresh('events');
        }, 3);
    }

    public function shipmentStatus(
        Shipment $shipment,
        string $status,
        ?string $location = null,
        ?string $description = null,
    ): Shipment {
        return DB::transaction(function () use ($shipment, $status, $location, $description): Shipment {
            $shipment = Shipment::query()
                ->whereKey($shipment->id)
                ->lockForUpdate()
                ->with('order.user')
                ->firstOrFail();

            if ($shipment->status === $status) {
                return $shipment;
            }

            $allowed = self::SHIPMENT_TRANSITIONS[$shipment->status] ?? [];
            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => __('orders.invalid_shipment_transition', [
                        'from' => $shipment->status,
                        'to' => $status,
                    ]),
                ]);
            }

            $marksShipped = in_array($status, [
                'shipped',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'returned',
            ], true);

            $shipment->update([
                'status' => $status,
                'shipped_at' => $marksShipped && ! $shipment->shipped_at
                    ? now()
                    : $shipment->shipped_at,
                'delivered_at' => $status === 'delivered'
                    ? now()
                    : $shipment->delivered_at,
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

            $this->syncOrderFromShipment($shipment->order, $status);

            $shipment->order->user->notify(
                (new ShipmentStatusChanged($shipment->order, $shipment, $status))
                    ->locale($shipment->order->user->preferredLocale()),
            );

            return $shipment->fresh('events');
        }, 3);
    }

    public function event(Order $order, string $type, array $payload = []): CustomerEvent
    {
        return CustomerEvent::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'type' => $type,
            'payload' => [
                'order_uuid' => $order->uuid,
                'order_number' => $order->number,
                ...$payload,
            ],
            'occurred_at' => now(),
        ]);
    }

    private function syncOrderFromShipment(Order $order, string $shipmentStatus): void
    {
        $target = match ($shipmentStatus) {
            'shipped', 'in_transit', 'out_for_delivery' => 'shipped',
            'delivered' => 'delivered',
            'returned' => 'returned',
            default => null,
        };

        if (! $target || $order->status === $target) {
            return;
        }

        $this->transition(
            $order,
            $target,
            'shipment',
            __('orders.shipment_synced_note'),
            false,
        );
    }
}
