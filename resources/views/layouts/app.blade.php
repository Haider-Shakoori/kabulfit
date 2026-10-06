<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#D91E36">
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
    @if (! empty($seo->jsonLd))
        <script type="application/ld+json">{!! json_encode($seo->jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($siteSettingsForAnalytics = app(\App\Services\Settings\SiteSettings::class))
    @php($metaPixelId = $siteSettingsForAnalytics->get('analytics.meta_pixel_id', (string) config('services.meta.pixel_id')))
    @php($gaMeasurementId = $siteSettingsForAnalytics->get('analytics.ga_measurement_id', (string) config('services.analytics.measurement_id')))
    @if($metaPixelId)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', @json($metaPixelId));
            fbq('track', 'PageView');
        </script>
    @endif
    @if($gaMeasurementId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaMeasurementId) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($gaMeasurementId));
        </script>
    @endif
    <script>
        window.kabulFitTrack = function (eventName, payload) {
            payload = payload || {};
            if (typeof window.fbq === 'function') {
                window.fbq('track', eventName, payload);
            }
            if (typeof window.gtag === 'function') {
                const gaNames = {
                    ViewContent: 'view_item',
                    AddToCart: 'add_to_cart',
                    InitiateCheckout: 'begin_checkout',
                    Purchase: 'purchase'
                };
                window.gtag('event', gaNames[eventName] || eventName, payload);
            }
        };
    </script>
</head>
<body class="min-h-screen bg-[#FDFBF7] text-gray-900">
@if(session('pixel_event'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.kabulFitTrack?.(@json(session('pixel_event.name')), @json(session('pixel_event.payload')));
        }, { once: true });
    </script>
@endif
@php($siteSettings = app(\App\Services\Settings\SiteSettings::class))
@php($contactEmail = $siteSettings->get('site.contact_email', 'info@kabulfit.com'))
@php($facebookUrl = $siteSettings->get('social.facebook', 'https://www.facebook.com/KabulFitTailoring/'))
@php($instagramUrl = $siteSettings->get('social.instagram', ''))
@php($tiktokUrl = $siteSettings->get('social.tiktok', ''))
@php($youtubeUrl = $siteSettings->get('social.youtube', 'http://www.youtube.com/@Kabulfit'))
@php($whatsappUrl = $siteSettings->get('social.whatsapp', 'https://wa.me/93794120017'))
@php($measurementGuideSlug = app()->getLocale() === 'en' ? 'measurement-guide' : (app()->getLocale() === 'fa' ? 'راهنمای-اندازه-گیری' : 'د-اندازې-لارښود'))
@php($contactSlug = app()->getLocale() === 'en' ? 'contact' : (app()->getLocale() === 'fa' ? 'تماس' : 'اړیکه'))
@php($isHome = request()->routeIs('home'))
<a class="skip-link" href="#main-content">{{ __('site.skip_to_content') }}</a>

<header
    class="relative inset-x-0 top-0 z-50 bg-white shadow-sm md:fixed"
    x-data="{ open: false, searchOpen: false }"
    @keydown.escape.window="open = false; searchOpen = false"
>
    <div class="bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-2 text-sm text-white md:py-3">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <p class="hidden animate-blink md:block">
                {{ app()->getLocale() === 'ps' ? 'د محدود وخت لپاره وړیا شپینګ 🚚 او ځانګړې وړیا هدیې! 🎁' : (app()->getLocale() === 'fa' ? 'حمل‌ونقل رایگان 🚚 برای مدت محدود و هدایای رایگان ویژه! 🎁' : 'Free Shipping 🚚 for a Limited Time! & Exclusive Free Gifts! 🎁') }}
            </p>
            <div class="mx-auto flex items-center gap-1 md:mx-0">
                @foreach (($seo->alternates ?? []) as $locale => $href)
                    <a href="{{ $href }}"
                       hreflang="{{ $locale }}"
                       lang="{{ $locale }}"
                       @class([
                           'px-2 py-1 text-sm transition',
                           'border-b-2 border-white font-bold text-white' => app()->getLocale() === $locale,
                           'text-white/70 hover:text-white' => app()->getLocale() !== $locale,
                       ])>
                        {{ $locale === 'en' ? 'English' : ($locale === 'fa' ? 'دری' : 'پښتو') }}
                    </a>
                    @unless($loop->last)<span class="text-white/50">|</span>@endunless
                @endforeach
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-2 sm:px-4">
        <div class="flex min-h-16 items-center justify-between gap-2 md:min-h-20">
            <button type="button"
                    class="grid h-11 w-11 place-items-center rounded-full lg:hidden"
                    @click="open = true"
                    aria-label="{{ __('site.menu') }}" aria-controls="mobile-navigation" :aria-expanded="open.toString()">
                <x-icon name="menu" class="h-6 w-6 text-gray-700" />
            </button>

            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="shrink-0" aria-label="{{ __('site.kabulfit_home') }}">
                <img src="{{ asset('images/kabulfit-live/logo-header.png') }}" width="310" height="120" alt="KabulFit" class="kabulfit-header-logo block w-auto object-contain">
            </a>

            <nav id="primary-nav" class="hidden items-center gap-7 text-sm font-medium lg:flex" aria-label="{{ __('site.primary_navigation') }}">
                <a class="transition hover:text-[#D91E36] text-gray-800" href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a>
                <div class="group relative">
                    <a class="inline-flex items-center gap-1 transition hover:text-[#D91E36] text-gray-800" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">
                        {{ __('site.shop') }}
                        <span class="text-xs">⌄</span>
                    </a>
                    <div class="invisible absolute start-0 top-full mt-3 w-56 translate-y-2 rounded-2xl border border-gray-100 bg-white p-2 text-gray-700 opacity-0 shadow-xl transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                        @foreach ($categoriesForNavigation ?? [] as $navCategory)
                            @php($navTranslation = $navCategory->translation())
                            <a class="block rounded-xl px-4 py-3 hover:bg-gray-50 hover:text-[#D91E36]" href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $navTranslation?->slug]) }}">{{ $navTranslation?->name }}</a>
                        @endforeach
                    </div>
                </div>
                <a class="transition hover:text-[#D91E36] text-gray-800" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => $measurementGuideSlug]) }}">{{ __('site.measurement_guide') }}</a>
                <a class="transition hover:text-[#D91E36] text-gray-800" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')]) }}">{{ __('site.about') }}</a>
                <a class="transition hover:text-[#D91E36] text-gray-800" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => $contactSlug]) }}">{{ __('site.contact') }}</a>
            </nav>

            <div class="flex items-center gap-1 sm:gap-2">
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full transition hover:bg-gray-100" @click="searchOpen = !searchOpen" aria-label="{{ __('site.search_products') }}">
                    <x-icon name="search" class="h-5 w-5 text-gray-700" />
                </button>
                <a href="{{ route('wishlist', ['locale' => app()->getLocale()]) }}" class="hidden h-10 w-10 place-items-center rounded-full transition hover:bg-gray-100 sm:grid" aria-label="{{ __('commerce.wishlist') }}">
                    <x-icon name="heart" class="h-5 w-5 text-gray-700" />
                </a>
                <a href="{{ route('cart', ['locale' => app()->getLocale()]) }}" class="grid h-10 w-10 place-items-center rounded-full transition hover:bg-gray-100" aria-label="{{ __('commerce.cart') }}">
                    <x-icon name="bag" class="h-5 w-5 text-gray-700" />
                </a>
                <a href="{{ auth()->check() ? route('account', ['locale' => app()->getLocale()]) : route('login', ['locale' => app()->getLocale()]) }}" class="hidden h-10 w-10 place-items-center rounded-full transition hover:bg-gray-100 sm:grid" aria-label="{{ auth()->check() ? __('auth.account') : __('auth.login') }}">
                    <x-icon name="user" class="h-5 w-5 text-gray-700" />
                </a>
            </div>
        </div>

        <form x-show="searchOpen" x-transition class="pb-4" method="GET" action="{{ route('shop', ['locale' => app()->getLocale()]) }}">
            <div class="relative mx-auto max-w-2xl">
                <input name="q" type="search" placeholder="{{ __('site.search_placeholder') }}" class="w-full rounded-full border border-gray-200 bg-white px-5 py-3 pe-12 text-gray-900 shadow-lg outline-none focus:border-[#881C27]">
                <button type="submit" class="absolute inset-y-0 end-1 grid w-11 place-items-center text-gray-600"><x-icon name="search" class="h-5 w-5" /></button>
            </div>
        </form>
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-[70] lg:hidden">
        <button class="absolute inset-0 bg-black/40" @click="open = false" aria-label="{{ __('site.menu') }}"></button>
        <aside id="mobile-navigation" x-transition class="absolute inset-y-0 start-0 w-[84%] max-w-sm overflow-y-auto bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between">
                <img src="{{ asset('images/kabulfit-live/logo-header.png') }}" alt="KabulFit" class="kabulfit-drawer-logo block w-auto object-contain">
                <button class="grid h-10 w-10 place-items-center rounded-full bg-gray-100" @click="open = false"><x-icon name="close" class="h-5 w-5" /></button>
            </div>
            <nav class="mt-8 space-y-2">
                @foreach ([
                    [__('site.home'), route('home', ['locale' => app()->getLocale()])],
                    [__('site.shop'), route('shop', ['locale' => app()->getLocale()])],
                    [__('site.measurement_guide'), route('content.page', ['locale' => app()->getLocale(), 'slug' => $measurementGuideSlug])],
                    [__('site.about'), route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')])],
                    [__('site.contact'), route('content.page', ['locale' => app()->getLocale(), 'slug' => $contactSlug])],
                ] as [$label, $href])
                    <a @click="open = false" href="{{ $href }}" class="relative block overflow-hidden rounded-xl px-4 py-3 font-medium text-gray-800 transition duration-300 hover:scale-[1.02] hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:ps-6 hover:text-white">{{ $label }}</a>
                @endforeach
            </nav>
        </aside>
    </div>
</header>

<main id="main-content" class="pt-0 pb-16 md:pt-40 md:pb-0">
    @yield('content')
</main>

<footer id="contact" class="bg-[#111827] text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:py-16">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-5 lg:gap-10">
            <div class="lg:col-span-2">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="inline-block">
                    <img src="{{ asset('images/kabulfit-live/logo-footer.png') }}" width="310" height="120" alt="KabulFit" class="h-20 w-auto object-contain sm:h-24">
                </a>
                <p class="mt-4 max-w-sm text-sm leading-7 text-white/65">{{ __('site.footer_intro') }}</p>

                <div class="mt-5 space-y-3 text-sm text-white/75">
                    @if($whatsappUrl)
                    <a class="block hover:text-white" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">{{ __('site.whatsapp_contact') }}</a>
                @endif
                    <a class="block hover:text-white" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                    <span class="block">{{ __('site.locations') }}</span>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    @if($facebookUrl)<a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-sm font-bold transition hover:scale-110 hover:bg-[#881C27]">f</a>@endif
                    @if($instagramUrl)<a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-xs font-bold transition hover:scale-110 hover:bg-[#881C27]">◎</a>@endif
                    @if($tiktokUrl)<a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-xs font-bold transition hover:scale-110 hover:bg-[#881C27]">♪</a>@endif
                    @if($youtubeUrl)<a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-xs font-bold transition hover:scale-110 hover:bg-[#881C27]">▶</a>@endif
                </div>
            </div>

            <div>
                <h2 class="font-semibold">{{ __('site.shop') }}</h2>
                <div class="mt-4 grid gap-3 text-sm text-white/65">
                    <a class="hover:text-white" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
                    <a class="hover:text-white" href="{{ route('home', ['locale' => app()->getLocale()]) }}#categories">{{ __('site.categories') }}</a>
                    <a class="hover:text-white" href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
                </div>
            </div>

            <div>
                <h2 class="font-semibold">{{ __('site.support') }}</h2>
                <div class="mt-4 grid gap-3 text-sm text-white/65">
                    <a class="hover:text-white" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => $measurementGuideSlug]) }}">{{ __('site.measurement_guide') }}</a>
                    @auth
                        <a class="hover:text-white" href="{{ route('orders.index', ['locale' => app()->getLocale()]) }}">{{ __('orders.orders') }}</a>
                    @endauth
                    <a class="hover:text-white" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => $contactSlug]) }}">{{ __('site.contact') }}</a>
                </div>
            </div>

            <div>
                <h2 class="font-semibold">{{ __('site.company') }}</h2>
                <div class="mt-4 grid gap-3 text-sm text-white/65">
                    <a class="hover:text-white" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')]) }}">{{ __('site.about') }}</a>
                    <a class="hover:text-white" href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">{{ __('content.blog') }}</a>
                    <a class="hover:text-white" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'privacy-policy' : (app()->getLocale() === 'fa' ? 'سیاست-حریم-خصوصی' : 'د-محرمیت-تګلاره')]) }}">{{ __('site.privacy_policy') }}</a>
                </div>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-8 sm:mt-12 sm:pt-10">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="text-lg font-semibold sm:text-xl">{{ __('site.newsletter') }}</h2>
                <p class="mt-2 text-sm text-white/60 sm:text-base">{{ __('site.subscribe_text') }}</p>

                @if(session('newsletter_status'))
                    <div class="mt-4 rounded-lg border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('newsletter_status') }}</div>
                @endif

                <form method="POST" action="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" class="mx-auto mt-5 flex max-w-md flex-col gap-2 sm:flex-row sm:gap-3">
                    @csrf
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="absolute -start-[9999px] h-px w-px opacity-0" aria-hidden="true">
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="254"
                        autocomplete="email"
                        placeholder="your@email.com"
                        class="min-h-11 flex-1 rounded-lg border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white outline-none placeholder:text-white/40 focus:border-white/50 sm:min-h-12"
                    >
                    <button type="submit" class="base44-gradient-cta min-h-11 whitespace-nowrap rounded-lg px-5 py-2.5 text-sm font-semibold transition sm:min-h-12">
                        {{ __('site.subscribe') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:justify-between">
            <span>© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</span>
            <span>{{ __('site.footer_tagline') }}</span>
        </div>
    </div>
</footer>

<nav class="fixed inset-x-0 bottom-0 z-50 flex border-t border-gray-200 bg-white md:hidden" style="padding-bottom:max(env(safe-area-inset-bottom), 0px)">
    @foreach ([
        ['home', __('site.home'), route('home', ['locale' => app()->getLocale()]), request()->routeIs('home')],
        ['bag', __('site.shop'), route('shop', ['locale' => app()->getLocale()]), request()->routeIs('shop', 'categories.*', 'collections.*', 'products.*')],
        ['bag', __('commerce.cart'), route('cart', ['locale' => app()->getLocale()]), request()->routeIs('cart')],
        ['user', __('auth.account'), auth()->check() ? route('account', ['locale' => app()->getLocale()]) : route('login', ['locale' => app()->getLocale()]), request()->routeIs('account', 'login')],
    ] as [$icon, $label, $href, $active])
        <a href="{{ $href }}" @class([
            'relative flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-[10px] font-medium',
            'text-[#881C27]' => $active,
            'text-gray-500' => ! $active,
        ])>
            @if($active)<span class="absolute inset-x-1/2 top-0 h-0.5 w-6 -translate-x-1/2 rounded-b bg-[#881C27]"></span>@endif
            <x-icon :name="$icon" class="h-5 w-5" />
            <span>{{ $label }}</span>
        </a>
    @endforeach
</nav>
</body>
</html>
