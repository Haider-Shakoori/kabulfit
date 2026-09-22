@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">SEO migration</p><h1>Legacy URL inventory</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
@foreach($entries as $entry)
<details class="account-panel"><summary><strong>{{ $entry->legacy_path }}</strong> — {{ $entry->disposition }}</summary>
<p>Verified: {{ $entry->verified_at?->format('Y-m-d H:i') ?? 'not verified' }}</p>
<form method="post" action="{{ route('admin.legacy.update',['locale'=>app()->getLocale(),'legacyUrl'=>$entry]) }}" class="address-form">@csrf @method('PUT')
<label>Disposition<select name="disposition">@foreach(['redirect','manual_product','private','gone'] as $value)<option value="{{ $value }}" @selected($entry->disposition===$value)>{{ $value }}</option>@endforeach</select></label>
<label>Target path<input name="target_path" value="{{ $entry->target_path }}"></label>
<label><input name="is_active" type="checkbox" value="1" @checked($entry->is_active)> Active</label>
<label>Notes<textarea name="notes">{{ $entry->notes }}</textarea></label>
<button class="button button-primary" type="submit">Save mapping</button>
</form></details>
@endforeach
{{ $entries->links() }}
</div></div></section>
@endsection
