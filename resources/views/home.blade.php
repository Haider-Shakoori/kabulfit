@extends('layouts.app')

@section('content')
<section class="hero" data-section="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">{{ __('site.heritage_label') }}</p>
            <h1>{{ __('site.hero_title') }}</h1>
            <p class="hero-lead">{{ __('site.hero_subtitle') }}</p>
            <div class="button-row">
                <a class="button button-primary" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop_now') }}</a>
                <a class="button button-secondary" href="#tailoring">{{ __('site.measurement_guide') }}</a>
            </div>
            <div class="hero-proof" aria-label="{{ __('site.service_highlights') }}">
                <span><strong>100+</strong> {{ __('site.years_tradition') }}</span>
                <span><strong>3</strong> {{ __('site.languages_supported') }}</span>
            </div>
        </div>
        <div class="hero-visual">
            <img src="{{ asset('images/kabulfit-hero-textile.svg') }}" width="960" height="1120" alt="{{ __('site.hero_art_alt') }}" fetchpriority="high" decoding="async">
            <div class="hero-floating-card">
                <span class="hero-floating-icon"><x-icon name="scissors" /></span>
                <div><strong>{{ __('site.custom_sizing') }}</strong><small>{{ __('site.perfect_fit_guarantee') }}</small></div>
            </div>
        </div>
    </div>
</section>

<section class="trust-strip" data-section="trust" aria-label="{{ __('site.service_highlights') }}">
    <div class="container trust-grid">
        <div class="trust-item">
            <span class="trust-icon"><x-icon name="ruler" /></span>
            <div><strong>{{ __('site.custom_sizing') }}</strong><span>{{ __('site.custom_sizing_text') }}</span></div>
        </div>
        <div class="trust-item">
            <span class="trust-icon"><x-icon name="globe" /></span>
            <div><strong>{{ __('site.global_shipping') }}</strong><span>{{ __('site.global_shipping_text') }}</span></div>
        </div>
        <div class="trust-item">
            <span class="trust-icon"><x-icon name="quality" /></span>
            <div><strong>{{ __('site.quality_assured') }}</strong><span>{{ __('site.quality_assured_text') }}</span></div>
        </div>
    </div>
</section>

<section id="categories" class="section category-section" data-section="categories">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">{{ __('site.discover') }}</p><h2>{{ __('site.shop_by_category') }}</h2></div>
            <p>{{ __('site.category_intro') }}</p>
        </div>
        <div class="category-grid">
            @foreach ($categories as $category)
                @php($translation = $category->translation())
                <a class="category-card category-card-{{ $loop->iteration }}" href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">
                    <span class="category-motif" aria-hidden="true"><span></span></span>
                    <span class="category-number">0{{ $loop->iteration }}</span>
                    <div class="category-copy">
                        <h3>{{ $translation?->name }}</h3>
                        <p>{{ $translation?->description }}</p>
                        <span class="text-link">{{ __('site.explore_collection') }} <span aria-hidden="true">→</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft" data-section="featured">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">{{ __('site.featured_label') }}</p><h2>{{ __('site.featured_title') }}</h2></div>
            <a class="text-link" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.view_all') }} <span aria-hidden="true">→</span></a>
        </div>
        <div class="product-grid">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="empty-state">
                    <p>{{ __('site.no_featured') }}</p>
                    <a class="button button-secondary" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.browse_all_products') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section id="story" class="section story-section" data-section="story">
    <div class="container story-grid">
        <div class="story-visual">
            <img src="{{ asset('images/kabulfit-craftsmanship.svg') }}" width="1200" height="900" loading="lazy" decoding="async" alt="{{ __('site.craftsmanship_alt') }}">
            <div class="story-stat"><strong>100+</strong><span>{{ __('site.years_tradition') }}</span></div>
        </div>
        <div class="story-copy">
            <p class="eyebrow">{{ __('site.our_story') }}</p>
            <h2>{{ __('site.afghan_culture') }}</h2>
            <p>{{ __('site.story_p1') }}</p>
            <p>{{ __('site.story_p2') }}</p>
            <a class="text-link text-link-large" href="#heritage-content">{{ __('site.learn_more') }} <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section id="tailoring" class="section tailoring-section" data-section="measurements">
    <div class="container tailoring-grid">
        <div class="tailoring-copy">
            <p class="eyebrow">{{ __('site.perfect_fit_technology') }}</p>
            <h2>{{ __('site.measurements_title') }}</h2>
            <p>{{ __('site.measurements_text') }}</p>
            <div class="button-row">
                <a class="button button-gold" href="#measurement-guide">{{ __('site.start_measuring') }}</a>
                <span class="button button-dark-ghost" aria-disabled="true" title="{{ __('site.video_coming_soon') }}">{{ __('site.watch_tutorial') }}</span>
            </div>
        </div>
        <div id="measurement-guide" class="measurement-card" aria-label="{{ __('site.measurement_guide') }}">
            <div class="measurement-icon"><x-icon name="ruler" /></div>
            <ol class="measurement-steps">
                <li><span>01</span><div><strong>{{ __('site.measurement_step_1') }}</strong><small>{{ __('site.measurement_step_1_text') }}</small></div></li>
                <li><span>02</span><div><strong>{{ __('site.measurement_step_2') }}</strong><small>{{ __('site.measurement_step_2_text') }}</small></div></li>
                <li><span>03</span><div><strong>{{ __('site.measurement_step_3') }}</strong><small>{{ __('site.measurement_step_3_text') }}</small></div></li>
            </ol>
        </div>
    </div>
