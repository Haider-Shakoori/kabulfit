@extends('layouts.app')

@section('content')
@php
    $translation = $product->translation();
    $categoryTranslation = $product->category->translation();
@endphp
<section class="section product-detail">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('site.breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a><span>/</span>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a><span>/</span>
            <a href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $categoryTranslation?->slug]) }}">{{ $categoryTranslation?->name }}</a><span>/</span>
            <span aria-current="page">{{ $translation?->name }}</span>
        </nav>

        <div class="product-detail-grid">
            <div class="product-gallery">
                @forelse ($product->media as $media)
                    <figure @class(['product-gallery-item', 'is-primary' => $media->is_primary])>
                        <img
                            src="{{ $media->url() }}"
                            alt="{{ $media->translation()?->alt_text }}"
                            width="{{ $media->width }}"
                            height="{{ $media->height }}"
                            @if (! $media->is_primary) loading="lazy" @endif
                            decoding="async"
                        >
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

                <dl class="product-facts">
                    <div><dt>{{ __('site.sku') }}</dt><dd>{{ $product->sku }}</dd></div>
                    <div><dt>{{ __('site.availability') }}</dt><dd>{{ $product->availableStock() > 0 ? __('site.in_stock') : __('site.out_of_stock') }} · {{ $product->availableStock() }}</dd></div>
                </dl>

                @if ($product->variants->isNotEmpty())
                    <section class="variant-section" aria-labelledby="variants-title">
                        <h2 id="variants-title">{{ __('site.available_variants') }}</h2>
                        <div class="variant-grid">
                            @foreach ($product->variants->where('is_active', true) as $variant)
                                <article class="variant-card">
                                    <strong>{{ $variant->sku }}</strong>
                                    <div class="variant-options">
                                        @foreach ($variant->optionValues->sortBy(fn ($value) => $value->option->sort_order) as $value)
                                            <span>{{ $value->option->translation()?->name }}: {{ $value->translation()?->name }}</span>
                                        @endforeach
                                    </div>
                                    <small>{{ $variant->availableStock() > 0 ? __('site.in_stock') : __('site.out_of_stock') }} · {{ $variant->availableStock() }}</small>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <div class="notice-box"><strong>{{ __('site.measurement_ready') }}</strong><p>{{ __('site.measurement_ready_text') }}</p></div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="related-products">
                <div class="section-heading"><div><p class="eyebrow">{{ __('site.discover') }}</p><h2>{{ __('site.related_products') }}</h2></div></div>
                <div class="product-grid">@foreach ($related as $item)<x-product-card :product="$item" />@endforeach</div>
            </section>
        @endif

        @if ($recentlyViewed->isNotEmpty())
            <section class="related-products">
                <div class="section-heading"><div><p class="eyebrow">{{ __('site.recently_viewed') }}</p><h2>{{ __('site.continue_exploring') }}</h2></div></div>
                <div class="product-grid">@foreach ($recentlyViewed as $item)<x-product-card :product="$item" />@endforeach</div>
            </section>
        @endif
    </div>
</section>
@endsection
