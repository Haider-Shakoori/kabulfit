@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">KabulFit</p>
        <h1>{{ __('site.shop_title_h1') }}</h1>
        <p>{{ __('site.shop_description') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        @include('catalog._filters', [
            'action' => route('shop', ['locale' => app()->getLocale()]),
            'lockedCategory' => null,
            'lockedCollection' => null,
        ])

        <div class="catalog-results-heading">
            <p>{{ trans_choice('site.product_count', $products->total(), ['count' => $products->total()]) }}</p>
        </div>

        <div class="product-grid">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="empty-state">
                    <p>{{ __('site.no_products_match') }}</p>
                    <a class="button button-secondary" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.clear_filters') }}</a>
                </div>
            @endforelse
        </div>

        <div class="pagination-shell">{{ $products->links() }}</div>
    </div>
</section>
@endsection
