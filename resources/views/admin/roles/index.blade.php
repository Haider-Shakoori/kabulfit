@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Authorization</p><h1>Roles & permissions</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
@foreach($roles as $role)<section class="account-panel"><h2>{{ $role->name }}</h2><p>{{ $role->slug }}</p>
@if($role->slug==='super-admin')<p>Protected role: always has every permission.</p>@else
<form method="post" action="{{ route('admin.roles.update',['locale'=>app()->getLocale(),'role'=>$role]) }}">@csrf @method('PUT')
@foreach($permissions as $permission)<label><input type="checkbox" name="permissions[]" value="{{ $permission->slug }}" @checked($role->permissions->contains('id',$permission->id))> {{ $permission->slug }} — {{ $permission->name }}</label><br>@endforeach
<button class="button button-primary">Save permissions</button></form>@endif
</section>@endforeach
</div></div></section>
@endsection
