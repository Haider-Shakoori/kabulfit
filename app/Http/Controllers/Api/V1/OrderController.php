<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()->with(['items.measurements', 'shipments.events'])->latest()->paginate(20);

        return response()->json(['data' => $orders->getCollection()->map(fn ($order) => $this->data($order)), 'meta' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage()]]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json(['data' => $this->data($order->load(['items.measurements', 'statusHistory', 'shipments.events']))]);
    }

    public function events(Request $request): JsonResponse
    {
        $after = max(0, (int) $request->query('after', 0));
        $events = $request->user()->customerEvents()->where('id', '>', $after)->orderBy('id')->limit(100)->get();

        return response()->json(['data' => $events->map(fn ($event) => [
            'cursor' => $event->id,
            'uuid' => $event->uuid,
            'type' => $event->type,
            'order_uuid' => $event->order?->uuid,
            'payload' => $event->payload,
            'occurred_at' => $event->occurred_at?->toIso8601String(),
        ]), 'meta' => ['next_cursor' => $events->last()?->id ?? $after]]);
    }

    private function data(Order $order): array
    {
        return [
            'uuid' => $order->uuid,
            'number' => $order->number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'currency' => $order->currency,
            'total_minor' => $order->total_minor,
            'created_at' => $order->created_at?->toIso8601String(),
            'shipments' => $order->shipments->map(fn ($shipment) => [
                'uuid' => $shipment->uuid,
                'carrier' => $shipment->carrier,
                'service' => $shipment->service,
                'tracking_number' => $shipment->tracking_number,
                'tracking_url' => $shipment->tracking_url,
                'status' => $shipment->status,
                'events' => $shipment->events->map(fn ($event) => [
                    'uuid' => $event->uuid, 'status' => $event->status, 'location' => $event->location,
                    'description' => $event->description, 'occurred_at' => $event->occurred_at?->toIso8601String(),
                ])->values(),
            ])->values(),
        ];
    }
}
