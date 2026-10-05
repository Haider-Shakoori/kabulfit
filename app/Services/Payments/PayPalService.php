<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Services\Commerce\CheckoutService;
use App\Services\Orders\OrderLifecycleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PayPalService
{
    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly OrderLifecycleService $orders,
    ) {}

    public function prepare(Order $order): Payment
    {
        return $order->payment()->firstOrCreate([], [
            'uuid' => (string) Str::uuid(),
            'provider' => 'paypal',
            'status' => 'pending',
            'currency' => $order->currency,
            'amount_minor' => $order->total_minor,
            'idempotency_key' => 'paypal-'.$order->uuid,
        ]);
    }

    public function createOrder(Order $order): array
    {
        $payment = $this->prepare($order);

        if ($payment->provider !== 'paypal') {
            throw new RuntimeException('This order is not configured for PayPal.');
        }

        $response = $this->request()
            ->withHeaders(['PayPal-Request-Id' => $payment->idempotency_key])
            ->post($this->baseUrl().'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->uuid,
                    'description' => 'KabulFit order '.$order->number,
                    'amount' => [
                        'currency_code' => strtoupper($order->currency),
                        'value' => number_format($order->total_minor / 100, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'brand_name' => 'KabulFit',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal could not create the order.');
        }

        $payload = $response->json();
        $paypalOrderId = (string) ($payload['id'] ?? '');

        if ($paypalOrderId === '') {
            throw new RuntimeException('PayPal did not return an order ID.');
        }

        $payment->update([
            'provider_payment_id' => $paypalOrderId,
            'status' => 'processing',
        ]);

        PaymentEvent::query()->create([
            'payment_id' => $payment->id,
            'provider' => 'paypal',
            'provider_event_id' => 'paypal-order:'.$paypalOrderId,
            'type' => 'checkout.order.created',
            'payload' => $payload,
            'processed_at' => now(),
        ]);

        return ['id' => $paypalOrderId, 'status' => $payload['status'] ?? 'CREATED'];
    }

    public function capture(Order $order, string $paypalOrderId): Payment
    {
        $payment = $order->payment()->where('provider', 'paypal')->firstOrFail();

        if ($payment->status === 'succeeded') {
            return $payment;
        }

        if ($payment->provider_payment_id !== $paypalOrderId) {
            throw new RuntimeException('PayPal order mismatch.');
        }

        $response = $this->request()
            ->withHeaders(['PayPal-Request-Id' => 'capture-'.$payment->uuid])
            ->post($this->baseUrl().'/v2/checkout/orders/'.$paypalOrderId.'/capture');

        if (! $response->successful()) {
            throw new RuntimeException('PayPal payment capture failed.');
        }

        $payload = $response->json();
        $capture = data_get($payload, 'purchase_units.0.payments.captures.0');

        if (! is_array($capture) || ($capture['status'] ?? null) !== 'COMPLETED') {
            throw new RuntimeException('PayPal did not complete the payment.');
        }

        $captureAmount = (string) data_get($capture, 'amount.value');
        $captureCurrency = strtoupper((string) data_get($capture, 'amount.currency_code'));
        $expectedAmount = number_format($order->total_minor / 100, 2, '.', '');

        if ($captureAmount !== $expectedAmount || $captureCurrency !== strtoupper($order->currency)) {
            throw new RuntimeException('PayPal captured amount does not match the order.');
        }

        $captureId = (string) ($capture['id'] ?? '');

        return DB::transaction(function () use ($order, $payment, $payload, $captureId, $paypalOrderId): Payment {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === 'succeeded') {
                return $locked;
            }

            $locked->update([
                'provider_payment_id' => $captureId ?: $paypalOrderId,
                'status' => 'succeeded',
                'failure_message' => null,
            ]);

            PaymentEvent::query()->firstOrCreate(
                ['provider_event_id' => 'paypal-capture:'.($captureId ?: $paypalOrderId)],
                [
                    'payment_id' => $locked->id,
                    'provider' => 'paypal',
                    'type' => 'payment.capture.completed',
                    'payload' => $payload,
                    'processed_at' => now(),
                ],
            );

            $locked->order->update([
                'payment_status' => 'succeeded',
                'paid_at' => now(),
            ]);

            if ($locked->order->status !== 'paid') {
                $this->orders->transition($locked->order, 'paid', 'paypal');
            }

            $this->checkout->captureReservations($locked->order->load('items'));

            return $locked->fresh('order');
        }, 3);
    }

    public function refund(Payment $payment, ?int $amountMinor = null): array
    {
        if (! $payment->provider_payment_id) {
            throw new RuntimeException('PayPal capture ID is missing.');
        }

        $body = [];
        if ($amountMinor !== null) {
            $body['amount'] = [
                'value' => number_format($amountMinor / 100, 2, '.', ''),
                'currency_code' => strtoupper($payment->currency),
            ];
        }

        $response = $this->request()
            ->withHeaders(['PayPal-Request-Id' => 'refund-'.$payment->uuid.'-'.($amountMinor ?? 'full')])
            ->post($this->baseUrl().'/v2/payments/captures/'.$payment->provider_payment_id.'/refund', $body);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal refund failed.');
        }

        $payload = $response->json();

        $status = strtoupper((string) ($payload['status'] ?? 'PENDING'));

        return [
            'id' => (string) ($payload['id'] ?? ''),
            'status' => $status === 'COMPLETED' ? 'succeeded' : strtolower($status),
            'payload' => $payload,
        ];
    }

    public function enabled(): bool
    {
        return filled(config('services.paypal.client_id')) && filled(config('services.paypal.secret'));
    }

    public function enabledForCurrency(string $currency): bool
    {
        $supported = collect(config('services.paypal.supported_currencies', ['USD']))
            ->map(fn ($value): string => strtoupper((string) $value));

        return $this->enabled() && $supported->contains(strtoupper($currency));
    }

    private function request()
    {
        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->timeout(30)
            ->retry(2, 300);
    }

    private function accessToken(): string
    {
        $clientId = (string) config('services.paypal.client_id');
        $secret = (string) config('services.paypal.secret');

        if ($clientId === '' || $secret === '') {
            throw new RuntimeException('PayPal credentials are not configured.');
        }

        $response = Http::asForm()
            ->acceptJson()
            ->withBasicAuth($clientId, $secret)
            ->timeout(30)
            ->retry(2, 300)
            ->post($this->baseUrl().'/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new RuntimeException('PayPal authentication failed.');
        }

        return (string) $response->json('access_token');
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.paypal.base_url'), '/');
    }
}
