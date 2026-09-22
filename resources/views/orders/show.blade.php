@extends('layouts.app')
@section('content')
<section class="section"><div class="container"><h1>{{ __('orders.order_number',['number'=>$order->number]) }}</h1>
<p class="status-pill">{{ __('orders.status_'.$order->status) }}</p>
<h2>{{ __('orders.items') }}</h2>
@foreach($order->items as $item)<article class="account-panel"><strong>{{ $item->name }}</strong><p>{{ $item->quantity }} × {{ number_format($item->unit_price_minor/100,2) }} {{ $order->currency }}</p>
@if($item->is_custom_tailored)<p>{{ __('measurements.custom_tailoring') }} — {{ $item->measurement_profile_name }}</p>@endif</article>@endforeach
<h2>{{ __('orders.timeline') }}</h2>
@foreach($order->statusHistory as $history)<p><strong>{{ __('orders.status_'.$history->status) }}</strong> · {{ $history->occurred_at }}</p>@endforeach
@foreach($order->shipments as $shipment)<section class="account-panel"><h2>{{ __('orders.shipping') }}</h2><p>{{ $shipment->carrier }} {{ $shipment->tracking_number }}</p><p>{{ __('orders.status_'.$shipment->status) }}</p>
@foreach($shipment->events as $event)<p>{{ $event->occurred_at }} — {{ $event->description ?: __('orders.status_'.$event->status) }}</p>@endforeach</section>@endforeach
</div></section>
@endsection