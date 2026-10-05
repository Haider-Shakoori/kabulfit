@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $subtotalMinor = $cart->items->sum(fn ($item) => $item->lineTotalMinor());
    $currency = $cart->currency;
    $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
    $defaultShipping = $shippingMethods->first();

    $addressData = $addresses->map(fn ($address) => [
        'uuid' => $address->uuid,
        'label' => $address->label ?: __('account.address'),
        'recipient_name' => $address->recipient_name,
        'phone' => $address->phone,
        'address_line1' => $address->address_line1,
        'address_line2' => $address->address_line2,
        'city' => $address->city,
        'province' => $address->province,
        'postal_code' => $address->postal_code,
        'country_code' => $address->country_code,
        'is_default' => $address->is_default,
    ])->values()->all();

    $shippingData = $shippingMethods->map(fn ($method) => [
        'code' => $method->code,
        'name' => $method->name,
        'price_minor' => $method->price_minor,
        'currency' => $method->currency,
    ])->values()->all();

    $labels = [
        'back' => $locale === 'ps' ? 'بېرته ټوکرۍ ته' : ($locale === 'fa' ? 'بازگشت به سبد خرید' : 'Back to Cart'),
        'review' => $locale === 'ps' ? 'بیاکتنه' : ($locale === 'fa' ? 'بازبینی' : 'Review'),
        'saved_addresses' => $locale === 'ps' ? 'خوندي ادرسونه' : ($locale === 'fa' ? 'آدرس‌های ذخیره‌شده' : 'Saved Addresses'),
        'new_address' => $locale === 'ps' ? 'نوی ادرس' : ($locale === 'fa' ? 'آدرس جدید' : 'New Address'),
        'continue_review' => $locale === 'ps' ? 'بیاکتنې ته دوام' : ($locale === 'fa' ? 'ادامه به بازبینی' : 'Continue to Review'),
        'change' => $locale === 'ps' ? 'بدلول' : ($locale === 'fa' ? 'تغییر' : 'Change'),
        'order_summary' => $locale === 'ps' ? 'د فرمایش لنډیز' : ($locale === 'fa' ? 'خلاصه سفارش' : 'Order Summary'),
        'shipping' => $locale === 'ps' ? 'لېږد' : ($locale === 'fa' ? 'ارسال' : 'Shipping'),
        'terms' => $locale === 'ps' ? 'زه د کارولو شرایط او د محرمیت پالیسي منم.' : ($locale === 'fa' ? 'شرایط استفاده و سیاست حریم خصوصی را می‌پذیرم.' : 'I agree to the Terms & Conditions and Privacy Policy.'),
        'secure' => $locale === 'ps' ? 'خوندي تادیه' : ($locale === 'fa' ? 'پرداخت امن' : 'Secure payment'),
        'select_address' => $locale === 'ps' ? 'مهرباني وکړئ د لېږد ادرس وټاکئ.' : ($locale === 'fa' ? 'لطفاً آدرس ارسال را انتخاب کنید.' : 'Please select a shipping address.'),
        'select_shipping' => $locale === 'ps' ? 'د لېږد طریقه وټاکئ' : ($locale === 'fa' ? 'روش ارسال را انتخاب کنید' : 'Choose a shipping method'),
        'promo' => $locale === 'ps' ? 'د تخفیف کوډ' : ($locale === 'fa' ? 'کد تخفیف' : 'Promo code'),
        'place' => __('commerce.place_order'),
    ];
@endphp

<div
    class="min-h-screen bg-[#FDFBF7]"
    x-data='{
        step: 1,
        addAddress: false,
        terms: false,
        paymentMethod: @js(old('payment_method', 'stripe')),
        addressUuid: @js($defaultAddress?->uuid ?? ""),
        shippingCode: @js($defaultShipping?->code ?? ""),
        addresses: @js($addressData),
        shippingMethods: @js($shippingData),
        subtotalMinor: {{ $subtotalMinor }},
        currency: @js($currency),
        get selectedAddress() {
            return this.addresses.find(a => a.uuid === this.addressUuid) || null;
        },
        get selectedShipping() {
            return this.shippingMethods.find(s => s.code === this.shippingCode) || null;
        },
        money(minor) {
            return (minor / 100).toFixed(2) + " " + this.currency;
        },
        totalMinor() {
            return this.subtotalMinor + (this.selectedShipping?.price_minor || 0);
        }
    }'
