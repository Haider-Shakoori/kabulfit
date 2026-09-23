@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $measurementSlug = $locale === 'en' ? 'measurement-guide' : ($locale === 'fa' ? 'راهنمای-اندازه-گیری' : 'د-اندازې-لارښود');
    $aboutSlug = $locale === 'en' ? 'about' : ($locale === 'fa' ? 'درباره' : 'زموږ-په-اړه');

    $heroSlides = [
        [
            'image' => 'images/kabulfit-live/exact/hero-01.webp',
            'title' => __('site.hero_title'),
            'subtitle' => __('site.hero_subtitle'),
            'cta' => __('site.shop_now'),
            'href' => route('shop', ['locale' => $locale]),
        ],
        [
            'image' => 'images/kabulfit-live/exact/hero-02.webp',
            'title' => __('site.traditional_elegance'),
            'subtitle' => __('site.traditional_elegance_subtitle'),
            'cta' => __('site.explore_collection'),
            'href' => route('shop', ['locale' => $locale]),
        ],
        [
            'image' => 'images/kabulfit-live/exact/hero-03.webp',
            'title' => __('site.custom_fit_guarantee'),
            'subtitle' => __('site.custom_fit_guarantee_subtitle'),
            'cta' => __('site.measurement_guide'),
            'href' => route('content.page', ['locale' => $locale, 'slug' => $measurementSlug]),
        ],
    ];

    $liveCategories = [
        ['name' => __('site.men'), 'query' => 'men', 'image' => 'images/kabulfit-live/exact/category-men.webp'],
        ['name' => __('site.women'), 'query' => 'women', 'image' => 'images/kabulfit-live/exact/category-women.webp'],
        ['name' => __('site.boys'), 'query' => 'boys', 'image' => 'images/kabulfit-live/exact/category-boys.png'],
        ['name' => __('site.girls'), 'query' => 'girls', 'image' => 'images/kabulfit-live/exact/category-girls.webp'],
    ];
@endphp

<div
    class="min-h-screen bg-[#FDFBF7]"
    x-data='{ hero: 0, slides: @json($heroSlides, JSON_HEX_APOS | JSON_HEX_QUOT) }'
    x-init="setInterval(() => hero = (hero + 1) % slides.length, 6000)"
