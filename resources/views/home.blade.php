@extends('layouts.app')

@section('content')
<section class="live-hero" data-section="hero">
    <img class="live-hero-image" src="{{ asset('images/kabulfit-live/hero-heritage.png') }}" width="1600" height="1000" alt="{{ __('site.hero_art_alt') }}" fetchpriority="high" decoding="async">
    <div class="live-hero-overlay" aria-hidden="true"></div>
    <div class="container live-hero-content">
        <div class="live-hero-copy">
            <span class="live-badge">{{ __('site.featured_label') }}</span>
            <h1>{{ __('site.hero_title') }}</h1>
            <p>{{ __('site.hero_subtitle') }}</p>
            <div class="button-row">
                <a class="button live-gradient-button" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">
                    {{ __('site.shop_now') }} <span aria-hidden="true">→</span>
                </a>
                <a class="button live-outline-button" href="#tailoring">
                    <x-icon name="ruler" /> {{ __('site.measurement_guide') }}
                </a>
            </div>
        </div>
    </div>
    <div class="live-hero-fade" aria-hidden="true"></div>
</section>

<section class="live-assurance" data-section="trust" aria-label="{{ __('site.service_highlights') }}">
    <div class="container live-assurance-grid">
        <div class="live-assurance-item">
            <span class="live-assurance-icon"><x-icon name="ruler" /></span>
            <div><strong>{{ __('site.custom_sizing') }}</strong><span>{{ __('site.custom_sizing_text') }}</span></div>
        </div>
        <div class="live-assurance-item">
            <span class="live-assurance-icon"><x-icon name="globe" /></span>
            <div><strong>{{ __('site.global_shipping') }}</strong><span>{{ __('site.global_shipping_text') }}</span></div>
        </div>
        <div class="live-assurance-item">
            <span class="live-assurance-icon"><x-icon name="quality" /></span>
            <div><strong>{{ __('site.quality_assured') }}</strong><span>{{ __('site.quality_assured_text') }}</span></div>
        </div>
        <div class="live-assurance-item">
            <span class="live-assurance-icon"><x-icon name="scissors" /></span>
            <div><strong>{{ __('site.handcrafted') }}</strong><span>{{ __('site.handcrafted_text') }}</span></div>
        </div>
    </div>
</section>

<section id="categories" class="section live-category-section" data-section="categories">
    <div class="container">
        <div class="live-section-heading live-section-heading-centered">
            <h2>{{ __('site.shop_by_category') }}</h2>
            <p>{{ __('site.category_intro') }}</p>
        </div>
        @php($categoryImages = [
            'images/kabulfit-live/hero-heritage.png',
            'images/kabulfit-live/measurement-guide.png',
            'images/kabulfit-live/craftsmanship.jpg',
            'images/kabulfit-live/hero-heritage.png',
        ])
        <div class="live-category-grid">
            @foreach ($categories as $category)
                @php($translation = $category->translation())
                @php($categoryImage = $categoryImages[($loop->index) % count($categoryImages)])
                <a class="live-category-card" href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">
                    <img src="{{ asset($categoryImage) }}" alt="{{ $translation?->name }}" loading="lazy" decoding="async">
                    <span class="live-category-overlay" aria-hidden="true"></span>
                    <span class="live-category-copy">
                        <strong>{{ $translation?->name }}</strong>
                        <span>{{ __('site.explore_collection') }} <span aria-hidden="true">→</span></span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section live-products-section" data-section="featured">
    <div class="container">
        <div class="live-section-heading live-section-heading-split">
            <div>
                <h2>{{ __('site.featured_label') }}</h2>
                <p>{{ __('site.featured_title') }}</p>
            </div>
            <a class="button live-gradient-button live-small-button" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">
                {{ __('site.view_all') }} <span aria-hidden="true">→</span>
            </a>
        </div>
        <div class="product-grid">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="empty-state">
                    <p>{{ __('site.no_featured') }}</p>
                    <a class="button live-gradient-button" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.browse_all_products') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section id="story" class="live-story" data-section="story">
    <img class="live-story-bg" src="{{ asset('images/kabulfit-live/hero-heritage.png') }}" alt="" loading="lazy" decoding="async">
    <div class="live-story-overlay" aria-hidden="true"></div>
    <div class="container live-story-grid">
        <div class="live-story-copy">
            <span class="live-badge live-teal-badge">{{ __('site.our_story') }}</span>
            <h2>{{ __('site.afghan_culture') }}</h2>
            <p class="live-story-lead">{{ __('site.story_p1') }}</p>
            <p>{{ __('site.story_p2') }}</p>
            <a class="button live-outline-button" href="#heritage-content">{{ __('site.learn_more') }} <span aria-hidden="true">→</span></a>
        </div>
        <div class="live-story-visual">
            <img src="{{ asset('images/kabulfit-live/craftsmanship.jpg') }}" width="900" height="900" loading="lazy" decoding="async" alt="{{ __('site.craftsmanship_alt') }}">
            <div class="live-story-stat">
                <span class="live-stat-icon"><x-icon name="scissors" /></span>
                <div><strong>100+</strong><span>{{ __('site.years_tradition') }}</span></div>
            </div>
        </div>
    </div>
