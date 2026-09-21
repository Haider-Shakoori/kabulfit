@extends('layouts.app')

@section('content')
@php($translation = $collection->translation())
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('site.breadcrumbs') }}">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a><span>/</span>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a><span>/</span>
            <span aria-current="page">{{ $translation?->name }}</span>
        </nav>
        <p class="eyebrow">{{ __('site.collection') }}</p>
        <h1>{{ $translation?->name }}</h1>
        <p>{{ $translation?->description }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        @include('catalog._filters', [
            'action' => route('collections.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]),
            'lockedCategory' => null,
            'lockedCollection' => $translation?->slug,
        ])

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
