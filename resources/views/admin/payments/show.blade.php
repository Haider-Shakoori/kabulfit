@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Payment operations</p><h1>{{ $payment->order->number }}</h1><p>{{ $payment->status }} · {{ $payment->provider_payment_id }}</p></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
<section class="account-panel"><h2>Payment</h2><p>{{ number_format($payment->amount_minor/100,2) }} {{ $payment->currency }}</p><p>Customer: {{ $payment->order->user->email }}</p><p>Status: {{ $payment->status }}</p><p>Stripe PaymentIntent: {{ $payment->provider_payment_id }}</p><p><a href="{{ route('admin.orders.show',['locale'=>app()->getLocale(),'order'=>$payment->order]) }}">Open order</a></p>
@can('refund',$payment)@if($payment->status==='succeeded')<form method="post" action="{{ route('admin.payments.refund',['locale'=>app()->getLocale(),'payment'=>$payment]) }}" class="address-form">@csrf<label>Refund reason<textarea name="reason" maxlength="500"></textarea></label><button class="button button-primary" type="submit">Issue full refund</button></form>@endif @endcan
</section>
<section class="account-panel"><h2>Provider/webhook event trail</h2>@forelse($events as $event)<details><summary>{{ $event->type }} · {{ $event->provider_event_id }} · {{ $event->created_at }}</summary><pre>{{ json_encode($event->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></details>@empty<p>No payment events yet.</p>@endforelse</section>
</div></div></section>
@endsection
