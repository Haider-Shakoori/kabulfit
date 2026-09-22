<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Services\Commerce\CheckoutService;
use App\Services\Orders\OrderLifecycleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly CheckoutService $checkout,
        private readonly OrderLifecycleService $orders,
        private readonly AuditService $audit,
    ) {}

    public function initiate(Order $order): array
    {
        $payment = $order->payment()->firstOrCreate([], ['uuid' => (string) Str::uuid(), 'provider' => 'stripe', 'status' => 'pending', 'currency' => $order->currency, 'amount_minor' => $order->total_minor, 'idempotency_key' => 'checkout-'.$order->uuid]);
        $result = $this->gateway->createIntent($payment);
        $payment->update(['provider_payment_id' => $result['id'], 'status' => $result['status'] === 'succeeded' ? 'succeeded' : 'processing']);

        return ['payment' => $payment->fresh(), 'client_secret' => $result['client_secret']];
    }

    public function refund(Payment $payment, User $actor, ?string $reason = null): Payment
    {
        $payment = Payment::query()->with('order')->whereKey($payment->id)->firstOrFail();

        if ($payment->status !== 'succeeded' || ! $payment->provider_payment_id) {
            throw ValidationException::withMessages([
                'payment' => __('admin.payment_not_refundable'),
            ]);
        }

        $result = $this->gateway->refund($payment);

        DB::transaction(function () use ($payment, $actor, $reason, $result): void {
            $payment = Payment::query()->with('order')->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status === 'refunded') {
                return;
            }

            PaymentEvent::query()->firstOrCreate(
                ['provider_event_id' => 'admin-refund:'.$result['id']],
                [
                    'payment_id' => $payment->id,
                    'provider' => $payment->provider,
                    'type' => 'refund.admin',
                    'payload' => [
                        'refund_id' => $result['id'],
                        'status' => $result['status'],
                        'reason' => $reason,
                        'actor_uuid' => $actor->uuid,
                    ],
                    'processed_at' => now(),
                ],
            );

            if (($result['status'] ?? null) === 'succeeded') {
                $payment->update(['status' => 'refunded']);
                $payment->order->update(['payment_status' => 'refunded']);

                if ($payment->order->status !== 'refunded') {
                    $this->orders->transition(
                        $payment->order,
                        'refunded',
                        'admin',
                        $reason ?: __('admin.refund_default_note'),
                    );
                }
            }

            $this->audit->record($actor, 'payment.refund_requested', $payment, [
                'order_uuid' => $payment->order->uuid,
                'refund_id' => $result['id'],
                'refund_status' => $result['status'],
                'reason' => $reason,
            ]);
        }, 3);

        return $payment->fresh(['order']);
    }

    public function handleStripeEvent(string $eventId, string $type, array $object, array $payload): void
    {
        DB::transaction(function () use ($eventId, $type, $object, $payload) {
            if (PaymentEvent::where('provider_event_id', $eventId)->exists()) {
                return;
            }

            $providerPaymentId = $object['id'] ?? null;
            if (in_array($type, ['charge.refunded', 'refund.updated'], true)) {
                $providerPaymentId = $object['payment_intent'] ?? null;
            }

            $payment = Payment::where('provider_payment_id', $providerPaymentId)->lockForUpdate()->first();
            $event = PaymentEvent::create(['payment_id' => $payment?->id, 'provider' => 'stripe', 'provider_event_id' => $eventId, 'type' => $type, 'payload' => $payload]);
            if (! $payment) {
                $event->update(['processed_at' => now()]);

                return;
            }
            if ($type === 'payment_intent.succeeded' && $payment->status !== 'succeeded') {
                $payment->update(['status' => 'succeeded']);
                $payment->order->update(['payment_status' => 'succeeded', 'paid_at' => now()]);
                $this->orders->transition($payment->order, 'paid', 'stripe');
                $this->checkout->captureReservations($payment->order->load('items'));
            } elseif (in_array($type, ['charge.refunded', 'refund.updated'], true)
                && ($object['status'] ?? 'succeeded') === 'succeeded'
                && $payment->status === 'succeeded') {
                $payment->update(['status' => 'refunded']);
                $payment->order->update(['payment_status' => 'refunded']);
                $this->orders->transition($payment->order, 'refunded', 'stripe');
            } elseif (in_array($type, ['payment_intent.payment_failed', 'payment_intent.canceled'], true) && ! in_array($payment->status, ['succeeded', 'refunded'], true)) {
                $status = $type === 'payment_intent.canceled' ? 'cancelled' : 'failed';
                $payment->update(['status' => $status, 'failure_message' => $object['last_payment_error']['message'] ?? null]);
                $payment->order->update(['payment_status' => $status]);
                $this->orders->transition($payment->order, $status === 'cancelled' ? 'cancelled' : 'payment_failed', 'stripe');
                $this->checkout->releaseReservations($payment->order);
            }
            $event->update(['processed_at' => now()]);
        }, 3);
    }
}
