<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Payment::class);

        return view('admin.payments.index', [
            'payments' => Payment::query()->with('order.user')->latest()->paginate(30),
            'seo' => PrivatePageSeo::make('Admin Payments', route('admin.payments.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(string $locale, Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('admin.payments.show', [
            'payment' => $payment->load(['order.user']),
            'events' => \App\Models\PaymentEvent::query()->where('payment_id', $payment->id)->latest()->get(),
            'seo' => PrivatePageSeo::make('Admin Payment', route('admin.payments.show', ['locale' => app()->getLocale(), 'payment' => $payment])),
        ]);
    }

    public function refund(Request $request, string $locale, Payment $payment, PaymentService $payments): RedirectResponse
    {
        $this->authorize('refund', $payment);
        $data = $request->validate(['reason' => 'nullable|string|max:500']);

        $payments->refund($payment, $request->user(), $data['reason'] ?? null);

        return back()->with('status', 'Refund request processed.');
    }
}
