@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $statusClasses = [
        'delivered' => 'bg-green-100 text-green-700',
        'shipped' => 'bg-blue-100 text-blue-700',
        'processing' => 'bg-purple-100 text-purple-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'returned' => 'bg-red-100 text-red-700',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'pending_payment' => 'bg-yellow-100 text-yellow-700',
        'ready' => 'bg-cyan-100 text-cyan-700',
    ];
    $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700';
    $address = $order->shipping_address ?? [];
    $labels = [
        'back' => $locale === 'ps' ? 'بېرته فرمایشونو ته' : ($locale === 'fa' ? 'بازگشت به سفارش‌ها' : 'Back to Orders'),
        'summary' => $locale === 'ps' ? 'د فرمایش لنډیز' : ($locale === 'fa' ? 'خلاصه سفارش' : 'Order Summary'),
        'shipping_address' => __('commerce.shipping_address'),
        'custom' => $locale === 'ps' ? 'شخصي اندازه' : ($locale === 'fa' ? 'اندازه سفارشی' : 'Custom measurements'),
        'placed' => $locale === 'ps' ? 'ثبت شوی' : ($locale === 'fa' ? 'ثبت شده' : 'Placed'),
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('orders.index', ['locale' => $locale]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $labels['back'] }}
        </a>

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-[#2A6867]">{{ $labels['placed'] }} {{ $order->created_at?->translatedFormat('F j, Y') }}</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ __('orders.order_number', ['number' => $order->number]) }}</h1>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">
                @if($order->status === 'shipped')<x-icon name="truck" class="h-4 w-4" />@endif
                @if($order->status === 'delivered')<span>✓</span>@endif
                {{ __('orders.status_'.$order->status) }}
            </span>
        </div>

        <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
            <main class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5 sm:p-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('orders.items') }}</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <article class="flex gap-4 p-5 sm:gap-5 sm:p-6">
                                <div class="h-24 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-28 sm:w-24">
                                    @if($item->product?->primaryMedia)
                                        <x-responsive-product-image :media="$item->product->primaryMedia" :alt="$item->name" class="h-full w-full object-cover" />
                                    @else
                                        <span class="grid h-full w-full place-items-center text-sm font-bold text-gray-400">KF</span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            @if($item->product?->translation())
                                                <a href="{{ route('products.show', ['locale' => $locale, 'slug' => $item->product->translation()?->slug]) }}" class="font-semibold text-gray-900 transition hover:text-[#881C27]">{{ $item->name }}</a>
                                            @else
                                                <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                            @endif
                                            @if($item->variant_label)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->variant_label }}</p>
                                            @endif
                                        </div>
                                        <strong class="text-[#881C27]">{{ number_format($item->line_total_minor / 100, 2) }} {{ $order->currency }}</strong>
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs text-gray-600">{{ __('commerce.quantity') }}: {{ $item->quantity }}</span>
                                        @if($item->is_custom_tailored)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">
                                                <x-icon name="ruler" class="h-3 w-3" />
                                                {{ __('measurements.custom_tailoring') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($item->is_custom_tailored && $item->measurements->isNotEmpty())
                                        <details class="mt-4 rounded-xl bg-gray-50 p-3">
                                            <summary class="cursor-pointer text-sm font-semibold text-[#881C27]">{{ $labels['custom'] }}</summary>
                                            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                                @foreach($item->measurements as $measurement)
                                                    <div class="rounded-lg bg-white p-2 text-xs">
                                                        <span class="block text-gray-500">{{ $measurement->definition_name }}</span>
                                                        <strong class="text-gray-900">{{ $measurement->value_cm }} cm</strong>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('orders.timeline') }}</h2>
                    <div class="relative mt-6 space-y-6 before:absolute before:bottom-2 before:start-[11px] before:top-2 before:w-px before:bg-gray-200">
                        @foreach($order->statusHistory as $history)
                            <div class="relative flex gap-4">
                                <span class="relative z-10 mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] text-[10px] text-white">✓</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ __('orders.status_'.$history->status) }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $history->occurred_at?->translatedFormat('F j, Y · H:i') }}</p>
                                    @if($history->note)<p class="mt-1 text-sm text-gray-600">{{ $history->note }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                @forelse($order->shipments as $shipment)
                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#2A6867]">{{ __('orders.shipping') }}</p>
                                <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $shipment->carrier ?: __('orders.shipment') }}</h2>
                            </div>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ __('orders.status_'.$shipment->status) }}</span>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            @if($shipment->service)
                                <div class="rounded-xl bg-gray-50 p-4"><span class="text-xs text-gray-500">{{ __('orders.service') }}</span><strong class="mt-1 block text-sm">{{ $shipment->service }}</strong></div>
                            @endif
                            @if($shipment->tracking_number)
                                <div class="rounded-xl bg-gray-50 p-4"><span class="text-xs text-gray-500">{{ __('orders.tracking_number') }}</span><strong class="mt-1 block text-sm">{{ $shipment->tracking_number }}</strong></div>
                            @endif
                        </div>

                        @if($shipment->tracking_url)
                            <a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                                <x-icon name="truck" class="h-4 w-4" />
                                {{ __('orders.track_shipment') }}
                            </a>
                        @endif

                        @if($shipment->events->isNotEmpty())
                            <div class="mt-6 border-t border-gray-100 pt-5">
                                <h3 class="font-semibold text-gray-900">{{ __('orders.tracking_timeline') }}</h3>
                                <div class="mt-4 space-y-4">
                                    @foreach($shipment->events as $event)
                                        <div class="flex gap-3">
                                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#2A6867]"></span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ __('orders.status_'.$event->status) }}</p>
                                                <p class="text-xs text-gray-500">{{ $event->occurred_at?->translatedFormat('F j, Y · H:i') }}@if($event->location) · {{ $event->location }}@endif</p>
                                                @if($event->description)<p class="mt-1 text-sm text-gray-600">{{ $event->description }}</p>@endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>
                @empty
                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-full bg-blue-100 text-blue-600"><x-icon name="truck" class="h-5 w-5" /></span>
                            <div><h2 class="font-semibold text-gray-900">{{ __('orders.shipping') }}</h2><p class="text-sm text-gray-500">{{ __('orders.no_shipment_yet') }}</p></div>
                        </div>
                    </section>
                @endforelse
            </main>

            <aside>
                <div class="sticky top-36 space-y-6">
                    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="p-5 sm:p-6">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $labels['summary'] }}</h2>
                            <div class="mt-5 space-y-3 text-sm">
                                <div class="flex justify-between gap-4 text-gray-600"><span>{{ __('orders.subtotal') }}</span><strong class="text-gray-900">{{ number_format($order->subtotal_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                                @if($order->discount_minor > 0)
                                    <div class="flex justify-between gap-4 text-emerald-700"><span>{{ __('orders.discount') }}</span><strong>-{{ number_format($order->discount_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                                @endif
                                <div class="flex justify-between gap-4 text-gray-600"><span>{{ __('orders.shipping') }}</span><strong class="text-gray-900">{{ number_format($order->shipping_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                                <div class="flex justify-between gap-4 text-gray-600"><span>{{ __('orders.payment_status') }}</span><span class="capitalize">{{ __('orders.status_'.$order->payment_status) }}</span></div>
                            </div>
                            <div class="my-5 border-t border-gray-100"></div>
                            <div class="flex justify-between gap-4"><span class="text-lg font-semibold">{{ __('orders.total') }}</span><strong class="text-2xl text-[#D91E36]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</strong></div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <h2 class="flex items-center gap-2 font-semibold text-gray-900"><span>⌖</span>{{ $labels['shipping_address'] }}</h2>
                        <div class="mt-4 text-sm leading-6 text-gray-600">
                            <p class="font-medium text-gray-900">{{ $address['recipient_name'] ?? $address['full_name'] ?? '' }}</p>
                            <p>{{ $address['address_line1'] ?? '' }}</p>
                            @if(! empty($address['address_line2']))<p>{{ $address['address_line2'] }}</p>@endif
                            <p>{{ $address['city'] ?? '' }}@if(! empty($address['province'])), {{ $address['province'] }}@endif @if(! empty($address['postal_code'])) {{ $address['postal_code'] }}@endif</p>
                            <p>{{ $address['country_code'] ?? $address['country'] ?? '' }}</p>
                            @if(! empty($address['phone']))<p>{{ $address['phone'] }}</p>@endif
                        </div>
                    </section>

                    @if($order->payment_status === 'pending' || $order->payment_status === 'pending_payment')
                        <a href="{{ route('orders.payment', ['locale' => $locale, 'order' => $order->uuid]) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 font-semibold text-white">
                            <x-icon name="shield" class="h-5 w-5" />
                            {{ __('commerce.pay_now') }}
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
