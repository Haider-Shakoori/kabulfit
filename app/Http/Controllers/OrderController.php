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
        $user = $request->user();
        $activeStatus = (string) $request->query('status', 'all');
        $allowedTabs = ['all', 'pending', 'processing', 'shipped', 'delivered'];

        if (! in_array($activeStatus, $allowedTabs, true)) {
            $activeStatus = 'all';
        }

        $query = $user->orders()
            ->with([
                'shipments',
                'items.product.translations',
                'items.product.primaryMedia.translations',
                'items.product.primaryMedia.derivatives',
            ])
            ->latest();

        if ($activeStatus === 'pending') {
            $query->whereIn('status', ['pending', 'pending_payment']);
        } elseif ($activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        }

        $statusCounts = [
            'all' => $user->orders()->count(),
            'pending' => $user->orders()->whereIn('status', ['pending', 'pending_payment'])->count(),
            'processing' => $user->orders()->where('status', 'processing')->count(),
            'shipped' => $user->orders()->where('status', 'shipped')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
        ];

        return view('orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'activeStatus' => $activeStatus,
            'statusCounts' => $statusCounts,
            'seo' => PrivatePageSeo::make(__('orders.orders'), route('orders.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(Request $request, string $locale, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('orders.show', [
            'order' => $order->load([
                'items.measurements',
                'items.product.translations',
                'items.product.primaryMedia.translations',
                'items.product.primaryMedia.derivatives',
                'statusHistory',
                'shipments.events',
            ]),
            'seo' => PrivatePageSeo::make(__('orders.order_number', ['number' => $order->number]), route('orders.show', ['locale' => app()->getLocale(), 'order' => $order])),
        ]);
    }
}
