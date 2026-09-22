<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->hasPermission('dashboard.view'), 403);

        return view('admin.dashboard', [
            'metrics' => [
                'orders' => Order::query()->count(),
                'paid_orders' => Order::query()->whereIn('status', ['paid', 'processing', 'ready', 'shipped', 'delivered'])->count(),
                'customers' => User::query()->count(),
                'products' => Product::query()->count(),
                'tailoring' => TailoringRequest::query()->count(),
                'payments' => Payment::query()->count(),
            ],
            'recentOrders' => Order::query()->with('user')->latest()->limit(8)->get(),
            'recentAudit' => $request->user()->hasPermission('audit.view')
                ? AuditLog::query()->with('actor')->latest('created_at')->limit(8)->get()
                : collect(),
            'seo' => PrivatePageSeo::make('KabulFit Admin', route('admin.dashboard', ['locale' => app()->getLocale()])),
        ]);
    }
}
