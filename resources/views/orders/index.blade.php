@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('orders.customer_orders') }}</p>
        <h1>{{ __('orders.orders') }}</h1>
        <p>{{ __('orders.orders_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="account-main">
            @forelse ($orders as $order)
                <article class="account-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">{{ $order->created_at?->format('Y-m-d') }}</p>
                            <h2>{{ $order->number }}</h2>
                        </div>
                        <a class="button button-secondary" href="{{ route('orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}">
                            {{ __('orders.view_order') }}
                        </a>
                    </div>
                    <dl class="account-facts">
                        <div><dt>{{ __('orders.status') }}</dt><dd>{{ __('orders.status_'.$order->status) }}</dd></div>
                        <div><dt>{{ __('orders.payment_status') }}</dt><dd>{{ __('orders.status_'.$order->payment_status) }}</dd></div>
                        <div><dt>{{ __('orders.total') }}</dt><dd>{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</dd></div>
                        @if ($order->shipments->first())
                            <div><dt>{{ __('orders.shipping') }}</dt><dd>{{ __('orders.status_'.$order->shipments->first()->status) }}</dd></div>
                        @endif
                    </dl>
                </article>
            @empty
                <div class="account-panel">
                    <p>{{ __('orders.no_orders') }}</p>
                </div>
            @endforelse
        </div>

        {{ $orders->links() }}
    </div>
</section>
@endsection
