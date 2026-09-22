@extends('layouts.app')
@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">Catalog administration</p><h1>Products</h1></div></section>
<section class="section"><div class="container account-grid">@include('admin._nav')
<div class="account-main"><x-form-errors />@if(session('status'))<div class="account-panel">{{ session('status') }}</div>@endif
@foreach($products as $product)
@php($translations=$product->translations->keyBy('locale'))
<details class="account-panel"><summary><strong>{{ $product->sku }}</strong> — {{ $translations->get(app()->getLocale())?->name ?? $translations->get('en')?->name }}</summary>
<form method="post" action="{{ route('admin.products.update',['locale'=>app()->getLocale(),'product'=>$product->sku]) }}" class="address-form">@csrf @method('PUT')
<label>Price minor units<input name="price_minor" type="number" min="0" value="{{ $product->price_minor }}" required></label>
<label>Sale price minor units<input name="sale_price_minor" type="number" min="0" value="{{ $product->sale_price_minor }}"></label>
<label>Fallback stock<input name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}" required></label>
<label><input name="is_active" type="checkbox" value="1" @checked($product->is_active)> Active</label>
<label><input name="is_featured" type="checkbox" value="1" @checked($product->is_featured)> Featured</label>
<label><input name="tailoring_enabled" type="checkbox" value="1" @checked($product->tailoring_enabled)> Tailoring enabled</label>
<label>Measurement garment type<select name="measurement_garment_type"><option value="">None</option>@foreach(['perahan_tunban','dress','waistcoat'] as $type)<option value="{{ $type }}" @selected($product->measurement_garment_type===$type)>{{ $type }}</option>@endforeach</select></label>
@foreach(config('kabulfit.supported_locales') as $locale)
@php($t=$translations->get($locale))
<fieldset><legend>{{ strtoupper($locale) }}</legend>
<label>Name<input name="translations[{{ $locale }}][name]" value="{{ $t?->name }}" required></label>
<label>Short description<textarea name="translations[{{ $locale }}][short_description]">{{ $t?->short_description }}</textarea></label>
<label>Description<textarea name="translations[{{ $locale }}][description]">{{ $t?->description }}</textarea></label>
<label>SEO title<input name="translations[{{ $locale }}][seo_title]" value="{{ $t?->seo_title }}"></label>
<label>SEO description<textarea name="translations[{{ $locale }}][seo_description]">{{ $t?->seo_description }}</textarea></label>
</fieldset>
@endforeach
<button class="button button-primary" type="submit">Save product</button></form></details>
@endforeach
{{ $products->links() }}</div></div></section>
@endsection
