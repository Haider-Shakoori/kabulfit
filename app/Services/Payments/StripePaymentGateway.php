<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Payment;
use RuntimeException;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGateway
{
    public function __construct(private readonly ?StripeClient $client = null) {}

    private function stripe(): StripeClient
    {
        if ($this->client) {
            return $this->client;
        }

        $key = (string) config('services.stripe.secret');

        if ($key === '') {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        return new StripeClient($key);
    }

    public function createIntent(Payment $payment): array
    {
        $intent = $this->stripe()->paymentIntents->create([
            'amount' => $payment->amount_minor,
            'currency' => strtolower($payment->currency),
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'order_uuid' => $payment->order->uuid,
                'payment_uuid' => $payment->uuid,
            ],
        ], ['idempotency_key' => $payment->idempotency_key]);

        return [
            'id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'status' => $intent->status,
        ];
    }

    public function cancel(Payment $payment): array
    {
        $intent = $this->stripe()->paymentIntents->cancel(
            $payment->provider_payment_id,
            [],
            ['idempotency_key' => 'cancel-'.$payment->uuid],
        );

        return ['id' => $intent->id, 'status' => $intent->status];
    }

    public function refund(Payment $payment, ?int $amountMinor = null): array
    {
        $params = ['payment_intent' => $payment->provider_payment_id];

        if ($amountMinor !== null) {
            $params['amount'] = $amountMinor;
        }

        $refund = $this->stripe()->refunds->create(
            $params,
            ['idempotency_key' => 'refund-'.$payment->uuid.'-'.($amountMinor ?? 'full')],
        );

        return ['id' => $refund->id, 'status' => $refund->status];
    }
}
