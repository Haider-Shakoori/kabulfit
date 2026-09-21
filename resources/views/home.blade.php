@extends('layouts.app')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">{{ __('site.heritage_label') }}</p>
            <h1>{{ __('site.hero_title') }}</h1>
            <p class="hero-lead">{{ __('site.hero_subtitle') }}</p>
            <div class="button-row">
                <a class="button button-primary" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop_now') }}</a>
                <a class="button button-secondary" href="#tailoring">{{ __('site.measurement_guide') }}</a>
            </div>
        </div>
        <div class="hero-art" role="img" aria-label="{{ __('site.hero_art_alt') }}">
            <div class="hero-pattern" aria-hidden="true"></div>
            <div class="hero-card"><span>100+</span><small>{{ __('site.years_tradition') }}</small></div>
        </div>
    </div>
</section>

<section class="trust-strip" aria-label="{{ __('site.service_highlights') }}">
    <div class="container trust-grid">
        <div><strong>{{ __('site.custom_sizing') }}</strong><span>{{ __('site.custom_sizing_text') }}</span></div>
        <div><strong>{{ __('site.global_shipping') }}</strong><span>{{ __('site.global_shipping_text') }}</span></div>
        <div><strong>{{ __('site.quality_assured') }}</strong><span>{{ __('site.quality_assured_text') }}</span></div>
    </div>
</section>

<section id="categories" class="section">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">{{ __('site.discover') }}</p><h2>{{ __('site.shop_by_category') }}</h2></div>
            <p>{{ __('site.category_intro') }}</p>
        </div>
        <div class="category-grid">
            @foreach ($categories as $category)
                @php($translation = $category->translation())
                <a class="category-card" href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">
                    <span class="category-number">0{{ $loop->iteration }}</span>
                    <h3>{{ $translation?->name }}</h3>
                    <p>{{ $translation?->description }}</p>
                    <span class="text-link">{{ __('site.explore_collection') }} →</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">{{ __('site.featured_label') }}</p><h2>{{ __('site.featured_title') }}</h2></div>
            <a class="text-link" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.view_all') }} →</a>
        </div>
        <div class="product-grid">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <p>{{ __('site.no_featured') }}</p>
            @endforelse
        </div>
    </div>
</section>

<section class="section story-section">
    <div class="container story-grid">
        <div class="story-art" aria-hidden="true"><span>{{ __('site.craftsmanship') }}</span></div>
        <div>
            <p class="eyebrow">{{ __('site.our_story') }}</p>
            <h2>{{ __('site.afghan_culture') }}</h2>
            <p>{{ __('site.story_p1') }}</p>
            <p>{{ __('site.story_p2') }}</p>
        </div>
    </div>
</section>

<section id="tailoring" class="section tailoring-section">
    <div class="container tailoring-grid">
        <div>
            <p class="eyebrow">{{ __('site.perfect_fit_technology') }}</p>
            <h2>{{ __('site.measurements_title') }}</h2>
            <p>{{ __('site.measurements_text') }}</p>
        </div>
        <div class="tailoring-panel">
            <strong>{{ __('site.made_to_measure') }}</strong>
            <p>{{ __('site.tailoring_placeholder') }}</p>
            <span class="status-pill">{{ __('site.coming_batch') }}</span>
        </div>
    </div>
</section>

<section class="section prose-section">
    <div class="container prose-narrow">
        <h2>{{ __('site.seo_heading') }}</h2>
        <p>{{ __('site.seo_intro') }}</p>
        <h3>{{ __('site.gand_heading') }}</h3>
        <p>{{ __('site.gand_text') }}</p>
        <h3>{{ __('site.embroidery_heading') }}</h3>
        <p>{{ __('site.embroidery_text') }}</p>
        <h3>{{ __('site.tailoring_heading') }}</h3>
        <p>{{ __('site.tailoring_text') }}</p>
    </div>
</section>
@endsection
