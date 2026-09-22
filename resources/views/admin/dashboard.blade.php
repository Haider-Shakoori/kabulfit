@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">KabulFit Administration</p><h1>Dashboard</h1><p>Operational overview for the commerce platform.</p></div></section>
<section class="section"><div class="container account-grid">
@include('admin._nav')
<div class="account-main">
<div class="address-list">
@foreach($metrics as $label => $value)
<article class="account-panel"><p class="eyebrow">{{ str_replace('_',' ',ucfirst($label)) }}</p><h2>{{ number_format($value) }}</h2></article>
@endforeach
</div>
<section class="account-panel"><h2>Recent orders</h2>
@forelse($recentOrders as $order)
<p><a href="{{ route('admin.orders.show',['locale'=>app()->getLocale(),'order'=>$order]) }}">{{ $order->number }}</a> · {{ $order->user->email }} · {{ $order->status }}</p>
@empty<p>No orders yet.</p>@endforelse
</section>
@if($recentAudit->isNotEmpty())
<section class="account-panel"><h2>Recent audit activity</h2>
@foreach($recentAudit as $entry)<p><strong>{{ $entry->action }}</strong> · {{ $entry->actor?->email ?? 'system' }} · {{ $entry->created_at }}</p>@endforeach
</section>
@endif
</div></div></section>
@endsection
