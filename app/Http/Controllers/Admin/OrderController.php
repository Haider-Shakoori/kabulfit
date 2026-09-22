<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Admin\AuditService;
use App\Services\Orders\OrderLifecycleService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Order::class);

        return view('admin.orders.index', [
            'orders' => Order::query()->with(['user', 'payment', 'shipments'])->latest()->paginate(30),
            'seo' => PrivatePageSeo::make('Admin Orders', route('admin.orders.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(string $locale, Order $order): View
    {
        $this->authorize('view', $order);

        return view('admin.orders.show', [
            'order' => $order->load(['user', 'items.measurements', 'payment', 'statusHistory', 'shipments.events']),
            'seo' => PrivatePageSeo::make('Admin Order '.$order->number, route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order])),
        ]);
    }

    public function transition(Request $request, string $locale, Order $order, OrderLifecycleService $orders, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $order);
        $data = $request->validate(['status' => 'required|string|max:40', 'note' => 'nullable|string|max:1000']);

        $from = $order->status;
        $orders->transition($order, $data['status'], 'admin', $data['note'] ?? null);
        $audit->record($request->user(), 'order.status_changed', $order, ['from' => $from, 'to' => $data['status'], 'note' => $data['note'] ?? null]);

        return back()->with('status', 'Order status updated.');
    }

    public function shipment(Request $request, string $locale, Order $order, OrderLifecycleService $orders, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $order);
        $data = $request->validate([
            'carrier' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
            'tracking_url' => 'nullable|url|max:2000',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $shipment = $orders->createShipment($order, $data);
        $audit->record($request->user(), 'shipment.created', $shipment, ['order_uuid' => $order->uuid, 'tracking_number' => $shipment->tracking_number]);

        return back()->with('status', 'Shipment created.');
    }

    public function shipmentStatus(Request $request, string $locale, Shipment $shipment, OrderLifecycleService $orders, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $shipment->order);
        $data = $request->validate([
            'status' => 'required|string|max:40',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $from = $shipment->status;
        $orders->shipmentStatus($shipment, $data['status'], $data['location'] ?? null, $data['description'] ?? null);
        $audit->record($request->user(), 'shipment.status_changed', $shipment, ['from' => $from, 'to' => $data['status'], 'order_uuid' => $shipment->order->uuid]);

        return back()->with('status', 'Shipment status updated.');
    }
}
