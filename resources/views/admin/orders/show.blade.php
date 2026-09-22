@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Order operations</p><h1>{{ $order->number }}</h1><p>{{ $order->user->email }}</p></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
<section class="account-panel"><h2>Order state</h2><p>{{ $order->status }} · payment {{ $order->payment_status }}</p>
<form method="post" action="{{ route('admin.orders.transition',['locale'=>app()->getLocale(),'order'=>$order]) }}" class="address-form">@csrf
<label>New status<select name="status">@foreach(['paid','processing','ready','shipped','delivered','returned','cancelled','refunded'] as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></label>
<label>Internal/customer note<textarea name="note"></textarea></label><button class="button button-primary">Change status</button></form></section>
<section class="account-panel"><h2>Items</h2>@foreach($order->items as $item)<p><strong>{{ $item->sku }}</strong> {{ $item->name }} × {{ $item->quantity }}</p>@endforeach</section>
<section class="account-panel"><h2>Status history</h2>@foreach($order->statusHistory as $history)<p>{{ $history->occurred_at }} · <strong>{{ $history->status }}</strong> · {{ $history->source }} @if($history->note)· {{ $history->note }}@endif</p>@endforeach</section>
<section class="account-panel"><h2>Create shipment</h2><form method="post" action="{{ route('admin.orders.shipments.store',['locale'=>app()->getLocale(),'order'=>$order]) }}" class="address-form">@csrf
<label>Carrier<input name="carrier"></label><label>Service<input name="service"></label><label>Tracking number<input name="tracking_number"></label><label>Tracking URL<input name="tracking_url" type="url"></label><label>Location<input name="location"></label><label>Description<textarea name="description"></textarea></label><button class="button button-primary">Create shipment</button></form></section>
@foreach($order->shipments as $shipment)
<section class="account-panel"><h2>Shipment {{ $shipment->tracking_number ?: $shipment->uuid }}</h2><p>{{ $shipment->carrier }} · {{ $shipment->status }}</p>
<form method="post" action="{{ route('admin.shipments.status',['locale'=>app()->getLocale(),'shipment'=>$shipment]) }}" class="address-form">@csrf
<label>Status<select name="status">@foreach(['shipped','in_transit','out_for_delivery','delivered','exception','returned'] as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></label><label>Location<input name="location"></label><label>Description<textarea name="description"></textarea></label><button class="button button-secondary">Update shipment</button></form>
@foreach($shipment->events as $event)<p>{{ $event->occurred_at }} · {{ $event->status }} @if($event->location)· {{ $event->location }}@endif</p>@endforeach
</section>@endforeach
@if($order->payment)<p><a class="button button-secondary" href="{{ route('admin.payments.show',['locale'=>app()->getLocale(),'payment'=>$order->payment]) }}">View payment</a></p>@endif
</div></div></section>
@endsection
