@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <a href="{{ route('admin.orders.index', ['locale' => app()->getLocale()]) }}" class="mb-6 inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">← Back to Orders</a>

    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Order operations</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $order->number }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $order->user->email }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-700">{{ str($order->status)->replace('_', ' ') }}</span>
            <span class="rounded-full bg-[#2A6867]/10 px-3 py-1 text-xs font-semibold capitalize text-[#2A6867]">{{ str($order->payment_status)->replace('_', ' ') }}</span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <main class="space-y-6 xl:col-span-2">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Items</h2>
                <div class="mt-4 divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $item->sku }}@if($item->variant_label) · {{ $item->variant_label }}@endif</p>
                                @if($item->is_custom_tailored)<span class="mt-2 inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">Custom tailored</span>@endif
                            </div>
                            <div class="text-end">
                                <p class="text-sm text-gray-500">Qty {{ $item->quantity }}</p>
                                <p class="mt-1 font-semibold text-gray-900">{{ number_format($item->line_total_minor / 100, 2) }} {{ $order->currency }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Status History</h2>
                <div class="relative mt-5 space-y-5 before:absolute before:bottom-2 before:start-[11px] before:top-2 before:w-px before:bg-gray-200">
                    @foreach($order->statusHistory as $history)
                        <div class="relative flex gap-4">
                            <span class="relative z-10 mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-[#8B1538] text-[10px] text-white">✓</span>
                            <div>
                                <p class="font-medium capitalize text-gray-900">{{ str($history->status)->replace('_', ' ') }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $history->occurred_at?->translatedFormat('M j, Y · H:i') }} · {{ $history->source }}</p>
                                @if($history->note)<p class="mt-1 text-sm text-gray-600">{{ $history->note }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Create Shipment</h2>
                <form method="POST" action="{{ route('admin.orders.shipments.store', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="mt-5 grid gap-4 md:grid-cols-2">
                    @csrf
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Carrier</span><input name="carrier" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Service</span><input name="service" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Tracking number</span><input name="tracking_number" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Tracking URL</span><input name="tracking_url" type="url" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Location</span><input name="location" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description</span><textarea name="description" rows="3" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                    <div class="md:col-span-2"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Create shipment</button></div>
                </form>
            </section>

            @foreach($order->shipments as $shipment)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div><p class="text-xs text-gray-500">Shipment</p><h2 class="mt-1 text-lg font-semibold text-gray-900">{{ $shipment->tracking_number ?: $shipment->uuid }}</h2><p class="mt-1 text-sm text-gray-500">{{ $shipment->carrier }}</p></div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold capitalize text-blue-700">{{ str($shipment->status)->replace('_', ' ') }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.shipments.status', ['locale' => app()->getLocale(), 'shipment' => $shipment]) }}" class="mt-5 grid gap-4 md:grid-cols-3">
                        @csrf
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Status</span><select name="status" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach(['shipped','in_transit','out_for_delivery','delivered','exception','returned'] as $status)<option value="{{ $status }}">{{ str($status)->replace('_', ' ')->title() }}</option>@endforeach</select></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Location</span><input name="location" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Description</span><input name="description" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <div class="md:col-span-3"><button type="submit" class="rounded-md border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Update shipment</button></div>
                    </form>
                </section>
            @endforeach
        </main>

        <aside class="space-y-6">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Order State</h2>
                <form method="POST" action="{{ route('admin.orders.transition', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="mt-5 space-y-4">
                    @csrf
                    <label class="grid gap-1.5"><span class="text-sm font-medium">New status</span><select name="status" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach(['paid','processing','ready','shipped','delivered','returned','cancelled','refunded'] as $status)<option value="{{ $status }}">{{ str($status)->replace('_', ' ')->title() }}</option>@endforeach</select></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Internal/customer note</span><textarea name="note" rows="4" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                    <button type="submit" class="w-full rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Change status</button>
                </form>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Totals</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between text-gray-500"><span>Subtotal</span><strong class="text-gray-900">{{ number_format($order->subtotal_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                    <div class="flex justify-between text-gray-500"><span>Shipping</span><strong class="text-gray-900">{{ number_format($order->shipping_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                    <div class="flex justify-between text-gray-500"><span>Discount</span><strong class="text-gray-900">{{ number_format($order->discount_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between"><span class="font-semibold text-gray-900">Total</span><strong class="text-lg text-[#8B1538]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                </div>
            </section>

            @if($order->payment)
                <a href="{{ route('admin.payments.show', ['locale' => app()->getLocale(), 'payment' => $order->payment]) }}" class="flex w-full items-center justify-center rounded-md border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">View payment</a>
            @endif
        </aside>
    </div>
</div>
@endsection
