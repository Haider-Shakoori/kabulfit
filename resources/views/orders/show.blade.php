@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('orders.customer_orders') }}</p>
        <h1>{{ __('orders.order_number', ['number' => $order->number]) }}</h1>
        <p>{{ __('orders.order_detail_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container account-grid">
        <div class="account-main">
            <article class="account-panel">
                <h2>{{ __('orders.order_details') }}</h2>
                <dl class="account-facts">
                    <div><dt>{{ __('orders.status') }}</dt><dd>{{ __('orders.status_'.$order->status) }}</dd></div>
                    <div><dt>{{ __('orders.payment_status') }}</dt><dd>{{ __('orders.status_'.$order->payment_status) }}</dd></div>
                    <div><dt>{{ __('orders.subtotal') }}</dt><dd>{{ number_format($order->subtotal_minor / 100, 2) }} {{ $order->currency }}</dd></div>
                    <div><dt>{{ __('orders.discount') }}</dt><dd>{{ number_format($order->discount_minor / 100, 2) }} {{ $order->currency }}</dd></div>
                    <div><dt>{{ __('orders.shipping') }}</dt><dd>{{ number_format($order->shipping_minor / 100, 2) }} {{ $order->currency }}</dd></div>
                    <div><dt>{{ __('orders.total') }}</dt><dd>{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</dd></div>
                </dl>
            </article>

            <article class="account-panel">
                <h2>{{ __('orders.items') }}</h2>
                @foreach ($order->items as $item)
                    <div class="address-card">
                        <strong>{{ $item->name }}</strong>
                        <p>{{ $item->quantity }} × {{ number_format($item->unit_price_minor / 100, 2) }} {{ $order->currency }}</p>
                        @if ($item->variant_label)<p>{{ $item->variant_label }}</p>@endif
                        @if ($item->is_custom_tailored)
                            <p>{{ __('measurements.custom_tailoring') }} — {{ $item->measurement_profile_name }}</p>
                        @endif
                    </div>
                @endforeach
            </article>

            <article class="account-panel">
                <h2>{{ __('orders.timeline') }}</h2>
                <div class="address-list">
                    @foreach ($order->statusHistory as $history)
                        <div class="address-card">
                            <strong>{{ __('orders.status_'.$history->status) }}</strong>
                            <p>{{ $history->occurred_at?->format('Y-m-d H:i') }}</p>
                            @if ($history->note)<p>{{ $history->note }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </article>

            @forelse ($order->shipments as $shipment)
                <article class="account-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">{{ __('orders.shipping') }}</p>
                            <h2>{{ $shipment->carrier ?: __('orders.shipment') }}</h2>
                        </div>
                        <span class="status-pill">{{ __('orders.status_'.$shipment->status) }}</span>
                    </div>

                    <dl class="account-facts">
                        @if ($shipment->service)<div><dt>{{ __('orders.service') }}</dt><dd>{{ $shipment->service }}</dd></div>@endif
                        @if ($shipment->tracking_number)<div><dt>{{ __('orders.tracking_number') }}</dt><dd>{{ $shipment->tracking_number }}</dd></div>@endif
                    </dl>

                    @if ($shipment->tracking_url)
                        <a class="button button-secondary" href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer">
                            {{ __('orders.track_shipment') }}
                        </a>
                    @endif

                    <h3>{{ __('orders.tracking_timeline') }}</h3>
                    <div class="address-list">
                        @foreach ($shipment->events as $event)
                            <div class="address-card">
                                <strong>{{ __('orders.status_'.$event->status) }}</strong>
                                <p>{{ $event->occurred_at?->format('Y-m-d H:i') }}@if($event->location) · {{ $event->location }}@endif</p>
                                @if ($event->description)<p>{{ $event->description }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <article class="account-panel">
                    <h2>{{ __('orders.shipping') }}</h2>
                    <p>{{ __('orders.no_shipment_yet') }}</p>
                </article>
            @endforelse
        </div>
    </div>
</section>
@endsection
