@extends('layouts.app')
@section('content')
<section class="section"><div class="container auth-shell"><div class="auth-card"><h1>{{ __('commerce.checkout') }}</h1><form method="post" action="{{ route('checkout.place',['locale'=>app()->getLocale()]) }}">@csrf
<label>{{ __('commerce.shipping_address') }}<select name="address_uuid" required>@foreach($addresses as $a)<option value="{{ $a->uuid }}">{{ $a->label }} — {{ $a->city }}, {{ $a->province }}</option>@endforeach</select></label>
<label>{{ __('commerce.shipping_method') }}<select name="shipping_method" required>@foreach($shippingMethods as $s)<option value="{{ $s->code }}">{{ $s->name }} — {{ number_format($s->price_minor/100,2) }} {{ $s->currency }}</option>@endforeach</select></label>
<label>{{ __('commerce.coupon') }}<input name="coupon"></label><button class="button button-primary">{{ __('commerce.place_order') }}</button></form></div></div></section>
@endsection