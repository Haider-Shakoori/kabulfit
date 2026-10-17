<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payments\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PayPalController extends Controller
{
    public function create(Request $request, string $locale, Order $order, PayPalService $paypal): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        try {
            return response()->json($paypal->createOrder($order));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => __('commerce.paypal_error'),
            ], 422);
        }
    }

    public function capture(Request $request, string $locale, Order $order, PayPalService $paypal): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'paypal_order_id' => ['required', 'string', 'max:100'],
        ]);

        try {
            $payment = $paypal->capture($order, $data['paypal_order_id']);

            return response()->json([
                'success' => true,
                'redirect' => route('orders.show', ['locale' => app()->getLocale(), 'order' => $order]),
                'payment_status' => $payment->status,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => __('commerce.paypal_capture_error'),
            ], 422);
        }
    }
}
