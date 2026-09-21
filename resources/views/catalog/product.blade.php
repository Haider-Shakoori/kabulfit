@extends('layouts.app')

@section('content')
@php($translation = $product->translation())
<section class="section product-detail">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('site.breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a><span>/</span>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a><span>/</span>
            <a href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $product->category->translation()?->slug]) }}">{{ $product->category->translation()?->name }}</a><span>/</span>
            <span aria-current="page">{{ $translation?->name }}</span>
        </nav>
        <div class="product-detail-grid">
            <div class="product-gallery-placeholder" role="img" aria-label="{{ __('site.product_image_placeholder', ['product' => $translation?->name]) }}"><span>KabulFit</span></div>
            <div class="product-info">
                <p class="eyebrow">{{ $product->category->translation()?->name }}</p>
                <h1>{{ $translation?->name }}</h1>
                <p class="product-price">{{ $product->formattedPrice() }}</p>
                <p>{{ $translation?->description }}</p>
                <dl class="product-facts">
                    <div><dt>{{ __('site.sku') }}</dt><dd>{{ $product->sku }}</dd></div>
                    <div><dt>{{ __('site.availability') }}</dt><dd>{{ $product->stock_quantity > 0 ? __('site.in_stock') : __('site.out_of_stock') }}</dd></div>
                </dl>
                <div class="notice-box"><strong>{{ __('site.measurement_ready') }}</strong><p>{{ __('site.measurement_ready_text') }}</p></div>
            </div>
        </div>
    </div>
</section>
@endsection
