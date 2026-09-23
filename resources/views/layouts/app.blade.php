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
<body @class(['min-h-screen flex flex-col bg-[#FDFBF7]', 'kabulfit-live-home' => request()->routeIs('home')])>
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
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    dir="{{ in_array($locale, config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}"
    x-data="{ open: false, searchOpen: false, scrolled: false, lastScrollY: 0 }"
    @scroll.window="
        const y = window.scrollY;
        if (y > 100) scrolled = true;
        lastScrollY = y;
    "
    :class="{{ $isHome ? "scrolled ? 'bg-white/95 backdrop-blur-md shadow-lg' : 'bg-transparent'" : "'bg-white/95 backdrop-blur-md shadow-lg'" }}"
    @keydown.escape.window="open = false; searchOpen = false"
>
    <div class="bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white text-sm py-3 px-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <p class="hidden md:block animate-blink">{{ __('site.announcement') }}</p>
            <div class="flex items-center gap-1 mx-auto md:mx-0" aria-label="{{ __('site.language') }}">
                @foreach (($seo->alternates ?? []) as $alternateLocale => $href)
                    <a
                        href="{{ $href }}"
                        hreflang="{{ $alternateLocale }}"
                        lang="{{ $alternateLocale }}"
                        @class([
                            'px-2 py-1 text-sm transition-all',
                            'text-white font-bold border-b-2 border-white' => $locale === $alternateLocale,
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

    <div>
        <div class="max-w-7xl mx-auto px-2 sm:px-4 py-0.5 sm:py-1">
            <div class="flex items-center justify-between gap-2">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 lg:hidden"
                    :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                    @click="open = true"
                    aria-controls="mobile-navigation"
                    :aria-expanded="open.toString()"
                    aria-label="{{ __('site.menu') }}"
                >
                    <x-icon name="menu" class="w-6 h-6" />
                </button>

                <a href="{{ route('home', ['locale' => $locale]) }}" class="flex items-center gap-2" aria-label="{{ __('site.kabulfit_home') }}">
                    <div class="overflow-hidden relative h-16 sm:h-20 md:h-22 lg:h-25">
                        <img
                            src="{{ asset('images/kabulfit-live/logo-header.png') }}"
                            alt="KabulFit Logo"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                            class="h-16 sm:h-20 md:h-22 lg:h-25 w-auto object-contain transition-opacity duration-300 opacity-100"
                        >
                    </div>
                </a>

                <nav class="hidden lg:flex items-center gap-1" aria-label="{{ __('site.primary_navigation') }}">
                    @foreach ([
                        [__('site.home'), route('home', ['locale' => $locale])],
                        [__('site.measurement_guide'), route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['measurement']])],
                        [__('site.about'), route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['about']])],
                        [__('site.contact'), $isHome ? '#contact' : route('home', ['locale' => $locale]).'#contact'],
                    ] as [$label, $href])
                        <div class="relative group">
                            <a href="{{ $href }}">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 relative overflow-hidden transition-all duration-500 font-medium hover:scale-105 hover:shadow-lg before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity before:duration-500 hover:before:opacity-100 hover:!text-white after:absolute after:bottom-0 after:left-0 after:w-0 after:h-1 after:bg-gradient-to-r after:from-[#881C27] after:to-[#2A6867] after:transition-all after:duration-500 hover:after:w-full after:animate-pulse"
                                    :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                                >
                                    <span class="relative z-10">{{ $label }}</span>
                                </button>
                            </a>
                        </div>

                        @if ($loop->first)
                            <div class="relative group">
                                <a href="{{ route('shop', ['locale' => $locale]) }}">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 relative overflow-hidden transition-all duration-500 font-medium hover:scale-105 hover:shadow-lg before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity before:duration-500 hover:before:opacity-100 hover:!text-white after:absolute after:bottom-0 after:left-0 after:w-0 after:h-1 after:bg-gradient-to-r after:from-[#881C27] after:to-[#2A6867] after:transition-all after:duration-500 hover:after:w-full after:animate-pulse"
                                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                                    >
                                        <span class="relative z-10">{{ __('site.shop') }}</span>
                                        <x-icon name="chevron-down" class="w-4 h-4 ml-1 relative z-10 transition-transform duration-500 group-hover:rotate-180" />
                                    </button>
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
                                        ] as $shopLabel => $shopHref)
                                            <a href="{{ $shopHref }}" class="block px-4 py-2 text-sm text-gray-700 transition hover:bg-gradient-to-r hover:from-[#881C27]/10 hover:to-transparent hover:pl-6 hover:font-medium hover:text-[#881C27]">
                                                {{ $shopLabel }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </nav>

                <div class="flex items-center gap-1 sm:gap-2 relative">
                    <button
                        type="button"
                        data-header-action="search"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 sm:h-10 sm:w-10 transition-all duration-500 relative overflow-hidden hover:scale-105 hover:shadow-lg hover:!text-white before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity before:duration-500 hover:before:opacity-100"
                        :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                        @click="searchOpen = !searchOpen"
                        aria-label="{{ __('site.search_products') }}"
                    >
                        <x-icon name="search" class="w-5 h-5 relative z-10" />
                    </button>

                    @foreach ([
                        ['wishlist', route('wishlist', ['locale' => $locale]), 'heart', __('commerce.wishlist')],
                        ['cart', route('cart', ['locale' => $locale]), 'shopping-bag', __('commerce.cart')],
                        ['account', auth()->check() ? route('account', ['locale' => $locale]) : route('login', ['locale' => $locale]), 'user', auth()->check() ? __('auth.account') : __('auth.login')],
                    ] as [$action, $href, $icon, $label])
                        <a data-header-action="{{ $action }}" href="{{ $href }}">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 sm:h-10 sm:w-10 transition-all duration-500 relative overflow-hidden hover:scale-105 hover:shadow-lg hover:!text-white before:absolute before:inset-0 before:bg-gradient-to-r before:from-[#881C27] before:to-[#2A6867] before:opacity-0 before:transition-opacity before:duration-500 hover:before:opacity-100"
                                :class="{{ $isHome ? "scrolled ? 'text-gray-700' : 'text-white'" : "'text-gray-700'" }}"
                                aria-label="{{ $label }}"
                            >
                                <x-icon :name="$icon" class="w-5 h-5 relative z-10" />
                            </button>
                        </a>
                    @endforeach

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
                            <x-icon name="search" class="h-4 w-4" />
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

<main id="main-content" class="flex-1 pt-32 sm:pt-36 md:pt-40 pb-16 md:pb-0">
    @yield('content')
</main>

<footer id="contact" class="bg-gradient-to-b from-gray-900 to-black text-white pt-12 sm:pt-16 pb-6" dir="{{ in_array($locale, config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-12 mb-8 sm:mb-12">
            <div class="lg:col-span-2">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="inline-block mb-4 sm:mb-6" aria-label="{{ __('site.kabulfit_home') }}">
                    <div class="overflow-hidden relative h-16 sm:h-20 md:h-24">
                        <img src="{{ asset('images/kabulfit-live/logo-footer.png') }}" alt="KabulFit Logo" loading="lazy" decoding="async" class="h-16 sm:h-20 md:h-24 w-auto object-contain transition-opacity duration-300 opacity-100">
                    </div>
                </a>

                <p class="text-sm sm:text-base text-gray-400 mb-4 sm:mb-6 max-w-sm leading-relaxed">{{ __('site.story_p1') }}</p>

                <div class="space-y-2 sm:space-y-3 mb-6 sm:mb-8">
                    <h4 class="font-semibold text-white mb-3 sm:mb-4 text-sm sm:text-base">{{ __('site.contact') }}</h4>

                    <a href="https://wa.me/93794120017" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 sm:gap-3 text-gray-400 hover:text-white transition-colors group">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-gradient-to-r group-hover:from-[#25D366] group-hover:to-[#128C7E] group-hover:scale-110 transition-all">
                            <x-icon name="whatsapp" class="w-3 h-3 sm:w-4 sm:h-4" />
                        </div>
                        <span class="text-xs sm:text-sm">{{ __('site.whatsapp_contact') }}</span>
                    </a>

                    <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-2 sm:gap-3 text-gray-400 hover:text-white transition-colors group">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867] group-hover:scale-110 transition-all">
                            <x-icon name="mail" class="w-3 h-3 sm:w-4 sm:h-4" />
                        </div>
                        <span class="text-xs sm:text-sm">{{ $contactEmail }}</span>
                    </a>

                    <a href="https://www.google.com/maps/search/Kabul,+Afghanistan" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-400 hover:text-white transition-colors group cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867] group-hover:scale-110 transition-all">
                            <x-icon name="map-pin" class="w-4 h-4" />
                        </div>
                        <span class="text-sm">{{ __('site.kabul_location') }}</span>
                    </a>

                    <a href="https://www.google.com/maps/search/Dubai,+United+Arab+Emirates" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-gray-400 hover:text-white transition-colors group cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-gradient-to-r group-hover:from-[#881C27] group-hover:to-[#2A6867] group-hover:scale-110 transition-all">
                            <x-icon name="map-pin" class="w-4 h-4" />
                        </div>
                        <span class="text-sm">{{ __('site.dubai_location') }}</span>
                    </a>
                </div>

                <div>
                    <h4 class="font-semibold text-white mb-3 sm:mb-4 text-sm sm:text-base">{{ __('site.follow_us') }}</h4>
                    <div class="flex gap-2 sm:gap-3">
                        <a href="https://www.facebook.com/KabulFitTailoring/" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:scale-110 transition-all" aria-label="Facebook"><x-icon name="facebook" class="w-4 h-4 sm:w-5 sm:h-5" /></a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:scale-110 transition-all" aria-label="Instagram"><x-icon name="instagram" class="w-4 h-4 sm:w-5 sm:h-5" /></a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:scale-110 transition-all" aria-label="TikTok"><x-icon name="tiktok" class="w-4 h-4 sm:w-5 sm:h-5" /></a>
                        <a href="http://www.youtube.com/@Kabulfit" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:scale-110 transition-all" aria-label="Youtube"><x-icon name="youtube" class="w-4 h-4 sm:w-5 sm:h-5" /></a>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-base sm:text-lg mb-3 sm:mb-4">{{ __('site.shop') }}</h4>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'category' => 'men']) }}">{{ __('site.men') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'category' => 'women']) }}">{{ __('site.women') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'category' => 'boys']) }}">{{ __('site.boys') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'category' => 'girls']) }}">{{ __('site.girls') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale, 'sort' => 'newest']) }}">{{ __('site.new_arrivals') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-xs sm:text-sm" href="{{ route('shop', ['locale' => $locale]) }}">{{ __('site.on_sale') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-base sm:text-lg mb-3 sm:mb-4">{{ __('site.customer_service') }}</h4>
                <ul class="space-y-1 sm:space-y-2">
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['measurement']]) }}">{{ __('site.measurement_guide') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['shipping']]) }}">{{ __('site.shipping_policy') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['return']]) }}">{{ __('site.return_policy') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['faq']]) }}">{{ __('site.faq') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ $isHome ? '#contact' : route('home', ['locale' => $locale]).'#contact' }}">{{ __('site.contact') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-base sm:text-lg mb-3 sm:mb-4">{{ __('site.company') }}</h4>
                <ul class="space-y-2">
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['about']]) }}">{{ __('site.about_us') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('home', ['locale' => $locale]).'#story' }}">{{ __('site.our_story') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['privacy']]) }}">{{ __('site.privacy_policy') }}</a></li>
                    <li><a class="text-gray-400 hover:text-white transition-colors text-sm" href="{{ route('content.page', ['locale' => $locale, 'slug' => $pageSlugs['terms']]) }}">{{ __('site.terms_conditions') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 sm:pt-10 mb-8 sm:mb-10">
            <div class="max-w-xl mx-auto text-center" x-data="{ subscribed: false }">
                <h4 class="font-semibold text-lg sm:text-xl mb-2 sm:mb-3">{{ __('site.newsletter') }}</h4>
                <p class="text-sm sm:text-base text-gray-400 mb-4 sm:mb-6">{{ __('site.subscribe_text') }}</p>
                <form class="flex flex-col sm:flex-row gap-2 sm:gap-3 max-w-md mx-auto" @submit.prevent="subscribed = true">
                    <input type="email" required placeholder="your@email.com" data-live-newsletter-input class="flex w-full rounded-md border px-3 py-1 shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-white/10 border-white/20 text-white placeholder:text-gray-400 h-10 sm:h-12 text-sm sm:text-base">
                    <button type="submit" data-live-newsletter-button class="inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 text-primary-foreground shadow hover:bg-primary/90 py-2 bg-gradient-to-r from-[#881C27] to-[#2A6867] hover:opacity-90 px-4 sm:px-6 h-10 sm:h-12 text-sm sm:text-base whitespace-nowrap">
                        <x-icon name="mail" class="w-4 h-4 sm:w-5 sm:h-5 mr-2" />
                        {{ __('site.subscribe') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-4 sm:pt-6 flex flex-col md:flex-row justify-between items-center gap-2 sm:gap-3">
            <p class="text-gray-400 text-xs sm:text-sm text-center md:text-left">© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</p>
            <p class="text-gray-400 text-xs sm:text-sm flex items-center gap-1">{{ __('site.made_with') }} <x-icon name="heart" class="w-3 h-3 sm:w-4 sm:h-4 text-red-500 fill-current" /> {{ __('site.in_afghanistan') }}</p>
        </div>
    </div>
</footer>

<nav class="safe-bottom fixed inset-x-0 bottom-0 z-50 flex border-t border-gray-200 bg-white md:hidden" style="padding-bottom:max(env(safe-area-inset-bottom),0px)" aria-label="{{ __('site.mobile_navigation') }}">
    <a href="{{ route('home', ['locale' => $locale]) }}" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-[#881C27] transition-colors">
        <div class="relative"><x-icon name="home" class="!h-5 !w-5" /></div>
        <span class="text-[10px] font-medium">{{ __('site.home') }}</span>
        <div class="absolute left-1/2 top-0 h-0.5 w-6 -translate-x-1/2 rounded-b bg-[#881C27]"></div>
    </a>
    <a href="{{ route('shop', ['locale' => $locale]) }}" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-gray-500 transition-colors">
        <div class="relative"><x-icon name="shopping-bag" class="!h-5 !w-5" /></div>
        <span class="text-[10px] font-medium">{{ __('site.shop') }}</span>
    </a>
    <a href="{{ route('cart', ['locale' => $locale]) }}" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-gray-500 transition-colors">
        <div class="relative"><x-icon name="cart" class="!h-5 !w-5" /></div>
        <span class="text-[10px] font-medium">{{ __('commerce.cart') }}</span>
    </a>
    <a href="{{ auth()->check() ? route('account', ['locale' => $locale]) : route('login', ['locale' => $locale]) }}" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-gray-500 transition-colors">
        <div class="relative"><x-icon name="user" class="!h-5 !w-5" /></div>
        <span class="text-[10px] font-medium">{{ __('auth.account') }}</span>
    </a>
</nav>
</body>
</html>
