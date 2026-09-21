@extends('layouts.app')

@section('content')
<section class="page-hero"><div class="container"><p class="eyebrow">KabulFit</p><h1>{{ __('site.shop_title_h1') }}</h1><p>{{ __('site.shop_description') }}</p></div></section>
<section class="section"><div class="container"><div class="product-grid">@foreach ($products as $product)<x-product-card :product="$product" />@endforeach</div><div class="pagination-shell">{{ $products->links() }}</div></div></section>
@endsection
