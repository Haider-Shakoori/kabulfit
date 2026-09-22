@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Account administration</p><h1>{{ $customer->name }}</h1><p>{{ $customer->email }}</p></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
<section class="account-panel"><h2>Account state</h2><form method="post" action="{{ route('admin.customers.update',['locale'=>app()->getLocale(),'customer'=>$customer->uuid]) }}" class="address-form">@csrf @method('PUT')
<label><input type="checkbox" name="is_active" value="1" @checked($customer->is_active)> Active account</label><label>Preferred locale<select name="preferred_locale">@foreach(['en','fa','ps'] as $loc)<option value="{{ $loc }}" @selected($customer->preferred_locale===$loc)>{{ strtoupper($loc) }}</option>@endforeach</select></label><button class="button button-primary">Save account</button></form></section>
@can('manageRoles',$customer)<section class="account-panel"><h2>Roles</h2><form method="post" action="{{ route('admin.customers.roles',['locale'=>app()->getLocale(),'customer'=>$customer->uuid]) }}">@csrf @method('PUT')
@foreach($roles as $role)<label><input type="checkbox" name="roles[]" value="{{ $role->slug }}" @checked($customer->roles->contains('id',$role->id))> {{ $role->name }}</label>@endforeach
<button class="button button-primary">Save roles</button></form></section>@endcan
<section class="account-panel"><h2>Orders</h2>@forelse($customer->orders as $order)<p><a href="{{ route('admin.orders.show',['locale'=>app()->getLocale(),'order'=>$order]) }}">{{ $order->number }}</a> · {{ $order->status }}</p>@empty<p>No orders.</p>@endforelse</section>
</div></div></section>
@endsection
