<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#881C27">
    <meta name="color-scheme" content="light">
    <title>{{ $seo->title ?? 'KabulFit' }}</title>
    <meta name="description" content="{{ $seo->description ?? '' }}">
    <meta name="robots" content="{{ $seo->robots ?? 'index,follow' }}">
    <link rel="icon" type="image/png" href="{{ asset('images/kabulfit-live/favicon.png') }}">
    <link rel="canonical" href="{{ $seo->canonical ?? url()->current() }}">
    @foreach (($seo->alternates ?? []) as $locale => $href)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $href }}">
    @endforeach
    @if (! empty(($seo->alternates ?? [])['en']))
        <link rel="alternate" hreflang="x-default" href="{{ $seo->alternates['en'] }}">
    @endif
    <meta property="og:site_name" content="KabulFit">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->title ?? 'KabulFit' }}">
    <meta property="og:description" content="{{ $seo->description ?? '' }}">
    <meta property="og:url" content="{{ $seo->canonical ?? url()->current() }}">
    <meta property="og:image" content="{{ asset('images/kabulfit-live/hero-heritage.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? 'KabulFit' }}">
    <meta name="twitter:description" content="{{ $seo->description ?? '' }}">
    <meta name="twitter:image" content="{{ asset('images/kabulfit-live/hero-heritage.png') }}">
    @if (! empty($seo->jsonLd))
        <script type="application/ld+json">{!! json_encode($seo->jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pb-16 md:pb-0">
@php
    $contactEmail = app(\App\Services\Settings\SiteSettings::class)->get('site.contact_email', 'info@kabulfit.com');
    $isHome = request()->routeIs('home');
    $locale = app()->getLocale();

    $pageSlugs = [
        'en' => [
            'about' => 'about',
            'measurement' => 'measurement-guide',
            'shipping' => 'shipping-policy',
            'return' => 'return-policy',
            'faq' => 'faq',
            'privacy' => 'privacy-policy',
            'terms' => 'terms-and-conditions',
        ],
        'fa' => [
            'about' => 'درباره',
            'measurement' => 'راهنمای-اندازه-گیری',
            'shipping' => 'سیاست-ارسال',
            'return' => 'سیاست-بازگشت',
            'faq' => 'پرسش-های-متداول',
            'privacy' => 'سیاست-حریم-خصوصی',
            'terms' => 'شرایط-و-ضوابط',
        ],
        'ps' => [
            'about' => 'زموږ-په-اړه',
            'measurement' => 'د-اندازې-لارښود',
            'shipping' => 'د-لېږد-تګلاره',
            'return' => 'د-بېرته-ستنولو-تګلاره',
            'faq' => 'ډېرې-پوښتل-شوې-پوښتنې',
            'privacy' => 'د-محرمیت-تګلاره',
            'terms' => 'شرایط-او-مقررات',
        ],
    ][$locale];

    $categorySlugs = [
        'en' => ['men' => 'men', 'women' => 'women', 'boys' => 'kids', 'girls' => 'accessories'],
        'fa' => ['men' => 'مردانه', 'women' => 'زنانه', 'boys' => 'اطفال', 'girls' => 'اکسسوری'],
        'ps' => ['men' => 'نارینه', 'women' => 'ښځینه', 'boys' => 'ماشومان', 'girls' => 'اکسسوري'],
    ][$locale];
@endphp

<a class="skip-link" href="#main-content">{{ __('site.skip_to_content') }}</a>

<header
    class="fixed inset-x-0 top-0 z-50 transition-all duration-500"
    x-data="{ open: false, searchOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 100"
    @keydown.escape.window="open = false; searchOpen = false"
>
    <div class="bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-3 text-sm text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <p class="hidden md:block">{{ __('site.announcement') }}</p>
            <div class="mx-auto flex items-center gap-1 md:mx-0" aria-label="{{ __('site.language') }}">
                @foreach (($seo->alternates ?? []) as $alternateLocale => $href)
                    <a
                        href="{{ $href }}"
                        hreflang="{{ $alternateLocale }}"
                        lang="{{ $alternateLocale }}"
                        @class([
                            'px-2 py-1 text-sm transition-all',
                            'border-b-2 border-white font-bold text-white' => $locale === $alternateLocale,
                            'text-white/70 hover:text-white' => $locale !== $alternateLocale,
                        ])
                    >
                        {{ $alternateLocale === 'en' ? 'English' : ($alternateLocale === 'fa' ? 'دری' : 'پښتو') }}
                    </a>
                    @if (! $loop->last)
                        <span class="text-white/50">|</span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div
        @class([
            'transition-all duration-500',
            'bg-white shadow-lg' => ! $isHome,
        ])
        :class="{{ $isHome ? "scrolled ? 'bg-white/95 backdrop-blur-md shadow-lg' : 'bg-transparent'" : "'bg-white shadow-lg'" }}"
    >
        <div class="mx-auto max-w-7xl px-2 py-0.5 sm:px-4 sm:py-1">
            <div class="flex items-center justify-between gap-2">
                <button
                    type="button"
                    class="grid h-10 w-10 place-items-center rounded-md lg:hidden"
                    :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    @click="open = true"
                    aria-controls="mobile-navigation"
                    :aria-expanded="open.toString()"
                    aria-label="{{ __('site.menu') }}"
                >
                    <span class="grid w-5 gap-1.5" aria-hidden="true">
                        <span class="h-0.5 rounded bg-current"></span>
                        <span class="h-0.5 rounded bg-current"></span>
                        <span class="h-0.5 rounded bg-current"></span>
                    </span>
                </button>

                <a href="{{ route('home', ['locale' => $locale]) }}" class="flex items-center gap-2" aria-label="{{ __('site.kabulfit_home') }}">
                    <img
                        src="{{ asset('images/kabulfit-live/logo-header.png') }}"
                        width="310"
                        height="120"
                        alt="KabulFit"
                        fetchpriority="high"
                        class="h-16 w-auto object-contain sm:h-20 md:h-[5.5rem] lg:h-[6.25rem]"
                    >
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('site.primary_navigation') }}">
                    <a
                        href="{{ route('home', ['locale' => $locale]) }}"
                        class="relative overflow-hidden rounded-md px-4 py-2 font-medium transition-all duration-500 hover:scale-105 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white hover:shadow-lg"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    >{{ __('site.home') }}</a>

                    <div class="group relative">
                        <a
                            href="{{ route('shop', ['locale' => $locale]) }}"
                            class="relative flex items-center gap-1 overflow-hidden rounded-md px-4 py-2 font-medium transition-all duration-500 hover:scale-105 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white hover:shadow-lg"
                            :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        >
                            {{ __('site.shop') }}
                            <span class="text-xs transition-transform duration-300 group-hover:rotate-180" aria-hidden="true">⌄</span>
                        </a>
                        <div class="invisible absolute left-1/2 top-full z-50 w-52 -translate-x-1/2 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:opacity-100">
                            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white py-2 shadow-xl">
                                @foreach ([
                                    __('site.men') => route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['men']]),
                                    __('site.women') => route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['women']]),
                                    __('site.boys') => route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['boys']]),
                                    __('site.girls') => route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['girls']]),
                                    __('site.new_arrivals') => route('shop', ['locale' => $locale, 'sort' => 'newest']),
                                    __('site.on_sale') => route('shop', ['locale' => $locale]),
                                ] as $label => $href)
                                    <a href="{{ $href }}" class="block px-4 py-2 text-sm text-gray-700 transition hover:bg-gradient-to-r hover:from-[#881C27]/10 hover:to-transparent hover:pl-6 hover:font-medium hover:text-[#881C27]">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <a
                        href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['measurement']]) }}"
                        class="relative overflow-hidden rounded-md px-4 py-2 font-medium transition-all duration-500 hover:scale-105 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white hover:shadow-lg"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    >{{ __('site.measurement_guide') }}</a>

                    <a
                        href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['about']]) }}"
                        class="relative overflow-hidden rounded-md px-4 py-2 font-medium transition-all duration-500 hover:scale-105 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white hover:shadow-lg"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    >{{ __('site.about') }}</a>

                    <a
                        href="{{ $isHome ? '#contact' : route('home', ['locale' => $locale]).'#contact' }}"
                        class="relative overflow-hidden rounded-md px-4 py-2 font-medium transition-all duration-500 hover:scale-105 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white hover:shadow-lg"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    >{{ __('site.contact') }}</a>
                </nav>

                <div class="relative flex items-center gap-1 sm:gap-2">
                    <button
                        type="button"
                        data-header-action="search"
                        class="relative grid h-9 w-9 place-items-center overflow-hidden rounded-md transition-all duration-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity hover:scale-105 hover:text-white hover:shadow-lg hover:before:opacity-100 sm:h-10 sm:w-10"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        @click="searchOpen = !searchOpen"
                        aria-label="{{ __('site.search_products') }}"
                    >
                        <x-icon name="search" class="!h-5 !w-5 relative z-10" />
                    </button>

                    <a
                        data-header-action="wishlist"
                        href="{{ route('wishlist', ['locale' => $locale]) }}"
                        class="relative grid h-9 w-9 place-items-center overflow-hidden rounded-md transition-all duration-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity hover:scale-105 hover:text-white hover:shadow-lg hover:before:opacity-100 sm:h-10 sm:w-10"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        aria-label="{{ __('commerce.wishlist') }}"
                    >
                        <x-icon name="heart" class="!h-5 !w-5 relative z-10" />
                    </a>

                    <a
                        data-header-action="cart"
                        href="{{ route('cart', ['locale' => $locale]) }}"
                        class="relative grid h-9 w-9 place-items-center overflow-hidden rounded-md transition-all duration-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity hover:scale-105 hover:text-white hover:shadow-lg hover:before:opacity-100 sm:h-10 sm:w-10"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        aria-label="{{ __('commerce.cart') }}"
                    >
                        <x-icon name="cart" class="!h-5 !w-5 relative z-10" />
                    </a>

                    <a
                        data-header-action="account"
                        href="{{ auth()->check() ? route('account', ['locale' => $locale]) : route('login', ['locale' => $locale]) }}"
                        class="relative grid h-9 w-9 place-items-center overflow-hidden rounded-md transition-all duration-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity hover:scale-105 hover:text-white hover:shadow-lg hover:before:opacity-100 sm:h-10 sm:w-10"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        aria-label="{{ auth()->check() ? __('auth.account') : __('auth.login') }}"
                    >
                        <x-icon name="user" class="!h-5 !w-5 relative z-10" />
                    </a>

                    <form
                        x-cloak
                        x-show="searchOpen"
                        @click.outside="searchOpen = false"
                        method="get"
                        action="{{ route('shop', ['locale' => $locale]) }}"
                        class="absolute top-full z-50 mt-3 flex w-72 gap-2 rounded-xl border border-gray-100 bg-white p-3 shadow-xl ltr:right-0 rtl:left-0"
                    >
                        <input
                            type="search"
                            name="q"
                            class="min-w-0 flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 outline-none focus:border-[#881C27]"
                            placeholder="{{ __('site.search_placeholder') }}"
                        >
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-[#881C27] to-[#2A6867] px-3 text-white">
                            <x-icon name="search" class="!h-4 !w-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-black/40 lg:hidden"
        @click="open = false"
    ></div>

    <aside
        id="mobile-navigation"
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="-translate-x-full rtl:translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full rtl:translate-x-full"
        class="fixed inset-y-0 left-0 z-[70] w-80 max-w-[85vw] overflow-y-auto bg-white p-5 shadow-2xl rtl:left-auto rtl:right-0 lg:hidden"
    >
        <div class="mb-6 flex items-center justify-between">
            <img src="{{ asset('images/kabulfit-live/logo-header.png') }}" alt="KabulFit" class="h-24 w-auto object-contain">
            <button type="button" class="h-10 w-10 rounded-full text-2xl text-gray-600" @click="open = false" aria-label="{{ __('site.menu') }}">×</button>
        </div>

        <nav class="grid gap-1 text-gray-800">
            <a class="rounded-lg px-4 py-3 font-medium hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white" href="{{ route('home', ['locale' => $locale]) }}" @click="open = false">{{ __('site.home') }}</a>
            <a class="rounded-lg px-4 py-3 font-medium hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white" href="{{ route('shop', ['locale' => $locale]) }}" @click="open = false">{{ __('site.shop') }}</a>
            <div class="ml-4 grid gap-1 border-l-2 border-[#881C27]/30 pl-4 rtl:ml-0 rtl:mr-4 rtl:border-l-0 rtl:border-r-2 rtl:pl-0 rtl:pr-4">
                <a class="py-2 text-sm text-gray-600 hover:text-[#881C27]" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['men']]) }}">{{ __('site.men') }}</a>
                <a class="py-2 text-sm text-gray-600 hover:text-[#881C27]" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['women']]) }}">{{ __('site.women') }}</a>
                <a class="py-2 text-sm text-gray-600 hover:text-[#881C27]" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['boys']]) }}">{{ __('site.boys') }}</a>
                <a class="py-2 text-sm text-gray-600 hover:text-[#881C27]" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['girls']]) }}">{{ __('site.girls') }}</a>
            </div>
            <a class="rounded-lg px-4 py-3 font-medium hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['measurement']]) }}" @click="open = false">{{ __('site.measurement_guide') }}</a>
            <a class="rounded-lg px-4 py-3 font-medium hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['about']]) }}" @click="open = false">{{ __('site.about') }}</a>
            <a class="rounded-lg px-4 py-3 font-medium hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white" href="{{ $isHome ? '#contact' : route('home', ['locale' => $locale]).'#contact' }}" @click="open = false">{{ __('site.contact') }}</a>
        </nav>
    </aside>
