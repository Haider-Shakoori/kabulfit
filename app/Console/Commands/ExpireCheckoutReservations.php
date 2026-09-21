<?php

namespace App\Console\Commands;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Order;
use App\Services\Commerce\CheckoutService;
use Illuminate\Console\Command;

class ExpireCheckoutReservations extends Command
{
    protected $signature = 'commerce:expire-checkouts';

    protected $description = 'Release stock reservations for abandoned unpaid checkouts.';

    public function handle(CheckoutService $checkout, PaymentGateway $gateway): int
    {
        Order::query()
            ->where('status', 'pending_payment')
            ->whereNotNull('reservation_expires_at')
            ->where('reservation_expires_at', '<=', now())
            ->with(['items', 'payment'])
            ->chunkById(100, function ($orders) use ($checkout, $gateway): void {
                foreach ($orders as $order) {
                    if ($order->payment?->provider_payment_id) {
                        $gateway->cancel($order->payment);
                        $order->payment->update(['status' => 'cancelled']);
                    }

                    $checkout->releaseReservations($order);
                    $order->update(['status' => 'cancelled', 'payment_status' => 'cancelled']);
                }
            });

        return self::SUCCESS;
    }
}
