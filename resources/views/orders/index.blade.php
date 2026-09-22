@extends('layouts.app')
@section('content')
<section class="section"><div class="container"><h1>{{ __('orders.orders') }}</h1>
@forelse($orders as $order)
<article class="account-panel"><a href="{{ route('orders.show',['locale'=>app()->getLocale(),'order'=>$order]) }}"><strong>{{ $order->number }}</strong></a>
<p>{{ __('orders.status_'.$order->status) }} · {{ number_format($order->total_minor / 100,2) }} {{ $order->currency }}</p></article>
@empty<p>{{ __('orders.no_orders') }}</p>@endforelse
{{ $orders->links() }}</div></section>
@endsection