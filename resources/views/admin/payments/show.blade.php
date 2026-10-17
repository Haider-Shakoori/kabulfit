@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <a href="{{ route('admin.payments.index', ['locale' => app()->getLocale()]) }}" class="mb-6 inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">← Back to Payments</a>

    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <p class="text-sm text-gray-500">Payment operations</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $payment->order->number }}</h1>
        <p class="mt-1 break-all text-xs text-gray-400">{{ $payment->provider_payment_id }}</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <main class="space-y-6 xl:col-span-2">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Payment</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        ['Amount', number_format($payment->amount_minor / 100, 2).' '.$payment->currency],
                        ['Customer', $payment->order->user->email],
                        ['Status', str($payment->status)->replace('_', ' ')->title()],
                        ['Provider', $payment->provider],
                    ] as [$label, $value])
                        <div class="rounded-lg bg-gray-50 p-4"><dt class="text-xs text-gray-500">{{ $label }}</dt><dd class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $value }}</dd></div>
                    @endforeach
                </dl>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $payment->order]) }}" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Open order</a>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Provider / Webhook Event Trail</h2>
                <div class="mt-4 space-y-3">
                    @forelse($events as $event)
                        <details class="group overflow-hidden rounded-lg border border-gray-200">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-4"><div><p class="font-medium text-gray-900">{{ $event->type }}</p><p class="mt-1 text-xs text-gray-400">{{ $event->provider_event_id }} · {{ $event->created_at?->translatedFormat('M j, Y · H:i') }}</p></div><span class="transition group-open:rotate-180">⌄</span></summary>
                            <pre class="max-h-80 overflow-auto border-t border-gray-100 bg-gray-950 p-4 text-xs text-gray-200">{{ json_encode($event->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                        </details>
                    @empty
                        <p class="rounded-lg bg-gray-50 p-4 text-sm text-gray-500">No payment events yet.</p>
                    @endforelse
                </div>
            </section>
        </main>

        <aside>
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Payment Status</h2>
                <span class="mt-4 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-700">{{ str($payment->status)->replace('_', ' ') }}</span>

                @can('refund', $payment)
                    @if($payment->status === 'succeeded')
                        <form method="POST" action="{{ route('admin.payments.refund', ['locale' => app()->getLocale(), 'payment' => $payment]) }}" class="mt-6">
                            @csrf
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Refund reason</span><textarea name="reason" rows="4" maxlength="500" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                            <button type="submit" class="mt-4 w-full rounded-md bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Issue full refund</button>
                        </form>
                    @endif
                @endcan
            </section>
        </aside>
    </div>
</div>
@endsection
