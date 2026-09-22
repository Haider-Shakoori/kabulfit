@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Tailoring request</p><h1>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h1><p>{{ $tailoring->user->email }} · {{ $tailoring->status }}</p></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
<section class="account-panel"><h2>Request</h2><p>UUID: {{ $tailoring->uuid }}</p><p>Profile: {{ $tailoring->measurementProfile?->name }}</p><p>Notes: {{ $tailoring->customer_notes ?: '—' }}</p>@if($tailoring->orderItem?->order)<p>Order: <a href="{{ route('admin.orders.show',['locale'=>app()->getLocale(),'order'=>$tailoring->orderItem->order]) }}">{{ $tailoring->orderItem->order->number }}</a></p>@endif</section>
<section class="account-panel"><h2>Measurements</h2>@if($tailoring->orderItem)@foreach($tailoring->orderItem->measurements as $m)<p>{{ $m->definition_name }}: {{ $m->value_cm }} cm</p>@endforeach @elseif($tailoring->measurementProfile)@foreach($tailoring->measurementProfile->values as $v)<p>{{ $v->definition->translation()?->name }}: {{ $v->value_cm }} cm</p>@endforeach @endif</section>
@if($tailoring->status==='ready')<form method="post" action="{{ route('admin.tailoring.cancel',['locale'=>app()->getLocale(),'tailoring'=>$tailoring]) }}">@csrf<button class="button button-secondary" type="submit">Cancel un-ordered request</button></form>@endif
</div></div></section>
@endsection
