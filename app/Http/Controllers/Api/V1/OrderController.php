<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerEvent;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['per_page' => 'nullable|integer|min:1|max:50']);
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->with(['shipments.events'])
            ->latest()
            ->paginate((int) ($data['per_page'] ?? 20));

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order) => $this->summary($order))->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, string $locale, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        return response()->json([
            'data' => $this->detail($order->load([
                'items.measurements',
                'statusHistory',
                'shipments.events',
            ])),
        ]);
    }

    public function tracking(Request $request, string $locale, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);
        $order->load('shipments.events');

        return response()->json([
            'data' => [
                'order_uuid' => $order->uuid,
                'order_number' => $order->number,
                'order_status' => $order->status,
                'shipments' => $this->shipmentData($order),
            ],
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $data = $request->validate([
            'after' => 'nullable|uuid',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $after = $data['after'] ?? null;
        $limit = (int) ($data['limit'] ?? 50);
        $afterId = 0;

        if ($after) {
            $cursor = $request->user()
                ->customerEvents()
                ->where('uuid', $after)
                ->first();

            if (! $cursor) {
                throw ValidationException::withMessages([
                    'after' => __('orders.invalid_event_cursor'),
                ]);
            }

            $afterId = $cursor->id;
        }

        $events = $request->user()
            ->customerEvents()
            ->with('order')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $events->count() > $limit;
        $events = $events->take($limit)->values();

        return response()->json([
            'data' => $events->map(fn (CustomerEvent $event) => [
                'uuid' => $event->uuid,
                'type' => $event->type,
                'order_uuid' => $event->order?->uuid,
                'payload' => $event->payload,
                'occurred_at' => $event->occurred_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'next_cursor' => $events->last()?->uuid ?? $after,
                'has_more' => $hasMore,
            ],
        ]);
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }

    private function summary(Order $order): array
    {
        $latestShipment = $order->shipments->first();

        return [
            'uuid' => $order->uuid,
            'number' => $order->number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'currency' => $order->currency,
            'total_minor' => $order->total_minor,
            'items_count' => (int) ($order->items_count ?? 0),
            'latest_shipment' => $latestShipment ? [
                'uuid' => $latestShipment->uuid,
                'status' => $latestShipment->status,
                'carrier' => $latestShipment->carrier,
                'tracking_number' => $latestShipment->tracking_number,
            ] : null,
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }

    private function detail(Order $order): array
    {
        return [
            ...$this->summary($order),
            'subtotal_minor' => $order->subtotal_minor,
            'discount_minor' => $order->discount_minor,
            'shipping_minor' => $order->shipping_minor,
            'shipping_method_code' => $order->shipping_method_code,
            'shipping_address' => $order->shipping_address,
            'paid_at' => $order->paid_at?->toIso8601String(),
            'items' => $order->items->map(fn ($item) => [
                'sku' => $item->sku,
                'name' => $item->name,
                'variant_label' => $item->variant_label,
                'quantity' => $item->quantity,
                'unit_price_minor' => $item->unit_price_minor,
                'line_total_minor' => $item->line_total_minor,
                'is_custom_tailored' => $item->is_custom_tailored,
                'tailoring_request_uuid' => $item->tailoring_request_uuid,
                'measurement_profile_name' => $item->measurement_profile_name,
                'tailoring_notes' => $item->tailoring_notes,
                'measurements' => $item->measurements->map(fn ($measurement) => [
                    'code' => $measurement->definition_code,
                    'name' => $measurement->definition_name,
                    'value_cm' => (float) $measurement->value_cm,
                ])->values(),
            ])->values(),
            'status_history' => $order->statusHistory->map(fn ($history) => [
                'uuid' => $history->uuid,
                'status' => $history->status,
                'source' => $history->source,
                'note' => $history->note,
                'occurred_at' => $history->occurred_at?->toIso8601String(),
            ])->values(),
            'shipments' => $this->shipmentData($order),
        ];
    }

    private function shipmentData(Order $order)
    {
        return $order->shipments->map(fn ($shipment) => [
            'uuid' => $shipment->uuid,
            'carrier' => $shipment->carrier,
            'service' => $shipment->service,
            'tracking_number' => $shipment->tracking_number,
            'tracking_url' => $shipment->tracking_url,
            'status' => $shipment->status,
            'shipped_at' => $shipment->shipped_at?->toIso8601String(),
            'delivered_at' => $shipment->delivered_at?->toIso8601String(),
            'events' => $shipment->events->map(fn ($event) => [
                'uuid' => $event->uuid,
                'status' => $event->status,
                'location' => $event->location,
                'description' => $event->description,
                'occurred_at' => $event->occurred_at?->toIso8601String(),
            ])->values(),
        ])->values();
    }
}
