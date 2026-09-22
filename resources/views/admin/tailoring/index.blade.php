@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Custom tailoring</p><h1>Tailoring requests</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main">@foreach($requests as $tailoring)<article class="account-panel"><div class="panel-heading"><div><h2>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h2><p>{{ $tailoring->user->email }} · {{ $tailoring->status }} · {{ $tailoring->uuid }}</p></div><a class="button button-secondary" href="{{ route('admin.tailoring.show',['locale'=>app()->getLocale(),'tailoring'=>$tailoring]) }}">Open</a></div></article>@endforeach {{ $requests->links() }}</div></div></section>
@endsection
