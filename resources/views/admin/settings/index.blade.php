@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Site configuration</p><h1>Settings & SEO</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
<section class="account-panel"><form method="post" action="{{ route('admin.settings.update',['locale'=>app()->getLocale()]) }}" class="address-form">@csrf @method('PUT')
<label>Contact email<input type="email" name="contact_email" value="{{ $values['contact_email'] }}" required></label>
@foreach(config('kabulfit.supported_locales') as $loc)<fieldset><legend>Homepage SEO — {{ strtoupper($loc) }}</legend><label>Title<input name="titles[{{ $loc }}]" value="{{ $values['titles'][$loc] }}" required></label><label>Description<textarea name="descriptions[{{ $loc }}]" maxlength="500" required>{{ $values['descriptions'][$loc] }}</textarea></label></fieldset>@endforeach
<button class="button button-primary">Save settings</button></form></section>
</div></div></section>
@endsection