</section>

<section id="heritage-content" class="section prose-section" data-section="heritage-content">
    <div class="container editorial-grid">
        <div class="editorial-heading">
            <p class="eyebrow">{{ __('site.heritage_label') }}</p>
            <h2>{{ __('site.seo_heading') }}</h2>
            <p>{{ __('site.seo_intro') }}</p>
            <div class="button-row">
                <a class="button button-primary" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop_afghan_clothes') }}</a>
                <a class="button button-secondary" href="#tailoring">{{ __('site.start_custom_tailoring') }}</a>
            </div>
        </div>
        <div class="editorial-stack">
            <article>
                <span class="editorial-number">01</span>
                <h3>{{ __('site.gand_heading') }}</h3>
                <p>{{ __('site.gand_text') }}</p>
            </article>
            <article>
                <span class="editorial-number">02</span>
                <h3>{{ __('site.embroidery_heading') }}</h3>
                <p>{{ __('site.embroidery_text') }}</p>
            </article>
            <article>
                <span class="editorial-number">03</span>
                <h3>{{ __('site.tailoring_heading') }}</h3>
                <p>{{ __('site.tailoring_text') }}</p>
            </article>
        </div>
    </div>
</section>

<section class="section testimonials-section" data-section="testimonials" aria-labelledby="testimonials-title">
    <div class="container">
        <div class="section-heading centered-heading">
            <div><p class="eyebrow">{{ __('site.customer_stories') }}</p><h2 id="testimonials-title">{{ __('site.testimonials_title') }}</h2></div>
            <p>{{ __('site.testimonials_intro') }}</p>
        </div>
        <div class="testimonial-grid">
            <figure class="testimonial-card">
                <blockquote>“{{ __('site.review_1_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">A</span><div><strong>Ahmad K.</strong><small>Dubai, UAE</small></div></figcaption>
            </figure>
            <figure class="testimonial-card">
                <blockquote>“{{ __('site.review_2_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">S</span><div><strong>Sarah M.</strong><small>London, UK</small></div></figcaption>
            </figure>
            <figure class="testimonial-card">
                <blockquote>“{{ __('site.review_3_quote') }}”</blockquote>
                <figcaption><span class="avatar" aria-hidden="true">F</span><div><strong>Farid A.</strong><small>Toronto, Canada</small></div></figcaption>
            </figure>
        </div>
    </div>
</section>

<section class="closing-cta" data-section="closing-cta">
    <div class="container closing-cta-inner">
        <div>
            <p class="eyebrow">{{ __('site.made_for_you') }}</p>
            <h2>{{ __('site.closing_title') }}</h2>
        </div>
        <a class="button button-gold" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop_now') }}</a>
    </div>
</section>
@endsection
