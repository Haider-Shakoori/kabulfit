@extends('layouts.app')

@section('content')
@php($heroSlides = [
    [
        'title' => __('site.hero_title'),
        'subtitle' => __('site.hero_subtitle'),
        'image' => 'https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/1cce53a01_2.png',
        'cta' => __('site.shop_now'),
        'href' => route('shop', ['locale' => app()->getLocale()]),
    ],
    [
        'title' => __('site.afghan_culture'),
        'subtitle' => __('site.story_p1'),
        'image' => 'https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/58f1df170_2.png',
        'cta' => __('site.explore_collection'),
        'href' => route('shop', ['locale' => app()->getLocale()]),
    ],
    [
        'title' => __('site.measurements_title'),
        'subtitle' => __('site.measurements_text'),
        'image' => asset('images/kabulfit-live/measurement-guide.png'),
        'cta' => __('site.measurement_guide'),
        'href' => '#tailoring',
    ],
])
<section
    class="relative min-h-screen overflow-hidden"
    x-data="{ slide: 0, count: {{ count($heroSlides) }} }"
    x-init="setInterval(() => slide = (slide + 1) % count, 6000)"
>
    @foreach($heroSlides as $index => $slide)
        <div class="absolute inset-0 transition-opacity duration-1000"
             x-show="slide === {{ $index }}"
             x-transition.opacity.duration.1000ms>
            <img
                src="{{ $slide['image'] }}"
                alt="{{ $slide['title'] }}"
                class="h-full w-full object-cover"
                width="1600"
                height="1000"
                {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}
            >
            <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-transparent rtl:bg-gradient-to-l"></div>
        </div>
    @endforeach

    <div class="relative z-10 mx-auto flex min-h-screen max-w-7xl items-center px-4 pt-28">
        @foreach($heroSlides as $index => $slide)
            <div class="max-w-3xl text-white" x-show="slide === {{ $index }}" x-transition>
                <span class="inline-flex rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] backdrop-blur">{{ __('site.featured_label') }}</span>
                <h1 class="mt-6 text-5xl font-bold leading-[0.98] tracking-tight sm:text-6xl lg:text-7xl">{{ $slide['title'] }}</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">{{ $slide['subtitle'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $slide['href'] }}" class="inline-flex min-h-12 items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white shadow-lg transition hover:scale-[1.02]">
                        {{ $slide['cta'] }}
                        <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                    </a>
                    <a href="#tailoring" class="inline-flex min-h-12 items-center gap-2 rounded-full border border-white/70 bg-white/10 px-6 py-3 font-semibold text-white backdrop-blur transition hover:bg-white hover:text-gray-900">
                        <x-icon name="ruler" class="h-4 w-4" />
                        {{ __('site.measurement_guide') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="absolute bottom-8 start-4 z-20 grid h-11 w-11 place-items-center rounded-full border border-white/30 bg-black/20 text-white backdrop-blur sm:start-8" @click="slide = (slide - 1 + count) % count" aria-label="Previous slide">‹</button>
    <button type="button" class="absolute bottom-8 end-4 z-20 grid h-11 w-11 place-items-center rounded-full border border-white/30 bg-black/20 text-white backdrop-blur sm:end-8" @click="slide = (slide + 1) % count" aria-label="Next slide">›</button>
    <div class="absolute bottom-10 left-1/2 z-20 flex -translate-x-1/2 gap-2">
        @foreach($heroSlides as $index => $slide)
            <button type="button" class="h-2.5 rounded-full bg-white transition-all" :class="slide === {{ $index }} ? 'w-8 opacity-100' : 'w-2.5 opacity-50'" @click="slide = {{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
</section>

<section class="border-b border-gray-100 bg-white py-8">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 md:grid-cols-4">
        @foreach ([
            ['ruler', __('site.custom_sizing'), __('site.custom_sizing_text')],
            ['globe', __('site.global_shipping'), __('site.global_shipping_text')],
            ['quality', __('site.quality_assured'), __('site.quality_assured_text')],
            ['scissors', __('site.handcrafted'), __('site.handcrafted_text')],
        ] as [$icon, $title, $description])
            <div class="flex items-center gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10">
                    <x-icon :name="$icon" class="h-6 w-6 text-[#881C27]" />
                </span>
                <div>
                    <h2 class="font-semibold text-gray-900">{{ $title }}</h2>
                    <p class="text-sm text-gray-500">{{ $description }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section id="categories" class="py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2 class="text-4xl font-bold tracking-tight text-gray-900">{{ __('site.shop_by_category') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('site.category_intro') }}</p>
        </div>

        @php($categoryFallbacks = [
            asset('images/kabulfit-live/catalog/category-3ed66ce35408.webp'),
            asset('images/kabulfit-live/catalog/category-861c19f93a16.webp'),
            asset('images/kabulfit-live/catalog/category-15e0668e6ecf.png'),
            asset('images/kabulfit-live/catalog/product-e3216ebea370.webp'),
        ])
        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4 lg:gap-8">
            @foreach($categories->take(4) as $category)
                @php($translation = $category->translation())
                <a href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}"
                   class="group relative h-[300px] overflow-hidden rounded-[2rem] sm:h-[400px] lg:h-[500px] lg:rounded-[3rem]">
                    <img src="{{ $categoryFallbacks[$loop->index % count($categoryFallbacks)] }}" alt="{{ $translation?->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-4 text-white sm:p-6 lg:p-8">
                        <h3 class="text-xl font-bold sm:text-2xl lg:text-3xl">{{ $translation?->name }}</h3>
                        <span class="mt-2 inline-flex items-center gap-1 text-sm font-medium sm:text-base">
                            {{ __('site.explore_collection') }}
                            <x-icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:rotate-180" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 flex items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-bold tracking-tight text-gray-900">{{ __('site.featured_label') }}</h2>
                <p class="mt-2 text-gray-600">{{ __('site.featured_title') }}</p>
            </div>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}" class="hidden items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90 sm:inline-flex">
                {{ __('site.view_all') }}
                <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-gray-200 py-16 text-center">
                    <p class="text-gray-500">{{ __('site.no_featured') }}</p>
                    <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}" class="mt-5 inline-flex rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white">{{ __('site.browse_all_products') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section id="story" class="relative overflow-hidden bg-[#111827] py-20 text-white">
    <img src="{{ asset('images/kabulfit-live/hero-heritage.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-20" loading="lazy">
    <div class="absolute inset-0 bg-gradient-to-r from-[#111827] via-[#111827]/90 to-[#2A6867]/70"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 lg:grid-cols-2">
        <div>
            <span class="inline-flex rounded-full bg-[#2A6867] px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">{{ __('site.our_story') }}</span>
            <h2 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl">{{ __('site.afghan_culture') }}</h2>
            <p class="mt-5 text-lg leading-8 text-white/80">{{ __('site.story_p1') }}</p>
            <p class="mt-4 leading-7 text-white/65">{{ __('site.story_p2') }}</p>
            <a href="#heritage-content" class="mt-7 inline-flex items-center gap-2 rounded-full border border-white/40 px-5 py-3 font-semibold transition hover:bg-white hover:text-gray-900">{{ __('site.learn_more') }} <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" /></a>
        </div>
        <div class="relative">
            <img src="{{ asset('images/kabulfit-live/craftsmanship.jpg') }}" alt="{{ __('site.craftsmanship_alt') }}" class="aspect-square w-full rounded-[2.5rem] object-cover shadow-2xl" loading="lazy">
            <div class="absolute -bottom-6 start-6 flex items-center gap-4 rounded-2xl bg-white p-4 text-gray-900 shadow-xl">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-[#881C27]/10"><x-icon name="scissors" class="h-5 w-5 text-[#881C27]" /></span>
                <div><strong class="block text-xl">100+</strong><span class="text-sm text-gray-500">{{ __('site.years_tradition') }}</span></div>
            </div>
        </div>
    </div>
</section>

<section id="tailoring" class="py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#881C27] to-[#2A6867] text-white shadow-2xl">
            <div class="grid items-center lg:grid-cols-2">
                <div class="p-8 sm:p-12 lg:p-16">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-sm backdrop-blur"><x-icon name="ruler" class="h-4 w-4" />{{ __('site.perfect_fit_technology') }}</span>
                    <h2 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl">{{ __('site.measurements_title') }}</h2>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-white/75">{{ __('site.measurements_text') }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-[#881C27]">{{ __('site.start_measuring') }} <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" /></a>
                        <a href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}" class="inline-flex rounded-full border border-white/50 px-6 py-3 font-semibold text-white">{{ __('site.watch_tutorial') }}</a>
                    </div>
                </div>
                <div class="h-full min-h-[420px]">
                    <img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" alt="{{ __('site.measurement_guide') }}" class="h-full w-full object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="heritage-content" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="text-4xl font-bold tracking-tight">{{ __('site.seo_heading') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('site.seo_intro') }}</p>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach ([
                [__('site.gand_heading'), __('site.gand_text')],
                [__('site.embroidery_heading'), __('site.embroidery_text')],
                [__('site.tailoring_heading'), __('site.tailoring_text')],
            ] as [$title, $text])
                <article class="rounded-[2rem] border border-gray-100 bg-[#FDFBF7] p-7 shadow-sm">
                    <h3 class="text-xl font-semibold">{{ $title }}</h3>
                    <p class="mt-3 leading-7 text-gray-600">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
