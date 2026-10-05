@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $labels = [
        'all' => $locale === 'ps' ? 'ټول' : ($locale === 'fa' ? 'همه' : 'All'),
        'pending' => __('orders.status_pending'),
        'processing' => __('orders.status_processing'),
        'shipped' => __('orders.status_shipped'),
        'delivered' => __('orders.status_delivered'),
        'no_orders' => $locale === 'ps' ? 'تر اوسه فرمایش نشته' : ($locale === 'fa' ? 'هنوز سفارشی ندارید' : 'No orders yet'),
        'start' => $locale === 'ps' ? 'د خپلو خوښې محصولاتو د موندلو لپاره خرید پیل کړئ.' : ($locale === 'fa' ? 'برای پیدا کردن محصولات مورد علاقه‌تان خرید را شروع کنید.' : 'Start shopping to find products you love.'),
        'shop_now' => $locale === 'ps' ? 'اوس خرید وکړئ' : ($locale === 'fa' ? 'اکنون خرید کنید' : 'Shop Now'),
        'select' => $locale === 'ps' ? 'د جزیاتو لپاره یو فرمایش وټاکئ' : ($locale === 'fa' ? 'برای مشاهده جزئیات یک سفارش را انتخاب کنید' : 'Select an order to view details'),
        'shipping_address' => __('commerce.shipping_address'),
        'view_details' => __('orders.view_order'),
    ];

    $orderData = $orders->getCollection()->map(function ($order) use ($locale) {
        return [
            'uuid' => $order->uuid,
            'number' => $order->number,
            'date' => $order->created_at?->translatedFormat('F j, Y'),
            'status' => $order->status,
            'status_label' => __('orders.status_'.$order->status),
            'payment_status' => $order->payment_status,
            'payment_label' => __('orders.status_'.$order->payment_status),
            'subtotal' => number_format($order->subtotal_minor / 100, 2).' '.$order->currency,
            'shipping' => number_format($order->shipping_minor / 100, 2).' '.$order->currency,
            'discount' => number_format($order->discount_minor / 100, 2).' '.$order->currency,
            'total' => number_format($order->total_minor / 100, 2).' '.$order->currency,
            'detail_url' => route('orders.show', ['locale' => $locale, 'order' => $order->uuid]),
            'shipping_address' => $order->shipping_address,
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'variant' => $item->variant_label,
                'tailored' => $item->is_custom_tailored,
                'line_total' => number_format($item->line_total_minor / 100, 2).' '.$order->currency,
                'image' => $item->product?->primaryMedia?->url(),
            ])->values()->all(),
        ];
    })->values()->all();

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
@endphp

