@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Stripe operations</p><h1>Payments</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main">@foreach($payments as $payment)<article class="account-panel"><div class="panel-heading"><div><h2>{{ $payment->order->number }}</h2><p>{{ $payment->order->user->email }} · {{ $payment->status }} · {{ number_format($payment->amount_minor/100,2) }} {{ $payment->currency }}</p></div><a class="button button-secondary" href="{{ route('admin.payments.show',['locale'=>app()->getLocale(),'payment'=>$payment]) }}">Open</a></div><p>Provider: {{ $payment->provider }} · {{ $payment->provider_payment_id ?: 'not created' }}</p></article>@endforeach {{ $payments->links() }}</div></div></section>
@endsection
