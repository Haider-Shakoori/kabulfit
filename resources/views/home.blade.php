@extends('layouts.app')

@section('content')
@php
    $heroSlides = [
        [
            'title' => __('site.hero_title'),
            'subtitle' => __('site.hero_subtitle'),
            'image' => 'https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/1cce53a01_2.png',
            'cta' => __('site.shop_now'),
            'href' => route('shop', ['locale' => app()->getLocale()]),
        ],
        [
            'title' => app()->getLocale() === 'ps' ? 'دودیزه ښکلا' : (app()->getLocale() === 'fa' ? 'ظرافت سنتی' : 'Traditional Elegance'),
            'subtitle' => __('site.story_p1'),
            'image' => 'https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/58f1df170_2.png',
            'cta' => __('site.explore_collection'),
            'href' => route('shop', ['locale' => app()->getLocale()]),
        ],
        [
            'title' => app()->getLocale() === 'ps' ? 'د مناسب فټ تضمین' : (app()->getLocale() === 'fa' ? 'تضمین اندازه مناسب' : 'Custom Fit Guarantee'),
            'subtitle' => __('site.measurements_text'),
            'image' => 'https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/1cce53a01_2.png',
            'cta' => __('site.measurement_guide'),
            'href' => '#tailoring',
        ],
    ];
    $categoryFallbacks = [
        asset('images/kabulfit-live/catalog/category-3ed66ce35408.webp'),
        asset('images/kabulfit-live/catalog/category-861c19f93a16.webp'),
        asset('images/kabulfit-live/catalog/category-15e0668e6ecf.png'),
        asset('images/kabulfit-live/catalog/product-e3216ebea370.webp'),
    ];
@endphp

