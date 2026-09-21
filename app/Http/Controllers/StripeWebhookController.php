<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException|UnexpectedValueException) {
            return response('invalid webhook', 400);
        }

        $payload = $event->toArray();
        $payments->handleStripeEvent(
            $event->id,
            $event->type,
            (array) ($payload['data']['object'] ?? []),
            $payload,
        );

        return response('ok', 200);
    }
}
