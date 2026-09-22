@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Tailoring configuration</p><h1>Measurement definitions</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
@foreach($definitions as $definition)@php($translations=$definition->translations->keyBy('locale'))
<details class="account-panel"><summary><strong>{{ $definition->garment_type }} / {{ $definition->code }}</strong></summary>
<form method="post" action="{{ route('admin.measurements.update',['locale'=>app()->getLocale(),'definition'=>$definition]) }}" class="address-form">@csrf @method('PUT')
<label>Minimum cm<input type="number" step="0.01" name="min_cm" value="{{ $definition->min_cm }}" required></label><label>Maximum cm<input type="number" step="0.01" name="max_cm" value="{{ $definition->max_cm }}" required></label><label>Step cm<input type="number" step="0.01" name="step_cm" value="{{ $definition->step_cm }}" required></label><label><input type="checkbox" name="is_required" value="1" @checked($definition->is_required)> Required</label><label><input type="checkbox" name="is_active" value="1" @checked($definition->is_active)> Active</label>
@foreach(config('kabulfit.supported_locales') as $loc)@php($t=$translations->get($loc))<fieldset><legend>{{ strtoupper($loc) }}</legend><label>Name<input name="translations[{{ $loc }}][name]" value="{{ $t?->name }}" required></label><label>Instructions<textarea name="translations[{{ $loc }}][instructions]">{{ $t?->instructions }}</textarea></label></fieldset>@endforeach
<button class="button button-primary">Save definition</button></form></details>@endforeach
</div></div></section>
@endsection