<div class="min-h-screen bg-[#FDFBF7]" x-data='{ orders: @js($orderData), selected: @js($orderData[0] ?? null) }'>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">{{ __('orders.orders') }}</h1>

        @if($statusCounts['all'] === 0)
            <div class="py-20 text-center">
                <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-full bg-gray-100">
                    <x-icon name="bag" class="h-12 w-12 text-gray-400" />
                </div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $labels['no_orders'] }}</h2>
                <p class="mt-2 text-gray-500">{{ $labels['start'] }}</p>
                <a href="{{ route('shop', ['locale' => $locale]) }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#D91E36] via-[#00A651] to-black px-6 py-3 font-semibold text-white transition hover:opacity-90">
                    <x-icon name="bag" class="h-4 w-4" />
                    {{ $labels['shop_now'] }}
                </a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-3">
                <section class="min-w-0 lg:col-span-2">
                    <div class="mb-6 flex max-w-full gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1">
                        @foreach([
                            'all' => $labels['all'],
                            'pending' => $labels['pending'],
                            'processing' => $labels['processing'],
                            'shipped' => $labels['shipped'],
                            'delivered' => $labels['delivered'],
                        ] as $status => $label)
                            <a
                                href="{{ route('orders.index', ['locale' => $locale, 'status' => $status === 'all' ? null : $status]) }}"
                                @class([
                                    'shrink-0 rounded-lg px-3 py-2 text-sm font-medium transition',
                                    'bg-white text-gray-900 shadow-sm' => $activeStatus === $status,
                                    'text-gray-500 hover:text-gray-900' => $activeStatus !== $status,
                                ])
                            >{{ $label }}@if($status === 'all') ({{ $statusCounts[$status] }})@endif</a>
                        @endforeach
                    </div>

                    <div class="space-y-4">
                        @forelse($orders as $order)
                            @php
                                $cardStatusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <button
                                type="button"
                                @click="selected = orders.find(order => order.uuid === @js($order->uuid))"
                                class="block w-full rounded-2xl border bg-white p-4 text-start shadow-sm transition hover:shadow-lg"
                                :class="selected?.uuid === @js($order->uuid) ? 'border-[#D91E36] ring-2 ring-[#D91E36]/20' : 'border-gray-100'"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900">{{ $order->number }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $order->created_at?->translatedFormat('F j, Y') }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $cardStatusClass }}">
                                        @if($order->status === 'shipped')<x-icon name="truck" class="h-3 w-3" />@endif
                                        @if($order->status === 'delivered')<span>✓</span>@endif
                                        {{ __('orders.status_'.$order->status) }}
                                    </span>
                                </div>

                                <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
                                    @foreach($order->items->take(4) as $item)
                                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                            @if($item->product?->primaryMedia)
                                                <x-responsive-product-image :media="$item->product->primaryMedia" :alt="$item->name" class="h-full w-full object-cover" />
                                            @else
                                                <span class="grid h-full w-full place-items-center text-xs font-bold text-gray-400">KF</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 4)
                                        <span class="grid h-16 w-16 shrink-0 place-items-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-500">+{{ $order->items->count() - 4 }}</span>
                                    @endif
                                </div>

                                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                                    <span class="text-sm text-gray-500">{{ $order->items->sum('quantity') }} {{ __('orders.items') }}</span>
                                    <strong class="text-lg text-[#D91E36]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</strong>
                                </div>
                            </button>
                        @empty
                            <div class="rounded-2xl bg-white py-14 text-center text-gray-500 shadow-sm">{{ $locale === 'ps' ? 'په دې برخه کې فرمایش نشته.' : ($locale === 'fa' ? 'سفارشی در این بخش وجود ندارد.' : 'No orders in this section.') }}</div>
                        @endforelse
                    </div>

                    @if($orders->hasPages())
                        <div class="mt-8">{{ $orders->links() }}</div>
                    @endif
                </section>

                <aside class="hidden lg:block">
                    <template x-if="selected">
                        <div class="sticky top-36 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="border-b border-gray-100 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">{{ __('commerce.order') }}</p>
                                        <h2 class="text-lg font-bold text-gray-900" x-text="selected.number"></h2>
                                        <p class="mt-1 text-xs text-gray-500" x-text="selected.date"></p>
                                    </div>
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600" x-text="selected.status_label"></span>
                                </div>
                            </div>

                            <div class="max-h-72 space-y-3 overflow-y-auto p-5">
                                <template x-for="(item, index) in selected.items" :key="index">
                                    <div class="flex gap-3">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                            <template x-if="item.image"><img :src="item.image" :alt="item.name" class="h-full w-full object-cover"></template>
                                            <template x-if="!item.image"><span class="grid h-full w-full place-items-center text-xs font-bold text-gray-400">KF</span></template>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="line-clamp-1 text-sm font-medium text-gray-900" x-text="item.name"></p>
                                            <p class="mt-1 text-xs text-gray-500"><span x-text="'{{ __('commerce.quantity') }}: ' + item.quantity"></span></p>
                                            <span x-show="item.tailored" class="mt-1 inline-flex rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-orange-700">{{ __('measurements.custom_tailoring') }}</span>
                                        </div>
                                        <strong class="text-xs text-gray-900" x-text="item.line_total"></strong>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t border-gray-100 p-5">
                                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900"><span>⌖</span>{{ $labels['shipping_address'] }}</h3>
                                <template x-if="selected.shipping_address">
                                    <div class="mt-2 text-xs leading-5 text-gray-500">
                                        <p x-text="selected.shipping_address.recipient_name || selected.shipping_address.full_name || ''"></p>
                                        <p x-text="selected.shipping_address.address_line1 || ''"></p>
                                        <p><span x-text="selected.shipping_address.city || ''"></span><span x-show="selected.shipping_address.country_code"> · <span x-text="selected.shipping_address.country_code"></span></span></p>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-2 border-t border-gray-100 p-5 text-sm">
                                <div class="flex justify-between text-gray-500"><span>{{ __('orders.subtotal') }}</span><span x-text="selected.subtotal"></span></div>
                                <div class="flex justify-between text-gray-500"><span>{{ __('orders.shipping') }}</span><span x-text="selected.shipping"></span></div>
                                <div class="flex justify-between font-bold text-gray-900"><span>{{ __('orders.total') }}</span><span class="text-[#D91E36]" x-text="selected.total"></span></div>
                            </div>

                            <div class="border-t border-gray-100 p-5">
                                <a :href="selected.detail_url" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-3 text-sm font-semibold text-white">
                                    {{ $labels['view_details'] }}
                                    <span class="rtl:rotate-180">›</span>
                                </a>
                            </div>
                        </div>
                    </template>

                    <template x-if="!selected">
                        <div class="sticky top-36 rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm">
                            <x-icon name="bag" class="mx-auto h-12 w-12 text-gray-300" />
                            <p class="mt-4 text-sm text-gray-500">{{ $labels['select'] }}</p>
                        </div>
                    </template>
                </aside>
            </div>
        @endif
    </div>
</div>
@endsection
