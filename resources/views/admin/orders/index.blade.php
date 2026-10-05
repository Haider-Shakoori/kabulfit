@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Operations</p>
            <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Orders</h1>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[260px_1fr]">
        @include('admin._nav')

        <main class="min-w-0">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Orders</h2>
                    <p class="mt-1 text-sm text-gray-500">Review payment, fulfillment, and customer order status.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-gray-600 shadow-sm">{{ $orders->total() }}</span>
            </div>

            <div class="space-y-4">
                @forelse($orders as $order)
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-lg">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-bold text-gray-900">{{ $order->number }}</h2>
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($order->status)->replace('_', ' ') }}</span>
                                    <span class="rounded-full bg-[#2A6867]/10 px-2.5 py-1 text-xs capitalize text-[#2A6867]">{{ str($order->payment_status)->replace('_', ' ') }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">{{ $order->user->email }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $order->created_at?->translatedFormat('F j, Y · H:i') }}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-xl font-bold text-[#881C27]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</p>
                                <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="mt-3 inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">Open <span>›</span></a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white py-14 text-center text-gray-500 shadow-sm">No orders yet.</div>
                @endforelse
            </div>

            <div class="mt-8">{{ $orders->links() }}</div>
        </main>
    </div>
</div>
@endsection