</header>

<main id="main-content" @class(['pt-[8.25rem] sm:pt-[9.25rem]' => ! $isHome])>
    @yield('content')
</main>

<footer id="contact" class="bg-gradient-to-b from-gray-900 to-black pb-6 pt-12 text-white sm:pt-16">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-8 grid grid-cols-1 gap-8 sm:mb-12 sm:gap-12 md:grid-cols-2 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="mb-4 inline-block sm:mb-6" aria-label="{{ __('site.kabulfit_home') }}">
                    <img src="{{ asset('images/kabulfit-live/logo-footer.png') }}" width="310" height="120" alt="KabulFit" loading="lazy" class="h-16 w-auto object-contain sm:h-20 md:h-24">
                </a>

                <p class="mb-4 max-w-sm text-sm leading-relaxed text-gray-400 sm:mb-6 sm:text-base">{{ __('site.story_p1') }}</p>

                <div class="mb-6 space-y-2 sm:mb-8 sm:space-y-3">
                    <h4 class="mb-3 text-sm font-semibold text-white sm:mb-4 sm:text-base">{{ __('site.contact') }}</h4>

                    <a href="https://wa.me/93794120017" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-2 text-gray-400 transition-colors hover:text-white sm:gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white/10 transition-all group-hover:scale-110 group-hover:bg-[#25D366] sm:h-8 sm:w-8">
                            <span class="text-xs font-bold">W</span>
                        </span>
                        <span class="text-xs sm:text-sm">{{ __('site.whatsapp_contact') }}</span>
                    </a>

                    <a href="mailto:{{ $contactEmail }}" class="group flex items-center gap-2 text-gray-400 transition-colors hover:text-white sm:gap-3">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-white/10 transition-all group-hover:scale-110 group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867] sm:h-8 sm:w-8">
                            <x-icon name="mail" class="!h-4 !w-4" />
                        </span>
                        <span class="text-xs sm:text-sm">{{ $contactEmail }}</span>
                    </a>

                    <a href="https://www.google.com/maps/search/Kabul,+Afghanistan" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-gray-400 transition-colors hover:text-white">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-white/10 transition-all group-hover:scale-110 group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867]">
                            <x-icon name="map-pin" class="!h-4 !w-4" />
                        </span>
                        <span class="text-sm">{{ __('site.kabul_location') }}</span>
                    </a>

                    <a href="https://www.google.com/maps/search/Dubai,+United+Arab+Emirates" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-gray-400 transition-colors hover:text-white">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-white/10 transition-all group-hover:scale-110 group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867]">
                            <x-icon name="map-pin" class="!h-4 !w-4" />
                        </span>
                        <span class="text-sm">{{ __('site.dubai_location') }}</span>
                    </a>
                </div>

                <div>
                    <h4 class="mb-3 text-sm font-semibold text-white sm:mb-4 sm:text-base">{{ __('site.follow_us') }}</h4>
                    <div class="flex gap-2 sm:gap-3">
                        <a href="https://www.facebook.com/KabulFitTailoring/" target="_blank" rel="noopener noreferrer" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 transition-all hover:scale-110 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] sm:h-10 sm:w-10" aria-label="Facebook"><x-icon name="facebook" class="!h-5 !w-5" /></a>
                        <a href="#" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 transition-all hover:scale-110 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] sm:h-10 sm:w-10" aria-label="Instagram"><x-icon name="instagram" class="!h-5 !w-5" /></a>
                        <a href="#" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-sm font-bold transition-all hover:scale-110 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] sm:h-10 sm:w-10" aria-label="TikTok">T</a>
                        <a href="http://www.youtube.com/@Kabulfit" target="_blank" rel="noopener noreferrer" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 transition-all hover:scale-110 hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] sm:h-10 sm:w-10" aria-label="YouTube"><x-icon name="youtube" class="!h-5 !w-5" /></a>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="mb-3 text-base font-semibold sm:mb-4 sm:text-lg">{{ __('site.shop') }}</h4>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['men']]) }}">{{ __('site.men') }}</a></li>
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['women']]) }}">{{ __('site.women') }}</a></li>
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['boys']]) }}">{{ __('site.boys') }}</a></li>
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('categories.show', ['locale' => $locale, 'slug' => $categorySlugs['girls']]) }}">{{ __('site.girls') }}</a></li>
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'sort' => 'newest']) }}">{{ __('site.new_arrivals') }}</a></li>
                    <li><a class="text-xs text-gray-400 transition-colors hover:text-white sm:text-sm" href="{{ route('shop', ['locale' => $locale]) }}">{{ __('site.on_sale') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-3 text-base font-semibold sm:mb-4 sm:text-lg">{{ __('site.customer_service') }}</h4>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['measurement']]) }}">{{ __('site.measurement_guide') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['shipping']]) }}">{{ __('site.shipping_policy') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['return']]) }}">{{ __('site.return_policy') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['faq']]) }}">{{ __('site.faq') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ $isHome ? '#contact' : route('home', ['locale' => $locale]).'#contact' }}">{{ __('site.contact') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-3 text-base font-semibold sm:mb-4 sm:text-lg">{{ __('site.company') }}</h4>
                <ul class="space-y-2">
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['about']]) }}">{{ __('site.about') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('home', ['locale' => $locale]).'#story' }}">{{ __('site.our_story') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['privacy']]) }}">{{ __('site.privacy_policy') }}</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors hover:text-white" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['terms']]) }}">{{ __('site.terms_conditions') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mb-8 border-t border-gray-800 pt-8 sm:mb-10 sm:pt-10">
            <div class="mx-auto max-w-xl text-center" x-data="{ subscribed: false }">
                <h4 class="mb-2 text-lg font-semibold sm:mb-3 sm:text-xl">{{ __('site.newsletter') }}</h4>
                <p class="mb-4 text-sm text-gray-400 sm:mb-6 sm:text-base">{{ __('site.subscribe_text') }}</p>
                <form class="mx-auto flex max-w-md flex-col gap-2 sm:flex-row sm:gap-3" @submit.prevent="subscribed = true">
                    <input type="email" required placeholder="your@email.com" class="h-10 min-w-0 flex-1 rounded-md border border-white/20 bg-white/10 px-3 text-sm text-white placeholder:text-gray-400 outline-none focus:border-white/50 sm:h-12 sm:text-base">
                    <button type="submit" class="h-10 whitespace-nowrap rounded-md bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 text-sm font-semibold text-white hover:opacity-90 sm:h-12 sm:px-6 sm:text-base">
                        <span x-show="!subscribed">{{ __('site.subscribe') }}</span>
                        <span x-cloak x-show="subscribed">✓</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-2 border-t border-gray-800 pt-4 text-xs text-gray-400 sm:gap-3 sm:pt-6 sm:text-sm md:flex-row">
            <p>© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</p>
            <p class="flex items-center gap-1">{{ __('site.made_with') }} <span class="text-red-500">♥</span> {{ __('site.in_afghanistan') }}</p>
        </div>
    </div>
