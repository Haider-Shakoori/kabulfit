@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $continueLabel = $locale === 'ps' ? 'خرید ته دوام ورکړئ' : ($locale === 'fa' ? 'ادامه خرید' : 'Continue Shopping');
    $startShopping = $locale === 'ps' ? 'د خپلې خوښې افغان جامې ومومئ.' : ($locale === 'fa' ? 'لباس افغانی مورد علاقه‌تان را پیدا کنید.' : 'Find authentic Afghan pieces you love.');
    $shippingNote = $locale === 'ps' ? 'د لېږد لګښت په چک‌اوت کې محاسبه کېږي.' : ($locale === 'fa' ? 'هزینه ارسال در مرحله پرداخت محاسبه می‌شود.' : 'Shipping is calculated at checkout.');
    $subtotalMinor = $cart->items->sum(fn ($item) => $item->lineTotalMinor());
    $moneyProduct = $cart->items->first()?->product;
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('shop', ['locale' => $locale]) }}" class="mb-4 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $continueLabel }}
        </a>

        <h1 class="mb-8 text-3xl font-bold text-gray-900 md:text-4xl">{{ __('commerce.cart') }}</h1>

        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        @if($cart->items->isEmpty())
            <div class="py-20 text-center">
                <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-full bg-gray-100 text-gray-400">
                    <x-icon name="bag" class="h-12 w-12" />
                </div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('commerce.empty_cart') }}</h2>
                <p class="mt-2 text-gray-500">{{ $startShopping }}</p>
                <a href="{{ route('shop', ['locale' => $locale]) }}" class="base44-gradient-cta mt-8 inline-flex items-center gap-2 rounded-full px-6 py-3 font-semibold transition">
                    {{ $continueLabel }}
                    <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                </a>
            </div>
        @else
            <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    @foreach($cart->items as $item)
                        @php
                            $productTranslation = $item->product->translation();
                            $media = $item->product->primaryMedia;
                            $available = $item->variant?->availableQuantity() ?? $item->product->availableStock();
                        @endphp
                        <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex gap-4 p-4 sm:gap-6 sm:p-5">
                                <a href="{{ route('products.show', ['locale' => $locale, 'slug' => $productTranslation?->slug]) }}" class="h-28 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-36 sm:w-28">
                                    @if($media)
                                        <x-responsive-product-image :media="$media" :alt="$media->translation()?->alt_text" class="h-full w-full object-cover" />
                                    @else
                                        <span class="grid h-full w-full place-items-center font-bold text-[#2A6867]/30">KF</span>
                                    @endif
                                </a>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <a href="{{ route('products.show', ['locale' => $locale, 'slug' => $productTranslation?->slug]) }}" class="font-semibold text-gray-900 transition hover:text-[#881C27]">{{ $productTranslation?->name }}</a>
                                            @if($item->variant)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->variant->option_key }}</p>
                                            @endif
                                            @if($item->tailoringRequest)
                                                <span class="mt-2 inline-flex rounded-full bg-[#881C27]/10 px-2.5 py-1 text-xs font-semibold text-[#881C27]">{{ __('measurements.tailored') }}</span>
                                                <p class="mt-1 text-xs text-gray-500">{{ __('measurements.measurement_profile') }}: {{ $item->tailoringRequest->measurementProfile?->name }}</p>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('cart.items.destroy', ['locale' => $locale, 'item' => $item]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="grid h-10 w-10 place-items-center rounded-full text-gray-400 transition hover:bg-red-50 hover:text-red-600" aria-label="{{ __('commerce.remove') }}">
                                                <x-icon name="trash" class="h-5 w-5" />
                                            </button>
                                        </form>
                                    </div>

                                    <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
                                        @unless($item->tailoringRequest)
                                            <form
                                                method="POST"
                                                action="{{ route('cart.items.update', ['locale' => $locale, 'item' => $item]) }}"
                                                class="flex items-center overflow-hidden rounded-lg border border-gray-200"
                                                x-data="{ quantity: {{ $item->quantity }}, max: {{ max(1, $available) }} }"
                                            >
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="quantity" :value="quantity">
                                                <button type="button" class="grid h-10 w-10 place-items-center text-gray-600 disabled:opacity-30" @click="quantity = Math.max(1, quantity - 1); $nextTick(() => $el.closest('form').requestSubmit())" :disabled="quantity <= 1"><x-icon name="minus" class="h-4 w-4" /></button>
                                                <span class="w-10 text-center text-sm font-semibold" x-text="quantity"></span>
                                                <button type="button" class="grid h-10 w-10 place-items-center text-gray-600 disabled:opacity-30" @click="quantity = Math.min(max, quantity + 1); $nextTick(() => $el.closest('form').requestSubmit())" :disabled="quantity >= max"><x-icon name="plus" class="h-4 w-4" /></button>
                                            </form>
                                        @else
                                            <span class="rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-600">{{ __('commerce.quantity') }}: 1</span>
                                        @endunless

                                        <strong class="text-lg text-[#D91E36]">{{ $item->product->formattedPrice($item->lineTotalMinor()) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside>
                    <div class="sticky top-36 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900">{{ __('commerce.order') }}</h2>

                            <div class="mt-6 space-y-4 text-sm">
                                <div class="flex justify-between gap-4 text-gray-600">
                                    <span>{{ __('commerce.subtotal') }}</span>
                                    <strong class="text-gray-900">{{ $moneyProduct?->formattedPrice($subtotalMinor) }}</strong>
                                </div>
                                <div class="flex justify-between gap-4 text-gray-600">
                                    <span>{{ $locale === 'ps' ? 'لېږد' : ($locale === 'fa' ? 'ارسال' : 'Shipping') }}</span>
                                    <span>{{ $locale === 'ps' ? 'په چک‌اوت کې' : ($locale === 'fa' ? 'در پرداخت' : 'At checkout') }}</span>
                                </div>
                            </div>

                            <div class="my-6 border-t border-gray-100"></div>

                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold">{{ __('commerce.total') }}</span>
                                <strong class="text-2xl text-[#D91E36]">{{ $moneyProduct?->formattedPrice($subtotalMinor) }}</strong>
                            </div>
                            <p class="mt-2 text-xs leading-5 text-gray-500">{{ $shippingNote }}</p>

                            <a href="{{ route('checkout', ['locale' => $locale]) }}" class="base44-gradient-cta mt-6 flex w-full items-center justify-center gap-2 rounded-xl px-5 py-4 font-semibold transition">
                                {{ __('commerce.continue_checkout') }}
                                <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                            </a>
                        </div>

                        <div class="grid grid-cols-3 border-t border-gray-100 bg-gray-50">
                            @foreach([
                                ['globe', __('site.global_shipping')],
                                ['shield', __('site.quality_assured')],
                                ['ruler', __('site.custom_sizing')],
                            ] as [$iconName, $label])
                                <div class="p-4 text-center">
                                    <x-icon :name="$iconName" class="mx-auto h-5 w-5 text-[#881C27]" />
                                    <p class="mt-2 text-[11px] font-medium text-gray-600">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>
@endsection