</section>

<section id="tailoring" class="section live-measurement-section" data-section="measurements">
    <div class="container">
        <div class="live-measurement-card">
            <div class="live-measurement-copy">
                <span class="live-badge live-glass-badge"><x-icon name="ruler" /> {{ __('site.perfect_fit_technology') }}</span>
                <h2>{{ __('site.measurements_title') }}</h2>
                <p>{{ __('site.measurements_text') }}</p>
                <div class="button-row">
                    <a class="button live-gradient-button" href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}">
                        {{ __('site.start_measuring') }} <span aria-hidden="true">→</span>
                    </a>
                    <a class="button live-outline-button" href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}">
                        {{ __('site.watch_tutorial') }}
                    </a>
                </div>
            </div>
            <div class="live-measurement-visual">
                <img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" width="1000" height="900" loading="lazy" decoding="async" alt="{{ __('site.measurement_guide') }}">
            </div>
        </div>
    </div>
</section>

<section id="heritage-content" class="section live-editorial" data-section="heritage-content">
    <div class="container">
        <div class="live-editorial-intro">
            <h2>{{ __('site.seo_heading') }}</h2>
            <p>{{ __('site.seo_intro') }}</p>
        </div>
        <div class="live-editorial-stack">
            <article>
                <h3>{{ __('site.gand_heading') }}</h3>
                <p>{{ __('site.gand_text') }}</p>
            </article>
            <article>
                <h3>{{ __('site.embroidery_heading') }}</h3>
                <p>{{ __('site.embroidery_text') }}</p>
            </article>
            <article>
                <h3>{{ __('site.tailoring_heading') }}</h3>
                <p>{{ __('site.tailoring_text') }}</p>
            </article>
        </div>
        <div class="button-row live-editorial-actions">
            <a class="button live-gradient-button" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop_afghan_clothes') }}</a>
            <a class="button button-secondary" href="#tailoring">{{ __('site.start_custom_tailoring') }}</a>
        </div>
    </div>
</section>

<section class="section live-testimonials" data-section="testimonials" aria-labelledby="testimonials-title">
    <div class="container">
        <div class="live-section-heading live-section-heading-centered">
            <h2 id="testimonials-title">{{ __('site.testimonials_title') }}</h2>
            <p>{{ __('site.testimonials_intro') }}</p>
        </div>
        <div class="testimonial-grid">
            <figure class="testimonial-card live-review-card">
                <div class="live-stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>“{{ __('site.review_1_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">A</span><div><strong>Ahmad K.</strong><small>Dubai, UAE</small></div></figcaption>
            </figure>
            <figure class="testimonial-card live-review-card">
                <div class="live-stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>“{{ __('site.review_2_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">S</span><div><strong>Sarah M.</strong><small>London, UK</small></div></figcaption>
            </figure>
            <figure class="testimonial-card live-review-card">
                <div class="live-stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>“{{ __('site.review_3_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">F</span><div><strong>Farid A.</strong><small>Toronto, Canada</small></div></figcaption>
            </figure>
        </div>
    </div>
</section>
@endsection
