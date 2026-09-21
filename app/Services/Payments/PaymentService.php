<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Services\Commerce\CheckoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(private readonly PaymentGateway $gateway, private readonly CheckoutService $checkout) {}

    public function initiate(Order $order): array
    {
        $payment = $order->payment()->firstOrCreate([], ['uuid' => (string) Str::uuid(), 'provider' => 'stripe', 'status' => 'pending', 'currency' => $order->currency, 'amount_minor' => $order->total_minor, 'idempotency_key' => 'checkout-'.$order->uuid]);
        $result = $this->gateway->createIntent($payment);
        $payment->update(['provider_payment_id' => $result['id'], 'status' => $result['status'] === 'succeeded' ? 'succeeded' : 'processing']);

        return ['payment' => $payment->fresh(), 'client_secret' => $result['client_secret']];
    }

    public function handleStripeEvent(string $eventId, string $type, array $object, array $payload): void
    {
        DB::transaction(function () use ($eventId, $type, $object, $payload) {
            if (PaymentEvent::where('provider_event_id', $eventId)->exists()) {
                return;
            }$payment = Payment::where('provider_payment_id', $object['id'] ?? null)->lockForUpdate()->first();
            $event = PaymentEvent::create(['payment_id' => $payment?->id, 'provider' => 'stripe', 'provider_event_id' => $eventId, 'type' => $type, 'payload' => $payload]);
            if (! $payment) {
                $event->update(['processed_at' => now()]);

                return;
            }
            if ($type === 'payment_intent.succeeded' && $payment->status !== 'succeeded') {
                $payment->update(['status' => 'succeeded']);
                $payment->order->update(['status' => 'paid', 'payment_status' => 'succeeded', 'paid_at' => now()]);
                $this->checkout->captureReservations($payment->order->load('items'));
            } elseif (in_array($type, ['payment_intent.payment_failed', 'payment_intent.canceled'], true) && ! in_array($payment->status, ['succeeded', 'refunded'], true)) {
                $status = $type === 'payment_intent.canceled' ? 'cancelled' : 'failed';
                $payment->update(['status' => $status, 'failure_message' => $object['last_payment_error']['message'] ?? null]);
                $payment->order->update(['status' => $status === 'cancelled' ? 'cancelled' : 'payment_failed', 'payment_status' => $status]);
                $this->checkout->releaseReservations($payment->order);
            }
            $event->update(['processed_at' => now()]);
        }, 3);
    }
}
