<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173b2d">
    <meta name="color-scheme" content="light">
    <title>{{ $seo->title ?? 'KabulFit' }}</title>
    <meta name="description" content="{{ $seo->description ?? '' }}">
    <meta name="robots" content="{{ $seo->robots ?? 'index,follow' }}">
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
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? 'KabulFit' }}">
    <meta name="twitter:description" content="{{ $seo->description ?? '' }}">
    @if (! empty($seo->jsonLd))
        <script type="application/ld+json">{!! json_encode($seo->jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main-content">{{ __('site.skip_to_content') }}</a>

<header class="site-header" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="announcement">
        <div class="container announcement-inner">
            <span>{{ __('site.announcement') }}</span>
            <span class="announcement-separator" aria-hidden="true">•</span>
            <span>{{ __('site.global_shipping_text') }}</span>
        </div>
    </div>

    <div class="container nav-shell" @click.outside="open = false">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand" aria-label="{{ __('site.kabulfit_home') }}">
            <x-brand-mark />
            <span class="brand-word">KabulFit</span>
        </a>

        <nav id="primary-nav" class="primary-nav" :class="{ 'is-open': open }" aria-label="{{ __('site.primary_navigation') }}">
            <a @click="open = false" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}#categories">{{ __('site.categories') }}</a>
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}#story">{{ __('site.our_story') }}</a>
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
            <a @click="open = false" href="{{ route('home', ['locale' => app()->getLocale()]) }}#contact">{{ __('site.contact') }}</a>
        </nav>

        <div class="nav-actions">
            <div class="locale-switcher" aria-label="{{ __('site.language') }}">
                @foreach (($seo->alternates ?? []) as $locale => $href)
                    <a href="{{ $href }}" hreflang="{{ $locale }}" lang="{{ $locale }}" @class(['active' => app()->getLocale() === $locale]) aria-current="{{ app()->getLocale() === $locale ? 'page' : 'false' }}">{{ strtoupper($locale) }}</a>
                @endforeach
            </div>

            <button class="mobile-toggle" type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="primary-nav" aria-label="{{ __('site.menu') }}">
                <span class="mobile-toggle-lines" aria-hidden="true"><span></span><span></span><span></span></span>
            </button>
        </div>
    </div>
</header>

<main id="main-content">
    @yield('content')
</main>

<footer id="contact" class="site-footer">
    <div class="container footer-grid">
        <div class="footer-intro">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand footer-brand" aria-label="{{ __('site.kabulfit_home') }}">
                <x-brand-mark />
                <span class="brand-word">KabulFit</span>
            </a>
            <p>{{ __('site.footer_intro') }}</p>
        </div>
        <div>
            <h2>{{ __('site.explore') }}</h2>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#categories">{{ __('site.categories') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
        </div>
        <div>
            <h2>{{ __('site.contact') }}</h2>
            <a href="mailto:info@kabulfit.com">info@kabulfit.com</a>
            <p>{{ __('site.whatsapp_contact') }}</p>
            <p>{{ __('site.locations') }}</p>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</span>
        <span>{{ __('site.footer_tagline') }}</span>
    </div>
</footer>
</body>
</html>