>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('cart', ['locale' => $locale]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $labels['back'] }}
        </a>

        @if($cart->items->isEmpty())
            <div class="py-20 text-center">
                <x-icon name="bag" class="mx-auto h-16 w-16 text-gray-300" />
                <h1 class="mt-5 text-2xl font-bold text-gray-900">{{ __('commerce.empty_cart') }}</h1>
                <a href="{{ route('shop', ['locale' => $locale]) }}" class="mt-6 inline-flex rounded-xl bg-[#881C27] px-5 py-3 font-semibold text-white">{{ __('site.shop_now') }}</a>
            </div>
        @else
            <div class="mb-10 flex items-center justify-center">
                @foreach([
                    [1, __('commerce.shipping_address'), '⌖'],
                    [2, $labels['review'], '✓'],
                    [3, __('commerce.payment'), '▣'],
                ] as [$number, $label, $icon])
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <span class="grid h-10 w-10 place-items-center rounded-full text-sm font-bold transition"
                                  :class="step >= {{ $number }} ? 'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step <= {{ $number }}">{{ $number }}</span>
                                <span x-show="step > {{ $number }}">✓</span>
                            </span>
                            <span class="ms-2 hidden text-sm sm:block" :class="step >= {{ $number }} ? 'font-medium text-gray-900' : 'text-gray-500'">{{ $label }}</span>
                        </div>
                        @unless($loop->last)
                            <span class="mx-3 h-1 w-10 rounded sm:mx-4 sm:w-20" :class="step > {{ $number }} ? 'bg-gradient-to-r from-[#881C27] to-[#2A6867]' : 'bg-gray-200'"></span>
                        @endunless
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('checkout.place', ['locale' => $locale]) }}">
                @csrf
                <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <section x-show="step === 1" x-transition>
                            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                                <div class="border-b border-gray-100 p-5 sm:p-6">
                                    <h1 class="flex items-center gap-2 text-xl font-semibold text-gray-900">
                                        <span class="text-[#881C27]">⌖</span>
                                        {{ __('commerce.shipping_address') }}
                                    </h1>
                                </div>

                                <div class="space-y-6 p-5 sm:p-6">
                                    @if($addresses->isNotEmpty())
                                        <div>
                                            <p class="mb-3 text-sm font-semibold text-gray-700">{{ $labels['saved_addresses'] }}</p>
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                @foreach($addresses as $address)
                                                    <label class="relative cursor-pointer">
                                                        <input type="radio" name="address_uuid" value="{{ $address->uuid }}" x-model="addressUuid" class="peer sr-only" required>
                                                        <span class="flex min-h-36 flex-col rounded-xl border-2 border-gray-200 p-4 transition peer-checked:border-[#881C27] peer-checked:bg-[#881C27]/5">
                                                            <span class="flex items-center gap-2">
                                                                <strong class="text-gray-900">{{ $address->label ?: __('account.address') }}</strong>
                                                                @if($address->is_default)
                                                                    <span class="rounded-full bg-[#881C27] px-2 py-0.5 text-[10px] font-semibold text-white">{{ __('account.default') }}</span>
                                                                @endif
                                                            </span>
                                                            <span class="mt-2 text-sm text-gray-600">{{ $address->recipient_name }}</span>
                                                            <span class="text-sm text-gray-600">{{ $address->address_line1 }}</span>
                                                            <span class="text-sm text-gray-600">{{ $address->city }}@if($address->province), {{ $address->province }}@endif · {{ $address->country_code }}</span>
                                                        </span>
                                                    </label>
                                                @endforeach

                                                <button type="button" @click="addAddress = true" class="flex min-h-36 flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 p-4 text-gray-500 transition hover:border-[#881C27] hover:bg-[#881C27]/5 hover:text-[#881C27]">
                                                    <span class="text-2xl">⌖</span>
                                                    <span class="mt-2 text-sm font-medium">{{ $labels['new_address'] }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                                            <p class="text-gray-600">{{ __('account.no_addresses') }}</p>
                                            <button type="button" @click="addAddress = true" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">
                                                <x-icon name="plus" class="h-4 w-4" />
                                                {{ $labels['new_address'] }}
                                            </button>
                                        </div>
                                    @endif

                                    <div class="flex justify-end">
                                        <button type="button"
                                                @click="if (addressUuid) step = 2"
                                                :disabled="!addressUuid"
                                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-40">
                                            {{ $labels['continue_review'] }}
                                            <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section x-show="step === 2" x-cloak x-transition class="space-y-6">
                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900"><span class="text-[#881C27]">⌖</span>{{ __('commerce.shipping_address') }}</h2>
                                        <template x-if="selectedAddress">
                                            <div class="mt-3 text-sm leading-6 text-gray-600">
                                                <p class="font-medium text-gray-900" x-text="selectedAddress.recipient_name"></p>
                                                <p x-text="selectedAddress.address_line1"></p>
                                                <p><span x-text="selectedAddress.city"></span><span x-show="selectedAddress.province">, <span x-text="selectedAddress.province"></span></span></p>
                                                <p x-text="selectedAddress.phone"></p>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="step = 1" class="rounded-lg px-3 py-2 text-sm font-semibold text-[#881C27] hover:bg-[#881C27]/5">{{ $labels['change'] }}</button>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                                    <x-icon name="truck" class="h-5 w-5 text-[#881C27]" />
                                    {{ $labels['select_shipping'] }}
                                </h2>
                                <div class="mt-4 grid gap-3">
                                    @foreach($shippingMethods as $method)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="shipping_method" value="{{ $method->code }}" x-model="shippingCode" required class="peer sr-only">
                                            <span class="flex items-center justify-between gap-4 rounded-xl border-2 border-gray-200 p-4 transition peer-checked:border-[#881C27] peer-checked:bg-[#881C27]/5">
                                                <span>
                                                    <strong class="block text-gray-900">{{ $method->name }}</strong>
                                                    <span class="text-xs text-gray-500">{{ $method->code }}</span>
                                                </span>
                                                <strong class="text-[#881C27]">{{ number_format($method->price_minor / 100, 2) }} {{ $method->currency }}</strong>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                                <label class="grid gap-2">
                                    <span class="text-sm font-semibold text-gray-700">{{ $labels['promo'] }}</span>
                                    <input name="coupon" value="{{ old('coupon') }}" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10" placeholder="{{ __('commerce.coupon') }}">
                                </label>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                                <h2 class="text-lg font-semibold text-gray-900">{{ __('commerce.payment_method') }}</h2>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="stripe" x-model="paymentMethod" class="peer sr-only" required>
                                        <span class="flex min-h-24 items-center gap-3 rounded-xl border-2 border-gray-200 p-4 transition peer-checked:border-[#881C27] peer-checked:bg-[#881C27]/5">
                                            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]"><x-icon name="shield" class="h-5 w-5" /></span>
                                            <span><strong class="block text-sm text-gray-900">{{ __('commerce.pay_with_card') }}</strong><span class="mt-1 block text-xs text-gray-500">Stripe</span></span>
                                        </span>
                                    </label>

                                    @if(config('services.paypal.client_id') && config('services.paypal.secret'))
                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="paypal" x-model="paymentMethod" class="peer sr-only" required>
                                            <span class="flex min-h-24 items-center gap-3 rounded-xl border-2 border-gray-200 p-4 transition peer-checked:border-[#0070ba] peer-checked:bg-blue-50">
                                                <span class="grid h-10 w-10 place-items-center rounded-full bg-blue-100 font-bold text-[#0070ba]">P</span>
                                                <span><strong class="block text-sm text-gray-900">{{ __('commerce.pay_with_paypal') }}</strong><span class="mt-1 block text-xs text-gray-500">{{ __('commerce.paypal_description') }}</span></span>
                                            </span>
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input type="checkbox" x-model="terms" class="mt-1 h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]">
                                    <span class="text-sm leading-6 text-gray-600">{{ $labels['terms'] }}</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <button type="button" @click="step = 1" class="rounded-xl border-2 border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700">{{ $labels['back'] }}</button>
                                <button type="submit"
                                        :disabled="!addressUuid || !shippingCode || !terms"
                                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-40">
                                    <x-icon name="shield" class="h-5 w-5" />
                                    {{ $labels['place'] }}
                                </button>
                            </div>
                        </section>
                    </div>

                    <aside>
                        <div class="sticky top-36 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="p-5 sm:p-6">
                                <h2 class="text-xl font-semibold text-gray-900">{{ $labels['order_summary'] }}</h2>

                                <div class="mt-5 max-h-72 space-y-4 overflow-y-auto pe-1">
                                    @foreach($cart->items as $item)
                                        @php($media = $item->product->primaryMedia)
                                        <div class="flex gap-3">
                                            <div class="h-16 w-14 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                                @if($media)
                                                    <x-responsive-product-image :media="$media" :alt="$media->translation()?->alt_text" class="h-full w-full object-cover" />
                                                @else
                                                    <span class="grid h-full w-full place-items-center text-xs font-bold text-[#2A6867]/40">KF</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="line-clamp-1 text-sm font-medium text-gray-900">{{ $item->product->translation()?->name }}</p>
                                                <p class="mt-1 text-xs text-gray-500">{{ __('commerce.quantity') }}: {{ $item->quantity }}</p>
                                            </div>
                                            <strong class="text-sm text-gray-900">{{ $item->product->formattedPrice($item->lineTotalMinor()) }}</strong>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="my-5 border-t border-gray-100"></div>

                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between gap-4 text-gray-600"><span>{{ __('commerce.subtotal') }}</span><strong class="text-gray-900">{{ number_format($subtotalMinor / 100, 2) }} {{ $currency }}</strong></div>
                                    <div class="flex justify-between gap-4 text-gray-600"><span>{{ $labels['shipping'] }}</span><strong class="text-gray-900" x-text="selectedShipping ? money(selectedShipping.price_minor) : '—'"></strong></div>
                                </div>

                                <div class="my-5 border-t border-gray-100"></div>

                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-lg font-semibold text-gray-900">{{ __('commerce.total') }}</span>
                                    <strong class="text-2xl text-[#881C27]" x-text="money(totalMinor())"></strong>
                                </div>
                            </div>

                            <div class="flex items-center justify-center gap-2 border-t border-gray-100 bg-gray-50 px-4 py-4 text-xs font-medium text-gray-500">
                                <x-icon name="shield" class="h-4 w-4 text-[#2A6867]" />
                                {{ $labels['secure'] }}
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        @endif
    </div>

    <div x-show="addAddress" x-cloak class="fixed inset-0 z-[100] grid place-items-center p-4">
        <button type="button" class="absolute inset-0 bg-black/50" @click="addAddress = false" aria-label="Close"></button>
        <div x-transition class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">{{ $labels['new_address'] }}</h2>
                <button type="button" @click="addAddress = false" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100"><x-icon name="close" class="h-5 w-5" /></button>
            </div>
            <form method="POST" action="{{ route('addresses.store', ['locale' => $locale]) }}">
                @csrf
                @include('account.partials.base44-address-fields', ['address' => null])
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="addAddress = false" class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">{{ $locale === 'ps' ? 'لغوه' : ($locale === 'fa' ? 'لغو' : 'Cancel') }}</button>
                    <button type="submit" class="rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ __('account.save_address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.kabulFitTrack?.('InitiateCheckout', {
            content_ids: @json($cart->items->map(fn ($item) => $item->product->sku)->values()),
            content_type: 'product',
            num_items: {{ (int) $cart->items->sum('quantity') }},
            value: {{ number_format($subtotalMinor / 100, 2, '.', '') }},
            currency: @json($currency)
        });
    }, { once: true });
</script>
@endsection
