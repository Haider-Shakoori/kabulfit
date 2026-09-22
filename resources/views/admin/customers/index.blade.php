@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Accounts</p><h1>Customers & users</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main">@foreach($customers as $customer)<article class="account-panel"><div class="panel-heading"><div><h2>{{ $customer->name }}</h2><p>{{ $customer->email }} · {{ $customer->is_active ? 'active' : 'inactive' }} · {{ $customer->roles->pluck('name')->join(', ') ?: 'customer' }}</p></div><a class="button button-secondary" href="{{ route('admin.customers.show',['locale'=>app()->getLocale(),'customer'=>$customer->uuid]) }}">Open</a></div></article>@endforeach {{ $customers->links() }}</div></div></section>
@endsection
