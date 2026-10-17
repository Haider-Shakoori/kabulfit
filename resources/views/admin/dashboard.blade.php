@extends('layouts.admin')

@section('content')
@php
    $currency = $recentOrders->first()?->currency ?? 'USD';
    $statCards = [
        [
            'title' => 'Total Revenue',
            'value' => number_format(($metrics['revenue_minor'] ?? 0) / 100, 2).' '.$currency,
            'icon' => 'chart',
            'tone' => 'bg-green-500',
            'hint' => 'Paid & fulfilled orders',
        ],
        [
            'title' => 'Total Orders',
            'value' => number_format($metrics['orders'] ?? 0),
            'icon' => 'bag',
            'tone' => 'bg-blue-500',
            'hint' => number_format($metrics['pending_orders'] ?? 0).' pending',
        ],
        [
            'title' => 'Products',
            'value' => number_format($metrics['products'] ?? 0),
            'icon' => 'package',
            'tone' => 'bg-purple-500',
            'hint' => 'Catalog items',
        ],
        [
            'title' => 'Customers',
            'value' => number_format($metrics['customers'] ?? 0),
            'icon' => 'users',
            'tone' => 'bg-orange-500',
            'hint' => 'Registered accounts',
        ],
    ];
@endphp

<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name ?: 'Admin' }}</p>
        @if(auth()->user()->hasPermission('products.manage'))
            <a href="{{ route('admin.products.index', ['locale' => app()->getLocale()]) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md bg-gradient-to-r from-[#D91E36] via-[#00A651] to-black px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 sm:w-auto">
                <x-icon name="plus" class="h-4 w-4" />
                Add Product
            </a>
        @endif
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:mb-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
        @foreach($statCards as $stat)
            <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-lg sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs text-gray-500 sm:text-sm">{{ $stat['title'] }}</p>
                        <p class="mt-1 truncate text-2xl font-bold text-gray-900 sm:mt-2 sm:text-3xl">{{ $stat['value'] }}</p>
                        <div class="mt-1 flex items-center gap-1 text-xs text-green-600 sm:mt-2 sm:text-sm">
                            <span>↗</span>
                            <span>{{ $stat['hint'] }}</span>
                        </div>
                    </div>
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-white sm:h-12 sm:w-12 {{ $stat['tone'] }}">
                        <x-icon :name="$stat['icon']" class="h-5 w-5 sm:h-6 sm:w-6" />
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-2 lg:gap-8">
        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-gray-900 sm:text-lg">Recent Orders</h2>
                @if(auth()->user()->hasPermission('orders.manage'))
                    <a href="{{ route('admin.orders.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50 sm:text-sm">
                        View All <span>›</span>
                    </a>
                @endif
            </div>
            <div class="p-4 sm:p-6">
                @forelse($recentOrders->take(5) as $order)
                    <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="mb-3 flex items-center justify-between gap-4 rounded-lg bg-gray-50 p-3 transition-colors last:mb-0 hover:bg-gray-100 sm:p-4">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold sm:text-base">{{ $order->number }}</p>
                            <p class="text-xs text-gray-500 sm:text-sm">{{ $order->created_at?->translatedFormat('M j, Y') }}</p>
                        </div>
                        <div class="shrink-0 text-end">
                            <p class="text-sm font-bold sm:text-base">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</p>
                            <span @class([
                                'mt-1 inline-flex rounded-full px-2 py-0.5 text-xs capitalize',
                                'bg-green-100 text-green-700' => in_array($order->status, ['delivered', 'paid'], true),
                                'bg-blue-100 text-blue-700' => in_array($order->status, ['shipped', 'ready'], true),
                                'border border-gray-200 text-gray-600' => ! in_array($order->status, ['delivered', 'paid', 'shipped', 'ready'], true),
                            ])>{{ str($order->status)->replace('_', ' ') }}</span>
                        </div>
                    </a>
                @empty
                    <p class="py-8 text-center text-sm text-gray-500">No orders yet</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-gray-900 sm:text-lg">Quick Actions</h2>
            </div>
            <div class="space-y-3 p-4 sm:p-6">
                @if(auth()->user()->hasPermission('products.manage'))
                    <a href="{{ route('admin.products.index', ['locale' => app()->getLocale()]) }}" class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100 sm:p-4">
                        <span class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-purple-100 text-purple-600 sm:h-10 sm:w-10"><x-icon name="package" class="h-4 w-4 sm:h-5 sm:w-5" /></span>
                            <span class="min-w-0">
                                <strong class="block truncate text-sm sm:text-base">Manage Products</strong>
                                <span class="block truncate text-xs text-gray-500 sm:text-sm">Add, edit or remove</span>
                            </span>
                        </span>
                        <span class="text-gray-400">›</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('orders.manage'))
                    <a href="{{ route('admin.orders.index', ['locale' => app()->getLocale()]) }}" class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100 sm:p-4">
                        <span class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-600 sm:h-10 sm:w-10"><x-icon name="bag" class="h-4 w-4 sm:h-5 sm:w-5" /></span>
                            <span class="min-w-0">
                                <strong class="block truncate text-sm sm:text-base">Process Orders</strong>
                                <span class="block truncate text-xs text-gray-500 sm:text-sm">{{ number_format($metrics['pending_orders'] ?? 0) }} pending</span>
                            </span>
                        </span>
                        <span class="text-gray-400">›</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('content.manage'))
                    <a href="{{ route('admin.content.index', ['locale' => app()->getLocale()]) }}" class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100 sm:p-4">
                        <span class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-green-100 text-green-600 sm:h-10 sm:w-10"><x-icon name="tag" class="h-4 w-4 sm:h-5 sm:w-5" /></span>
                            <span class="min-w-0">
                                <strong class="block truncate text-sm sm:text-base">Manage Categories</strong>
                                <span class="block truncate text-xs text-gray-500 sm:text-sm">Organize storefront content</span>
                            </span>
                        </span>
                        <span class="text-gray-400">›</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('customers.manage'))
                    <a href="{{ route('admin.customers.index', ['locale' => app()->getLocale()]) }}" class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100 sm:p-4">
                        <span class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-orange-100 text-orange-600 sm:h-10 sm:w-10"><x-icon name="users" class="h-4 w-4 sm:h-5 sm:w-5" /></span>
                            <span class="min-w-0">
                                <strong class="block truncate text-sm sm:text-base">View Customers</strong>
                                <span class="block truncate text-xs text-gray-500 sm:text-sm">{{ number_format($metrics['customers'] ?? 0) }} total</span>
                            </span>
                        </span>
                        <span class="text-gray-400">›</span>
                    </a>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection
