@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $orders->total() }} total orders</p>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-3 grid place-items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
            <input type="search" placeholder="Search orders..." class="w-full rounded-md border border-gray-200 py-2.5 ps-10 pe-3 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
        </div>
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
            <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-gray-900 sm:text-lg">{{ $order->number }}</h2>
                            <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($order->status)->replace('_', ' ') }}</span>
                            <span class="rounded-full bg-[#2A6867]/10 px-2.5 py-1 text-xs capitalize text-[#2A6867]">{{ str($order->payment_status)->replace('_', ' ') }}</span>
                        </div>
                        <p class="mt-2 truncate text-sm text-gray-500">{{ $order->user->email }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $order->created_at?->translatedFormat('M j, Y · H:i') }}</p>
                    </div>
                    <div class="shrink-0 text-end">
                        <p class="text-lg font-bold text-gray-900 sm:text-xl">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</p>
                        <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="mt-3 inline-flex items-center gap-2 rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Open <span>›</span></a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white py-14 text-center text-sm text-gray-500 shadow-sm">No orders yet.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $orders->links() }}</div>
</div>
@endsection
