@extends('layouts.app')

@section('content')
<section class="section">
    <div class="container auth-shell">
        <div class="auth-card">
            <p class="eyebrow">{{ __('commerce.secure_payment') }}</p>
            <h1>{{ __('commerce.order') }} {{ $order->number }}</h1>
            <p>{{ __('commerce.payment_pending') }}</p>
            <p><strong>{{ __('commerce.total') }}: {{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</strong></p>
            <p>{{ __('commerce.payment_status') }}: {{ $order->payment_status }}</p>

            @if ($clientSecret && $stripeKey)
                <form id="stripe-payment-form">
                    <div id="stripe-payment-element"></div>
                    <p id="stripe-payment-message" role="alert"></p>
                    <button class="button button-primary" id="stripe-submit" type="submit">{{ __('commerce.pay_now') }}</button>
                </form>
                <script src="https://js.stripe.com/v3/"></script>
                <script>
                    (() => {
                        const stripe = Stripe(@json($stripeKey));
                        const elements = stripe.elements({ clientSecret: @json($clientSecret) });
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
                                confirmParams: { return_url: @json(route('orders.payment', ['locale' => app()->getLocale(), 'order' => $order])) },
                            });
                            if (result.error) {
                                message.textContent = result.error.message || @json(__('commerce.payment_error'));
                                button.disabled = false;
                            }
                        });
                    })();
                </script>
            @endif
        </div>
    </div>
</section>
@endsection
