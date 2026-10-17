@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
        <p class="mt-1 text-sm text-gray-500">Stripe payment operations and refund review.</p>
    </div>

    <div class="space-y-4">
        @forelse($payments as $payment)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-semibold text-gray-900">{{ $payment->order->number }}</h2>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($payment->status)->replace('_', ' ') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ $payment->order->user->email }}</p>
                        <p class="mt-1 text-xs text-gray-400">Provider: {{ $payment->provider }} · {{ $payment->provider_payment_id ?: 'not created' }}</p>
                    </div>
                    <div class="text-end">
                        <p class="text-lg font-bold text-gray-900">{{ number_format($payment->amount_minor / 100, 2) }} {{ $payment->currency }}</p>
                        <a href="{{ route('admin.payments.show', ['locale' => app()->getLocale(), 'payment' => $payment]) }}" class="mt-3 inline-flex rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Open</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white py-14 text-center text-sm text-gray-500 shadow-sm">No payments yet.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $payments->links() }}</div>
</div>
@endsection
