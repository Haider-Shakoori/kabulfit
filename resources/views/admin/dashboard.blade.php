@extends('layouts.app')

@section('content')
@php
    $metricMeta = [
        'users' => ['label' => 'Users', 'tone' => 'bg-blue-100 text-blue-600'],
        'products' => ['label' => 'Products', 'tone' => 'bg-purple-100 text-purple-600'],
        'orders' => ['label' => 'Orders', 'tone' => 'bg-amber-100 text-amber-600'],
        'payments' => ['label' => 'Payments', 'tone' => 'bg-emerald-100 text-emerald-600'],
        'tailoring' => ['label' => 'Tailoring', 'tone' => 'bg-cyan-100 text-cyan-600'],
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">KabulFit Administration</p>
            <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Dashboard</h1>
            <p class="mt-2 max-w-2xl text-white/80">Operational overview for the commerce platform.</p>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[260px_1fr]">
        @include('admin._nav')

        <main class="min-w-0 space-y-8">
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($metrics as $label => $value)
                    @php($meta = $metricMeta[$label] ?? ['label' => str($label)->replace('_', ' ')->title(), 'tone' => 'bg-gray-100 text-gray-600'])
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="grid h-11 w-11 place-items-center rounded-full {{ $meta['tone'] }}">●</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">{{ $meta['label'] }}</span>
                        </div>
                        <p class="mt-5 text-3xl font-bold text-gray-900">{{ number_format($value) }}</p>
                    </article>
                @endforeach
            </section>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 p-5">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Recent orders</h2>
                            <p class="mt-1 text-sm text-gray-500">Latest commerce activity</p>
                        </div>
                        @if(auth()->user()->hasPermission('orders.manage'))
                            <a href="{{ route('admin.orders.index', ['locale' => app()->getLocale()]) }}" class="text-sm font-semibold text-[#881C27]">View all</a>
                        @endif
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="flex items-center justify-between gap-4 p-5 transition hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $order->number }}</p>
                                    <p class="mt-1 truncate text-sm text-gray-500">{{ $order->user->email }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($order->status)->replace('_', ' ') }}</span>
                                    <p class="mt-2 text-xs text-gray-400">{{ $order->created_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">No orders yet.</div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5">
                        <h2 class="text-lg font-semibold text-gray-900">Recent audit activity</h2>
                        <p class="mt-1 text-sm text-gray-500">Latest administrative actions</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentAudit as $entry)
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ str($entry->action)->replace('_', ' ')->title() }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $entry->actor?->email ?? 'system' }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $entry->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">No recent audit activity.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>
@endsection
