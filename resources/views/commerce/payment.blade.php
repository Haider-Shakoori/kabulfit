@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $labels = [
        'shipping' => $locale === 'ps' ? 'د لېږد ادرس' : ($locale === 'fa' ? 'آدرس ارسال' : 'Shipping Address'),
        'review' => $locale === 'ps' ? 'بیاکتنه' : ($locale === 'fa' ? 'بازبینی' : 'Review'),
        'payment' => $locale === 'ps' ? 'د تادیې طریقه' : ($locale === 'fa' ? 'روش پرداخت' : 'Payment Method'),
        'card' => __('commerce.pay_with_card'),
        'paypal' => __('commerce.pay_with_paypal'),
        'protected' => $locale === 'ps' ? 'ستاسو تادیه په خوندي ډول پروسس کېږي.' : ($locale === 'fa' ? 'پرداخت شما به‌صورت امن پردازش می‌شود.' : 'Your payment is processed securely.'),
        'summary' => $locale === 'ps' ? 'د فرمایش لنډیز' : ($locale === 'fa' ? 'خلاصه سفارش' : 'Order Summary'),
        'back_orders' => $locale === 'ps' ? 'فرمایشونه وګورئ' : ($locale === 'fa' ? 'مشاهده سفارش‌ها' : 'View Orders'),
        'continue' => $locale === 'ps' ? 'خرید ته دوام ورکړئ' : ($locale === 'fa' ? 'ادامه خرید' : 'Continue Shopping'),
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <div class="mb-10 flex items-center justify-center">
            @foreach([
                [1, $labels['shipping']],
                [2, $labels['review']],
                [3, $labels['payment']],
            ] as [$number, $label])
                <div class="flex items-center">
                    <div class="flex items-center">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] text-sm font-bold text-white">
                            {{ $number < 3 ? '✓' : '3' }}
                        </span>
                        <span class="ms-2 hidden text-sm font-medium text-gray-900 sm:block">{{ $label }}</span>
                    </div>
                    @unless($loop->last)
                        <span class="mx-3 h-1 w-10 rounded bg-gradient-to-r from-[#881C27] to-[#2A6867] sm:mx-4 sm:w-20"></span>
                    @endunless
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
            <main class="lg:col-span-2">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5 sm:p-6">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]">
                                <x-icon name="shield" class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#2A6867]">{{ __('commerce.secure_payment') }}</p>
                                <h1 class="text-xl font-semibold text-gray-900">{{ $paymentMethod === 'paypal' ? $labels['paypal'] : $labels['card'] }}</h1>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="mb-6 rounded-xl bg-gray-50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">{{ __('commerce.order') }}</p>
                                    <p class="font-semibold text-gray-900">{{ $order->number }}</p>
                                </div>
                                <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium capitalize text-amber-700">{{ str($order->payment_status)->replace('_', ' ') }}</span>
                            </div>
                        </div>

                        @if($paymentMethod === 'stripe' && $clientSecret && $stripeKey)
                            <form id="stripe-payment-form" class="space-y-5">
                                <div id="stripe-payment-element" class="min-h-20"></div>
                                <p id="stripe-payment-message" role="alert" class="text-sm text-red-600"></p>
                                <button class="base44-gradient-cta flex min-h-14 w-full items-center justify-center gap-2 rounded-xl px-5 py-3 text-lg font-semibold transition disabled:cursor-not-allowed disabled:opacity-50" id="stripe-submit" type="submit">
                                    <x-icon name="shield" class="h-5 w-5" />
                                    {{ __('commerce.pay_now') }}
                                </button>
                            </form>

                            <script src="https://js.stripe.com/v3/"></script>
                            <script>
                                (() => {
                                    const stripe = Stripe(@json($stripeKey));
                                    const elements = stripe.elements({
                                        clientSecret: @json($clientSecret),
                                        appearance: {
                                            theme: 'stripe',
                                            variables: {
                                                colorPrimary: '#881C27',
                                                borderRadius: '12px',
                                            },
                                        },
                                    });
                                    const paymentElement = elements.create('payment');
                                    paymentElement.mount('#stripe-payment-element');
                                    const form = document.getElementById('stripe-payment-form');
                                    const message = document.getElementById('stripe-payment-message');

                                    form.addEventListener('submit', async (event) => {
                                        event.preventDefault();
                                        const button = document.getElementById('stripe-submit');
                                        button.disabled = true;

                                        const result = await stripe.confirmPayment({
                                            elements,
                                            confirmParams: {
                                                return_url: @json(route('orders.payment', ['locale' => app()->getLocale(), 'order' => $order])),
                                            },
                                        });

                                        if (result.error) {
                                            message.textContent = result.error.message || @json(__('commerce.payment_error'));
                                            button.disabled = false;
                                        }
                                    });
                                })();
                            </script>
                        @elseif($paymentMethod === 'paypal' && $paypalClientId)
                            <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                                <p class="text-sm text-gray-600">{{ __('commerce.paypal_description') }}</p>
                                <div id="paypal-button-container" class="mt-4"></div>
                                <p id="paypal-payment-message" role="alert" class="mt-3 text-sm text-red-600"></p>
                                <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500">
                                    <x-icon name="shield" class="h-4 w-4 text-[#0070ba]" />
                                    {{ __('commerce.secured_by_paypal') }}
                                </div>
                            </div>

                            <script src="https://www.paypal.com/sdk/js?client-id={{ urlencode($paypalClientId) }}&currency={{ urlencode(strtoupper($order->currency)) }}&components=buttons"></script>
                            <script>
                                (() => {
                                    const message = document.getElementById('paypal-payment-message');
                                    const csrf = @json(csrf_token());
                                    const createUrl = @json(route('paypal.create', ['locale' => $locale, 'order' => $order]));
                                    const captureUrl = @json(route('paypal.capture', ['locale' => $locale, 'order' => $order]));
                                    const contentIds = @json($order->items->pluck('sku')->values());
                                    const numItems = {{ (int) $order->items->sum('quantity') }};
                                    const value = {{ number_format($order->total_minor / 100, 2, '.', '') }};
                                    const currency = @json($order->currency);
                                    const orderNumber = @json($order->number);
                                    const purchaseKey = 'kabulfit-purchase-' + @json($order->uuid);

                                    if (!window.paypal) {
                                        message.textContent = @json(__('commerce.paypal_error'));
                                        return;
                                    }

                                    window.paypal.Buttons({
                                        style: { layout: 'vertical', shape: 'rect' },
                                        createOrder: async () => {
                                            message.textContent = '';
                                            const response = await fetch(createUrl, {
                                                method: 'POST',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': csrf,
                                                },
                                                body: JSON.stringify({}),
                                            });
                                            const payload = await response.json();
                                            if (!response.ok || !payload.id) {
                                                throw new Error(payload.message || @json(__('commerce.paypal_error')));
                                            }
                                            return payload.id;
                                        },
                                        onApprove: async (data) => {
                                            message.textContent = '';
                                            const response = await fetch(captureUrl, {
                                                method: 'POST',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': csrf,
                                                },
                                                body: JSON.stringify({ paypal_order_id: data.orderID }),
                                            });
                                            const payload = await response.json();
                                            if (!response.ok || !payload.success) {
                                                throw new Error(payload.message || @json(__('commerce.paypal_capture_error')));
                                            }

                                            if (!window.localStorage?.getItem(purchaseKey)) {
                                                window.kabulFitTrack?.('Purchase', {
                                                    content_ids: contentIds,
                                                    content_type: 'product',
                                                    num_items: numItems,
                                                    value,
                                                    currency,
                                                    order_id: orderNumber,
                                                });
                                                window.localStorage?.setItem(purchaseKey, '1');
                                            }

                                            window.location.assign(payload.redirect);
                                        },
                                        onError: (error) => {
                                            message.textContent = error?.message || @json(__('commerce.paypal_error'));
                                        },
                                        onCancel: () => {
                                            message.textContent = @json(__('commerce.payment_pending'));
                                        },
                                    }).render('#paypal-button-container');
                                })();
                            </script>
                        @else
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-800">
                                <p class="font-semibold">{{ __('commerce.payment_pending') }}</p>
                                <p class="mt-2 text-sm">{{ __('commerce.payment_status') }}: <span class="capitalize">{{ str($order->payment_status)->replace('_', ' ') }}</span></p>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('orders.show', ['locale' => $locale, 'order' => $order->uuid]) }}" class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">{{ $labels['back_orders'] }}</a>
                                <a href="{{ route('shop', ['locale' => $locale]) }}" class="rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ $labels['continue'] }}</a>
                            </div>
                        @endif

                        <div class="mt-6 flex items-center justify-center gap-2 border-t border-gray-100 pt-5 text-xs text-gray-500">
                            <x-icon name="shield" class="h-4 w-4 text-[#2A6867]" />
                            {{ $labels['protected'] }}
                        </div>
                    </div>
                </div>
            </main>

            <aside>
                <div class="sticky top-36 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="p-5 sm:p-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $labels['summary'] }}</h2>

                        <div class="mt-6 space-y-4 text-sm">
                            <div class="flex justify-between gap-4 text-gray-600">
                                <span>{{ __('commerce.subtotal') }}</span>
                                <strong class="text-gray-900">{{ number_format($order->subtotal_minor / 100, 2) }} {{ $order->currency }}</strong>
                            </div>
                            @if($order->discount_minor > 0)
                                <div class="flex justify-between gap-4 text-emerald-700">
                                    <span>{{ __('commerce.coupon') }}@if($order->coupon_code) · {{ $order->coupon_code }}@endif</span>
                                    <strong>-{{ number_format($order->discount_minor / 100, 2) }} {{ $order->currency }}</strong>
                                </div>
                            @endif
                            <div class="flex justify-between gap-4 text-gray-600">
                                <span>{{ $locale === 'ps' ? 'لېږد' : ($locale === 'fa' ? 'ارسال' : 'Shipping') }}</span>
                                <strong class="text-gray-900">{{ number_format($order->shipping_minor / 100, 2) }} {{ $order->currency }}</strong>
                            </div>
                        </div>

                        <div class="my-5 border-t border-gray-100"></div>

                        <div class="flex items-center justify-between gap-4">
                            <span class="text-lg font-semibold text-gray-900">{{ __('commerce.total') }}</span>
                            <strong class="text-2xl text-[#881C27]">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 border-t border-gray-100 bg-gray-50">
                        <div class="p-4 text-center">
                            <x-icon name="shield" class="mx-auto h-5 w-5 text-[#881C27]" />
                            <p class="mt-2 text-[11px] font-medium text-gray-600">{{ __('commerce.secure_payment') }}</p>
                        </div>
                        <div class="p-4 text-center">
                            <x-icon name="quality" class="mx-auto h-5 w-5 text-[#2A6867]" />
                            <p class="mt-2 text-[11px] font-medium text-gray-600">{{ __('site.quality_assured') }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@if($order->payment_status === 'succeeded')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const storageKey = 'kabulfit-purchase-' + @json($order->uuid);
        if (window.localStorage && window.localStorage.getItem(storageKey)) return;

        window.kabulFitTrack?.('Purchase', {
            content_ids: @json($order->items()->pluck('sku')->values()),
            content_type: 'product',
            num_items: {{ (int) $order->items()->sum('quantity') }},
            value: {{ number_format($order->total_minor / 100, 2, '.', '') }},
            currency: @json($order->currency),
            order_id: @json($order->number)
        });

        window.localStorage?.setItem(storageKey, '1');
    }, { once: true });
</script>
@endif
@endsection
