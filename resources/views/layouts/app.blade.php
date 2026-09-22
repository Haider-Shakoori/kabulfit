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
<body>
@php($contactEmail = app(AppServicesSettingsSiteSettings::class)->get('site.contact_email', 'info@kabulfit.com'))
@php($isHome = request()->routeIs('home'))
<a class="skip-link" href="#main-content">{{ __('site.skip_to_content') }}</a>

<header class="site-header live-site-header {{ $isHome ? 'is-home' : '' }}"
        x-data="{ open: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 80"
        :class="{ 'is-scrolled': scrolled }"
        @keydown.escape.window="open = false">
    <div class="announcement live-announcement">
        <div class="container live-announcement-inner">
            <span>{{ __('site.announcement') }}</span>
            <div class="locale-switcher live-top-languages" aria-label="{{ __('site.language') }}">
                @foreach (($seo->alternates ?? []) as $locale => $href)
                    <a href="{{ $href }}" hreflang="{{ $locale }}" lang="{{ $locale }}" @class(['active' => app()->getLocale() === $locale]) aria-current="{{ app()->getLocale() === $locale ? 'page' : 'false' }}">
                        {{ $locale === 'en' ? 'English' : ($locale === 'fa' ? 'دری' : 'پښتو') }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container nav-shell live-nav-shell" @click.outside="open = false">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="live-brand" aria-label="{{ __('site.kabulfit_home') }}">
            <img src="{{ asset('images/kabulfit-live/logo-header.png') }}" width="310" height="120" alt="KabulFit" fetchpriority="high">
        </a>

        <nav id="primary-nav" class="primary-nav live-primary-nav" :class="{ 'is-open': open }" aria-label="{{ __('site.primary_navigation') }}">
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a>
            <a @click="open = false" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.measurement_guide') }}</a>
            <a @click="open = false" href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')]) }}">{{ __('site.about') }}</a>
            <a @click="open = false" href="#contact">{{ __('site.contact') }}</a>
        </nav>

        <div class="nav-actions live-nav-actions">
            <a class="live-action-link" href="{{ route('wishlist', ['locale' => app()->getLocale()]) }}">{{ __('commerce.wishlist') }}</a>
            <a class="live-action-link" href="{{ route('cart', ['locale' => app()->getLocale()]) }}">{{ __('commerce.cart') }}</a>
            @auth
                @if (auth()->user()->hasPermission('tailoring.work'))
                    <a class="live-action-link live-role-link" href="{{ route('tailor.index', ['locale' => app()->getLocale()]) }}">{{ __('tailor.nav') }}</a>
                @endif
                @if (auth()->user()->hasPermission('admin.access'))
                    <a class="live-action-link live-role-link" href="{{ route('admin.dashboard', ['locale' => app()->getLocale()]) }}">Admin</a>
                @endif
                <a class="live-action-link" href="{{ route('account', ['locale' => app()->getLocale()]) }}">{{ __('auth.account') }}</a>
            @else
                <a class="live-action-link" href="{{ route('login', ['locale' => app()->getLocale()]) }}">{{ __('auth.login') }}</a>
            @endauth

            <button class="mobile-toggle" type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="primary-nav" aria-label="{{ __('site.menu') }}">
                <span class="mobile-toggle-lines" aria-hidden="true"><span></span><span></span><span></span></span>
            </button>
        </div>
    </div>
</header>

<main id="main-content">
    @yield('content')
</main>

<footer id="contact" class="site-footer live-footer">
    <div class="container live-footer-grid">
        <div class="live-footer-intro">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="live-footer-brand" aria-label="{{ __('site.kabulfit_home') }}">
                <img src="{{ asset('images/kabulfit-live/logo-footer.png') }}" width="310" height="120" alt="KabulFit" loading="lazy">
            </a>
            <p>{{ __('site.footer_intro') }}</p>
            <div class="live-footer-contact">
                <a href="https://wa.me/93794120017" rel="noopener noreferrer" target="_blank">{{ __('site.whatsapp_contact') }}</a>
                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                <span>{{ __('site.locations') }}</span>
            </div>
        </div>

        <div class="live-footer-links">
            <h2>{{ __('site.shop') }}</h2>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#categories">{{ __('site.categories') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
        </div>

        <div class="live-footer-links">
            <h2>{{ __('site.support') }}</h2>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.measurement_guide') }}</a>
            <a href="{{ route('orders.index', ['locale' => app()->getLocale()]) }}">{{ __('orders.orders') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#contact">{{ __('site.contact') }}</a>
        </div>

        <div class="live-footer-links">
            <h2>{{ __('site.company') }}</h2>
            <a href="{{ route('content.page', ['locale' => app()->getLocale(), 'slug' => app()->getLocale() === 'en' ? 'about' : (app()->getLocale() === 'fa' ? 'درباره' : 'زموږ-په-اړه')]) }}">{{ __('site.about') }}</a>
            <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">{{ __('content.blog') }}</a>
            <a href="https://www.facebook.com/KabulFitTailoring/" rel="noopener noreferrer" target="_blank">Facebook</a>
            <a href="http://www.youtube.com/@Kabulfit" rel="noopener noreferrer" target="_blank">YouTube</a>
        </div>
    </div>
    <div class="container footer-bottom live-footer-bottom">
        <span>© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</span>
        <span>{{ __('site.footer_tagline') }}</span>
    </div>
</footer>
</body>
</html>