</footer>

<nav class="safe-bottom fixed inset-x-0 bottom-0 z-50 flex border-t border-gray-200 bg-white md:hidden" style="padding-bottom:max(env(safe-area-inset-bottom),0px)" aria-label="{{ __('site.mobile_navigation') }}">
    <a href="{{ route('home', ['locale' => $locale]) }}" class="flex flex-1 flex-col items-center justify-center gap-1 py-2 text-[11px] text-gray-600">
        <span class="text-lg" aria-hidden="true">⌂</span><span>{{ __('site.home') }}</span>
    </a>
    <a href="{{ route('shop', ['locale' => $locale]) }}" class="flex flex-1 flex-col items-center justify-center gap-1 py-2 text-[11px] text-gray-600">
        <span class="text-lg" aria-hidden="true">▦</span><span>{{ __('site.shop') }}</span>
    </a>
    <a href="{{ route('cart', ['locale' => $locale]) }}" class="flex flex-1 flex-col items-center justify-center gap-1 py-2 text-[11px] text-gray-600">
        <x-icon name="cart" class="!h-5 !w-5" /><span>{{ __('commerce.cart') }}</span>
    </a>
    <a href="{{ auth()->check() ? route('account', ['locale' => $locale]) : route('login', ['locale' => $locale]) }}" class="flex flex-1 flex-col items-center justify-center gap-1 py-2 text-[11px] text-gray-600">
        <x-icon name="user" class="!h-5 !w-5" /><span>{{ __('auth.account') }}</span>
    </a>
</nav>
</body>
</html>
