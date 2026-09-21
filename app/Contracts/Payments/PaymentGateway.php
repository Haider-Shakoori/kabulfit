<?php
namespace App\Contracts\Payments;
use App\Models\Payment;
interface PaymentGateway {public function createIntent(Payment $payment): array; public function refund(Payment $payment, ?int $amountMinor=null): array;}