<section class="relative h-screen overflow-hidden" data-section="hero"
    x-data='{ slides: @js($heroSlides), slide: 0 }'
    x-init="setInterval(() => slide = (slide + 1) % slides.length, 6000)">
    <template x-for="(item, index) in slides" :key="index">
        <div class="absolute inset-0 transition-opacity duration-1000" :class="slide === index ? 'opacity-100' : 'opacity-0'">
            <img :src="item.image" :alt="item.title" class="h-full w-full object-cover object-center" :loading="index === 0 ? 'eager' : 'lazy'" :fetchpriority="index === 0 ? 'high' : 'low'">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent rtl:bg-gradient-to-l"></div>
        </div>
    </template>

    <div class="absolute inset-0 z-20 flex items-center">
        <div class="mx-auto w-full max-w-7xl px-4">
            <div class="mt-16 max-w-2xl sm:mt-20">
                <span class="mb-4 inline-flex items-center gap-1 rounded-full border-0 bg-gradient-to-r from-[#881C27] to-[#2A6867] px-3 py-1 text-xs font-semibold text-white sm:mb-6 sm:text-sm">
                    ✦ {{ __('site.featured_label') }}
                </span>
                <h1 class="mb-4 text-3xl font-bold leading-tight text-white sm:mb-6 sm:text-4xl md:text-5xl lg:text-7xl" x-text="slides[slide]?.title || @js(__('site.hero_title'))"></h1>
                <p class="mb-6 text-base text-white/80 sm:mb-8 sm:text-lg md:text-xl lg:text-2xl" x-text="slides[slide]?.subtitle || @js(__('site.hero_subtitle'))"></p>
                <div class="flex flex-wrap gap-3 sm:gap-4">
                    <a :href="slides[slide]?.href || @js(route('shop', ['locale' => app()->getLocale()]))" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90 sm:px-7 sm:text-base">
                        <span x-text="slides[slide]?.cta || @js(__('site.shop_now'))"></span>
                        <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                    </a>
                    <a href="#tailoring" class="inline-flex items-center gap-2 rounded-full border-2 border-white bg-transparent px-5 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-black sm:px-7 sm:text-base">
                        <x-icon name="ruler" class="h-4 w-4" />
                        {{ __('site.measurement_guide') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-8 left-1/2 z-30 flex -translate-x-1/2 gap-2">
        <template x-for="(item, index) in slides" :key="'dot-' + index">
            <button type="button" class="h-2.5 rounded-full bg-white transition-all" :class="slide === index ? 'w-8 opacity-100' : 'w-2.5 opacity-50'" @click="slide = index" :aria-label="'Go to slide ' + (index + 1)"></button>
        </template>
    </div>
    <button type="button" class="absolute bottom-7 start-4 z-30 grid h-11 w-11 place-items-center rounded-full border border-white/30 bg-black/20 text-xl text-white backdrop-blur sm:start-8" @click="slide = slide === 0 ? slides.length - 1 : slide - 1" aria-label="Previous slide">‹</button>
    <button type="button" class="absolute bottom-7 end-4 z-30 grid h-11 w-11 place-items-center rounded-full border border-white/30 bg-black/20 text-xl text-white backdrop-blur sm:end-8" @click="slide = slide === slides.length - 1 ? 0 : slide + 1" aria-label="Next slide">›</button>
</section>

<section class="border-b border-gray-100 bg-white py-8" data-section="trust">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 md:grid-cols-4">
        @foreach([
            ['ruler', __('site.custom_sizing'), __('site.custom_sizing_text')],
            ['truck', __('site.global_shipping'), __('site.global_shipping_text')],
            ['shield', __('site.quality_assured'), __('site.quality_assured_text')],
            ['star', __('site.handcrafted'), __('site.handcrafted_text')],
        ] as [$icon, $title, $desc])
            <div class="flex items-center gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10"><x-icon :name="$icon" class="h-6 w-6 text-[#881C27]" /></span>
                <div><strong class="block text-gray-900">{{ $title }}</strong><span class="text-sm text-gray-500">{{ $desc }}</span></div>
            </div>
        @endforeach
    </div>
</section>

<section id="categories" class="py-20" data-section="categories">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2 class="text-4xl font-bold tracking-tight text-gray-900">{{ __('site.shop_by_category') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('site.category_intro') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4 lg:gap-8">
            @foreach($categories->take(4) as $category)
                @php($translation = $category->translation())
                <a href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" class="group relative h-[300px] overflow-hidden rounded-[2rem] sm:h-[400px] lg:h-[500px] lg:rounded-[3rem]">
                    <img src="{{ $categoryFallbacks[$loop->index % count($categoryFallbacks)] }}" alt="{{ $translation?->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-4 text-white sm:p-6 lg:p-8">
                        <h3 class="text-xl font-bold sm:text-2xl lg:text-3xl">{{ $translation?->name }}</h3>
                        <span class="mt-2 inline-flex items-center gap-1 text-sm font-medium sm:text-base">{{ __('site.explore_collection') }} <x-icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:rotate-180" /></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20" data-section="featured">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 flex items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-bold tracking-tight text-gray-900">{{ __('site.featured_label') }}</h2>
                <p class="mt-2 text-gray-600">{{ __('site.featured_title') }}</p>
            </div>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}" class="hidden items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white sm:inline-flex">{{ __('site.view_all') }} <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" /></a>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-gray-200 py-16 text-center text-gray-500">{{ __('site.no_featured') }}</div>
            @endforelse
        </div>
    </div>
</section>

<section id="story" class="relative overflow-hidden py-20 text-white" data-section="story">
    <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/1cce53a01_2.png" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
    <div class="absolute inset-0 bg-black/85"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 md:grid-cols-2">
        <div>
            <span class="inline-flex rounded-full bg-[#2A6867] px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">{{ __('site.our_story') }}</span>
            <h2 class="mt-6 text-4xl font-bold tracking-tight md:text-5xl">{{ __('site.afghan_culture') }}</h2>
            <p class="mt-5 text-xl leading-8 text-white/80">{{ __('site.story_p1') }}</p>
            <p class="mt-4 leading-7 text-white/70">{{ __('site.story_p2') }}</p>
            <a href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')]) }}" class="mt-8 inline-flex items-center gap-2 rounded-full border-2 border-white px-6 py-3 font-semibold text-white transition hover:bg-white hover:text-black">{{ __('site.learn_more') }} <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" /></a>
        </div>
        <div class="relative">
            <div class="aspect-square overflow-hidden rounded-[3rem]">
                <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/f065351e6_bn.jpg" alt="{{ __('site.craftsmanship_alt') }}" class="h-full w-full object-cover" loading="lazy">
            </div>
            <div class="absolute -bottom-6 -start-6 rounded-[2rem] bg-white p-6 text-gray-900 shadow-xl">
                <div class="flex items-center gap-4">
                    <span class="grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10"><x-icon name="star" class="h-8 w-8 text-[#881C27]" /></span>
                    <div><strong class="block text-3xl">100+</strong><span class="text-gray-500">{{ __('site.years_tradition') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($bestSellers->isNotEmpty())
<section class="bg-gradient-to-b from-[#FDF5E6] to-[#FDFBF7] py-20" data-section="bestsellers">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-[#2A6867] px-4 py-2 text-xs font-semibold text-white"><x-icon name="star" class="h-4 w-4" />{{ app()->getLocale() === 'ps' ? 'لوړ درجه' : (app()->getLocale() === 'fa' ? 'بالاترین امتیاز' : 'Top Rated') }}</span>
            <h2 class="mt-4 text-4xl font-bold text-gray-900">{{ app()->getLocale() === 'ps' ? 'غوره پلورل شوي' : (app()->getLocale() === 'fa' ? 'پرفروش‌ترین‌ها' : 'Best Sellers') }}</h2>
            <p class="mt-3 text-gray-600">{{ app()->getLocale() === 'ps' ? 'د مشتریانو خوښې' : (app()->getLocale() === 'fa' ? 'محبوب مشتریان' : 'Customer favorites') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
            @foreach($bestSellers as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endif

<section id="tailoring" class="py-20" data-section="measurements">
    <div class="mx-auto max-w-7xl px-4">
        <div class="overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white shadow-2xl">
            <div class="grid md:grid-cols-2">
                <div class="flex flex-col justify-center p-8 md:p-12">
                    <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm"><x-icon name="ruler" class="h-4 w-4" />{{ __('site.perfect_fit_technology') }}</span>
                    <h2 class="mt-6 text-3xl font-bold md:text-4xl">{{ __('site.measurements_title') }}</h2>
                    <p class="mt-4 text-white/80">{{ __('site.measurements_text') }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white shadow-lg">{{ __('site.start_measuring') }} <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" /></a>
                        <a href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}" class="inline-flex rounded-full border-2 border-white px-6 py-3 font-semibold text-white transition hover:bg-white hover:text-gray-900">{{ __('site.watch_tutorial') }}</a>
                    </div>
                </div>
                <div class="relative min-h-[400px]">
                    <img src="https://qtrypzzcjebvfcihiynt.supabase.co/storage/v1/object/public/base44-prod/public/6944d141c2878421ef544832/58f1df170_2.png" alt="{{ __('site.measurement_guide') }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="heritage-content" class="bg-white py-20" data-section="heritage-content">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="text-4xl font-bold tracking-tight">{{ __('site.seo_heading') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('site.seo_intro') }}</p>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach([
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

<section class="bg-white py-20" data-section="testimonials" aria-labelledby="testimonials-title">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-12 text-center">
            <h2 id="testimonials-title" class="text-4xl font-bold text-gray-900">{{ __('site.testimonials_title') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('site.testimonials_intro') }}</p>
        </div>
        <div class="grid gap-8 md:grid-cols-3">
            @foreach([
                ['Ahmad K.', 'Dubai, UAE', __('site.review_1_quote')],
                ['Sarah M.', 'London, UK', __('site.review_2_quote')],
                ['Farid A.', 'Toronto, Canada', __('site.review_3_quote')],
            ] as [$name, $location, $quote])
                <article class="h-full rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex gap-1 text-[#2A6867]">★★★★★</div>
                    <p class="mb-6 italic text-gray-700">“{{ $quote }}”</p>
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10 font-bold text-[#881C27]">{{ mb_substr($name, 0, 1) }}</span>
                        <div><strong class="block text-gray-900">{{ $name }}</strong><span class="text-sm text-gray-500">{{ $location }}</span></div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
