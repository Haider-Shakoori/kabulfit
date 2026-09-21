@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">KabulFit</p>
        <h1>{{ __('site.shop_title_h1') }}</h1>
        <p>{{ __('site.shop_description') }}</p>
    </div>
</section>

<section class="section catalog-layout">
    <div class="container">
        <x-catalog-filters :action="route('shop', ['locale' => app()->getLocale()])" :facets="$facets" />

        <div class="catalog-results-header">
            <p>{{ trans_choice('site.products_found', $products->total(), ['count' => $products->total()]) }}</p>
        </div>

        <div class="product-grid">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="empty-state"><p>{{ __('site.no_products_match') }}</p></div>
            @endforelse
        </div>

        <div class="pagination-shell">{{ $products->links() }}</div>
    </div>
</section>
@endsection
