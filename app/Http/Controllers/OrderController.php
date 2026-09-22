<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $request->user()->orders()->with('shipments')->latest()->paginate(20),
            'seo' => PrivatePageSeo::make(__('orders.orders'), route('orders.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(Request $request, string $locale, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('orders.show', [
            'order' => $order->load(['items.measurements', 'statusHistory', 'shipments.events']),
            'seo' => PrivatePageSeo::make(__('orders.order_number', ['number' => $order->number]), route('orders.show', ['locale' => app()->getLocale(), 'order' => $order])),
        ]);
    }
}
