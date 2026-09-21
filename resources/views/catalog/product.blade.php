@extends('layouts.app')

@section('content')
@php($translation = $product->translation())
@php($categoryTranslation = $product->category->translation())
<section class="section product-detail">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('site.breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a><span>/</span>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a><span>/</span>
            <a href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $categoryTranslation?->slug]) }}">{{ $categoryTranslation?->name }}</a><span>/</span>
            <span aria-current="page">{{ $translation?->name }}</span>
        </nav>

        <div class="product-detail-grid">
            <div class="product-gallery" aria-label="{{ __('site.product_gallery') }}">
                @forelse ($product->media as $media)
                    <figure @class(['product-gallery-item', 'is-primary' => $media->is_primary])>
                        <img src="{{ $media->url() }}" width="{{ $media->width }}" height="{{ $media->height }}" alt="{{ $media->translation()?->alt_text }}" @if (! $media->is_primary) loading="lazy" @else fetchpriority="high" @endif>
                    </figure>
                @empty
                    <div class="product-gallery-placeholder" role="img" aria-label="{{ __('site.product_image_placeholder', ['product' => $translation?->name]) }}"><span>KabulFit</span></div>
                @endforelse
            </div>

            <div class="product-info">
                <p class="eyebrow">{{ $categoryTranslation?->name }}</p>
                <h1>{{ $translation?->name }}</h1>
                <p class="product-price">{{ $product->formattedPrice() }}</p>
                <p>{{ $translation?->description }}</p>

                @if ($product->collections->isNotEmpty())
                    <div class="product-collections">
                        @foreach ($product->collections as $collection)
                            @php($collectionTranslation = $collection->translation())
                            <a href="{{ route('collections.show', ['locale' => app()->getLocale(), 'slug' => $collectionTranslation?->slug]) }}">{{ $collectionTranslation?->name }}</a>
                        @endforeach
                    </div>
                @endif

                <dl class="product-facts">
                    <div><dt>{{ __('site.sku') }}</dt><dd>{{ $product->sku }}</dd></div>
                    <div><dt>{{ __('site.availability') }}</dt><dd>{{ $product->isInStock() ? __('site.in_stock') : __('site.out_of_stock') }}</dd></div>
                    <div><dt>{{ __('site.available_quantity') }}</dt><dd>{{ $product->availableStock() }}</dd></div>
                </dl>

                @if ($product->variants->isNotEmpty())
                    <div class="variant-table-wrap">
                        <h2>{{ __('site.available_options') }}</h2>
                        <div class="variant-grid">
                            @foreach ($product->variants->where('is_active', true) as $variant)
                                <div class="variant-card">
                                    <div>
                                        <strong>{{ $variant->size?->code ?? __('site.custom') }}</strong>
                                        @if ($variant->color)
                                            <span>{{ $variant->color->translation()?->name }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $product->formattedPrice($variant->currentPriceMinor()) }}</strong>
                                        <span>{{ trans_choice('site.units_available', $variant->availableQuantity(), ['count' => $variant->availableQuantity()]) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="notice-box">
                    <strong>{{ __('site.measurement_ready') }}</strong>
                    <p>{{ __('site.measurement_ready_text') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($relatedProducts->isNotEmpty())
<section class="section section-soft">
    <div class="container">
        <div class="section-heading"><div><p class="eyebrow">{{ __('site.discover') }}</p><h2>{{ __('site.related_products') }}</h2></div></div>
        <div class="product-grid">
            @foreach ($relatedProducts as $relatedProduct)
                <x-product-card :product="$relatedProduct" />
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($recentlyViewed->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-heading"><div><p class="eyebrow">{{ __('site.recently_viewed_label') }}</p><h2>{{ __('site.recently_viewed') }}</h2></div></div>
        <div class="product-grid">
            @foreach ($recentlyViewed as $recentProduct)
                <x-product-card :product="$recentProduct" />
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
