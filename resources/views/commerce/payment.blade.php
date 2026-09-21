@extends('layouts.app')
@section('content')
<section class="section"><div class="container auth-shell"><div class="auth-card"><p class="eyebrow">{{ __('commerce.secure_payment') }}</p><h1>{{ __('commerce.order') }} {{ $order->number }}</h1><p>{{ __('commerce.payment_pending') }}</p><p><strong>{{ __('commerce.total') }}: {{ number_format($order->total_minor/100,2) }} {{ $order->currency }}</strong></p><p>Status: {{ $order->payment_status }}</p></div></div></section>
@endsection