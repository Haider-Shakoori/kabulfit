@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Operations</p><h1>Orders</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main">@foreach($orders as $order)
<article class="account-panel"><div class="panel-heading"><div><h2>{{ $order->number }}</h2><p>{{ $order->user->email }} · {{ $order->status }} · {{ $order->payment_status }}</p></div><a class="button button-secondary" href="{{ route('admin.orders.show',['locale'=>app()->getLocale(),'order'=>$order]) }}">Open</a></div><p>{{ number_format($order->total_minor/100,2) }} {{ $order->currency }} · {{ $order->created_at }}</p></article>
@endforeach {{ $orders->links() }}</div></div></section>
@endsection