>
    <section class="relative h-screen overflow-hidden" data-section="hero">
        @foreach ($heroSlides as $index => $slide)
            <div
                x-show="hero === {{ $index }}"
                x-transition.opacity.duration.1000ms
                class="absolute inset-0"
                @if($index !== 0) x-cloak @endif
            >
                <div class="absolute inset-0 z-10 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>
                <img
                    src="{{ asset($slide['image']) }}"
                    alt="{{ $slide['title'] }}"
                    class="h-full w-full object-cover object-[center_50%]"
                    @if($index === 0) fetchpriority="high" @else loading="lazy" @endif
                    decoding="async"
                >
            </div>
        @endforeach

        <div class="absolute inset-0 z-20 flex items-center">
            <div class="mx-auto w-full max-w-7xl px-4">
                <div class="mt-16 max-w-2xl translate-y-[18px] sm:mt-20">
                    <span class="mb-4 inline-flex items-center rounded-md border-0 bg-gradient-to-r from-[#881C27] to-[#2A6867] px-3 py-1 text-xs font-semibold text-white shadow transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover:bg-primary/80 sm:mb-6 sm:text-sm">
                        <x-icon name="sparkles" class="!mr-1 !h-3 !w-3 sm:!mr-2 sm:!h-4 sm:!w-4" />
                        {{ __('site.featured_label') }}
                    </span>

                    <h1
                        class="mb-4 text-3xl font-bold leading-tight text-white sm:mb-6 sm:text-4xl md:text-5xl lg:text-7xl lg:leading-none"
                        x-text="slides[hero].title"
                    >{{ $heroSlides[0]['title'] }}</h1>

                    <p
                        class="mb-6 text-base text-white/80 sm:mb-8 sm:text-lg md:text-xl lg:text-2xl"
                        x-text="slides[hero].subtitle"
                    >{{ $heroSlides[0]['subtitle'] }}</p>

                    <div class="flex flex-wrap gap-3 sm:gap-4">
                        <a
                            :href="slides[hero].href"
                            href="{{ $heroSlides[0]['href'] }}"
                            class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-3 text-sm font-medium text-white shadow transition-colors hover:bg-primary/90 hover:opacity-90 sm:px-6 sm:py-5 sm:text-base md:px-8 md:py-6 md:text-lg"
                        >
                            <span x-text="slides[hero].cta">{{ $heroSlides[0]['cta'] }}</span>
                            <x-icon name="arrow-right" class="!ml-1 !h-4 !w-4 sm:!ml-2 sm:!h-5 sm:!w-5" />
                        </a>

                        <a
                            href="{{ route('content.page', ['locale' => $locale, 'slug' => $measurementSlug]) }}"
                            class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-full border-2 border-white bg-transparent px-4 py-3 text-sm font-medium text-white shadow transition-colors hover:bg-white hover:text-[#000000] sm:px-6 sm:py-5 sm:text-base md:px-8 md:py-6 md:text-lg"
                        >
                            <x-icon name="ruler" class="!mr-1 !h-4 !w-4 sm:!mr-2 sm:!h-5 sm:!w-5" />
                            {{ __('site.measurement_guide') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 z-30 flex -translate-x-1/2 gap-3">
            @foreach ($heroSlides as $index => $slide)
                <button
                    type="button"
                    @click="hero = {{ $index }}"
                    class="h-3 rounded-full transition-all"
                    :class="hero === {{ $index }} ? 'w-8 bg-gradient-to-r from-[#881C27] to-[#2A6867]' : 'w-3 bg-white/50'"
                    aria-label="{{ __('site.hero_slide', ['number' => $index + 1]) }}"
                ></button>
            @endforeach
        </div>

        <div class="absolute bottom-0 left-0 right-0 z-20 h-24 bg-gradient-to-t from-[#FDFBF7] to-transparent"></div>
    </section>

    <section class="border-y border-gray-100 bg-white py-12" data-section="trust" aria-label="{{ __('site.service_highlights') }}">
        <div class="mx-auto max-w-7xl px-4">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                @foreach ([
                    ['ruler', __('site.custom_sizing'), __('site.custom_sizing_text')],
                    ['truck', __('site.global_shipping'), __('site.global_shipping_text')],
                    ['shield', __('site.quality_assured'), __('site.quality_assured_text')],
                    ['sparkles', __('site.handcrafted'), __('site.handcrafted_text')],
                ] as [$icon, $title, $description])
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10">
                            <x-icon :name="$icon" class="!h-6 !w-6 text-[#881C27]" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $title }}</h3>
                            <p class="text-sm text-gray-500">{{ $description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="categories" class="py-20" data-section="categories">
        <div class="mx-auto max-w-7xl px-4">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-4xl font-bold text-gray-900">{{ __('site.shop_by_category') }}</h2>
                <p class="mx-auto max-w-2xl text-gray-600">{{ __('site.category_intro') }}</p>
            </div>

            <div class="grid grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                @foreach ($liveCategories as $category)
                    <div>
                        <a href="{{ route('shop', ['locale' => $locale, 'category' => $category['query']]) }}">
                            <div class="group relative h-[300px] overflow-hidden rounded-2xl sm:h-[400px] sm:rounded-[2.5rem] md:h-[500px] md:rounded-[3rem]">
                                <div class="relative h-full w-full overflow-hidden">
                                    <img
                                        src="{{ asset($category['image']) }}"
                                        alt="{{ $category['name'] }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover opacity-100 transition-opacity duration-300 transition-transform duration-700 group-hover:scale-110"
                                    >
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" aria-hidden="true"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8">
                                    <h3 class="mb-1 text-xl font-bold text-white sm:mb-2 sm:text-2xl md:text-3xl">{{ $category['name'] }}</h3>
                                    <span class="flex items-center text-sm font-medium text-white group-hover:underline sm:text-base">
                                        {{ __('site.explore_collection') }}
                                        <x-icon name="arrow-right" class="!ml-1 !h-3 !w-3 transition-transform group-hover:translate-x-1 sm:!ml-2 sm:!h-4 sm:!w-4" />
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-20" data-section="featured">
        <div class="mx-auto max-w-7xl px-4">
            <div class="mb-12 flex items-center justify-between gap-6">
                <div>
                    <h2 class="mb-2 text-4xl font-bold text-gray-900">{{ __('site.featured_label') }}</h2>
                    <p class="text-gray-600">{{ __('site.featured_title') }}</p>
                </div>
                <a href="{{ route('shop', ['locale' => $locale]) }}">
                    <button type="button" class="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-full border-2 border-input bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground hover:opacity-90">
                        {{ __('site.view_all') }}
                    </button>
                </a>
            </div>

            @if ($featuredProducts->isNotEmpty())
                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <div class="animate-fadeInUp" style="animation-delay: {{ $loop->index * 60 }}ms">
                            <x-product-card :product="$product" :eager="$loop->first" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center">
                    <p class="mb-4 text-gray-500">{{ __('site.no_featured') }}</p>
                    <a href="{{ route('shop', ['locale' => $locale]) }}" class="inline-flex rounded-md bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 font-semibold text-white">
                        {{ __('site.browse_all_products') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section id="story" class="relative overflow-hidden py-20" data-section="story">
        <div class="absolute inset-0">
            <img src="{{ asset('images/kabulfit-live/exact/story-bg.png') }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-black/85"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4">
            <div class="grid items-center gap-12 md:grid-cols-2">
                <div>
                    <div class="mb-6 inline-flex items-center rounded-md border border-transparent bg-[#2A6867] px-2.5 py-0.5 text-xs font-semibold text-white shadow transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover:bg-primary/80">{{ __('site.our_story') }}</div>
                    <h2 class="mb-6 text-4xl font-bold text-white md:text-5xl">{{ __('site.afghan_culture') }}</h2>
                    <p class="mb-8 text-xl leading-relaxed text-white/80">{{ __('site.story_p1') }}</p>
                    <p class="mb-8 text-white/70">{{ __('site.story_p2') }}</p>
                    <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $aboutSlug]) }}" class="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-full border-2 border-white bg-transparent px-8 py-2 text-sm font-medium text-white shadow transition-colors hover:bg-white hover:text-black">
                        {{ __('site.learn_more') }} <x-icon name="arrow-right" class="!ml-2 !h-4 !w-4" />
                    </a>
                </div>

                <div class="relative">
                    <div class="aspect-square overflow-hidden rounded-[3rem]">
                        <img src="{{ asset('images/kabulfit-live/exact/craftsmanship.jpg') }}" alt="{{ __('site.craftsmanship_alt') }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                    </div>
                    <div class="absolute -bottom-3 -left-3 rounded-xl bg-white p-3 shadow-xl sm:-bottom-4 sm:-left-4 sm:rounded-[1.5rem] sm:p-4 md:-bottom-6 md:-left-6 md:rounded-[2rem] md:p-6">
                        <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10 sm:h-12 sm:w-12 md:h-16 md:w-16">
                                <x-icon name="sparkles" class="!h-5 !w-5 text-[#881C27] sm:!h-6 sm:!w-6 md:!h-8 md:!w-8" />
                            </div>
                            <div>
                                <p class="text-xl font-bold text-gray-900 sm:text-2xl md:text-3xl">100+</p>
                                <p class="text-xs text-gray-500 sm:text-sm md:text-base">{{ __('site.years_tradition') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($bestSellerProducts->isNotEmpty())
        <section class="bg-gradient-to-b from-[#FDF5E6] to-[#FDFBF7] py-20" data-section="best-sellers">
            <div class="mx-auto max-w-7xl px-4">
                <div class="mb-12 text-center">
                    <div class="mb-4 inline-flex items-center rounded-md border border-transparent bg-[#2A6867] px-2.5 py-0.5 text-xs font-semibold text-white shadow transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover:bg-primary/80">
                        <x-icon name="star" class="!mr-1 !h-4 !w-4 fill-current" />{{ __('site.top_rated') }}
                    </div>
                    <h2 class="mb-4 text-4xl font-bold text-gray-900">{{ __('site.best_sellers') }}</h2>
                    <p class="text-gray-600">{{ __('site.customers_favorite') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                    @foreach ($bestSellerProducts as $product)
                        <div class="animate-fadeInUp" style="animation-delay: {{ $loop->index * 60 }}ms">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-20" data-section="measurements" id="tailoring">
        <div class="mx-auto max-w-7xl px-4">
            <div class="overflow-hidden rounded-xl border border-white/10 bg-gradient-to-r from-[#881C27] to-[#2A6867] shadow-sm">
                <div class="grid gap-8 md:grid-cols-2">
                    <div class="flex flex-col justify-center p-8 md:p-12">
                        <span class="mb-6 inline-flex w-fit items-center rounded-md border border-transparent bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white shadow">
                            <x-icon name="ruler" class="!mr-2 !h-4 !w-4" />{{ __('site.perfect_fit_technology') }}
                        </span>
                        <h2 class="mb-4 text-3xl font-bold text-white md:text-4xl">{{ __('site.measurements_title') }}</h2>
                        <p class="mb-8 text-white/80">{{ __('site.measurements_text') }}</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $measurementSlug]) }}" class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-8 text-sm font-medium text-white shadow transition-colors hover:opacity-90">
                                {{ __('site.start_measuring') }} <x-icon name="arrow-right" class="!ml-2 !h-5 !w-5" />
                            </a>
                            <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $measurementSlug]) }}" class="inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-full border-2 border-white bg-transparent px-8 text-sm font-medium text-white shadow transition-colors hover:bg-white hover:text-gray-900">
                                <x-icon name="play" class="!mr-2 !h-5 !w-5" />{{ __('site.watch_tutorial') }}
                            </a>
                        </div>
                    </div>

                    <div class="relative h-[400px] md:h-auto">
                        <img src="{{ asset('images/kabulfit-live/exact/measurement-guide.png') }}" alt="{{ __('site.measurement_guide') }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent md:hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#FDFBF7] py-16" data-section="heritage-content" id="heritage-content">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="mb-6 text-center text-2xl font-bold text-gray-900 md:text-3xl">{{ __('site.seo_heading') }}</h2>
            <div class="prose prose-lg max-w-none space-y-4 text-gray-700">
                <p class="leading-6">{{ __('site.seo_intro') }}</p>

                <h3 class="text-xl font-bold text-gray-900">{{ __('site.gand_heading') }}</h3>
                <p class="leading-6">{{ __('site.gand_text') }}</p>

                <h3 class="text-xl font-bold text-gray-900">{{ __('site.embroidery_heading') }}</h3>
                <p class="leading-6">{{ __('site.embroidery_text') }}</p>

                <h3 class="text-xl font-bold text-gray-900">{{ __('site.tailoring_heading') }}</h3>
                <p class="leading-6">{{ __('site.tailoring_text') }}</p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('shop', ['locale' => $locale]) }}" class="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-2 text-sm font-medium text-white shadow transition hover:opacity-90">
                        {{ __('site.shop_afghan_clothes') }}
                    </a>
                    <a href="{{ route('content.page', ['locale' => $locale, 'slug' => $measurementSlug]) }}" class="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-full border border-gray-200 bg-white px-6 py-2 text-sm font-medium text-gray-900 shadow-sm transition-colors hover:bg-gray-100">
                        {{ __('site.start_custom_tailoring') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-20" data-section="testimonials" aria-labelledby="testimonials-title">
        <div class="mx-auto max-w-7xl px-4">
            <div class="mb-12 text-center">
                <h2 id="testimonials-title" class="mb-4 text-4xl font-bold text-gray-900">{{ __('site.testimonials_title') }}</h2>
                <p class="text-gray-600">{{ __('site.testimonials_intro') }}</p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                @foreach ([
                    ['Ahmad K.', 'Dubai, UAE', __('site.review_1_quote')],
                    ['Sarah M.', 'London, UK', __('site.review_2_quote')],
                    ['Farid A.', 'Toronto, Canada', __('site.review_3_quote')],
                ] as [$name, $location, $quote])
                    <div class="h-full rounded-xl border bg-card text-card-foreground shadow">
                        <div class="p-6">
                            <div class="mb-4 flex gap-1" aria-label="5 out of 5">
                                @for ($star = 0; $star < 5; $star++)
                                    <x-icon name="star" class="!h-5 !w-5 fill-[#2A6867] text-[#2A6867]" />
                                @endfor
                            </div>
                            <p class="mb-6 italic text-gray-700">"{{ $quote }}"</p>
                            <div class="flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10 font-bold text-[#881C27]" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
                                <span>
                                    <strong class="block font-semibold text-gray-900">{{ $name }}</strong>
                                    <small class="text-sm text-gray-500">{{ $location }}</small>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
