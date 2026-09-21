<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<header class="site-header" x-data="{ open: false }">
    <div class="announcement">{{ __('site.announcement') }}</div>
    <div class="container nav-shell">
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="brand" aria-label="KabulFit home">
            <span class="brand-mark" aria-hidden="true">K</span>
            <span>KabulFit</span>
        </a>

        <button class="mobile-toggle" type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="primary-nav">
            <span class="sr-only">{{ __('site.menu') }}</span>
            <span aria-hidden="true">☰</span>
        </button>

        <nav id="primary-nav" class="primary-nav" :class="{ 'is-open': open }" aria-label="{{ __('site.primary_navigation') }}">
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#categories">{{ __('site.categories') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
        </nav>

        <div class="locale-switcher" aria-label="{{ __('site.language') }}">
            @foreach (($seo->alternates ?? []) as $locale => $href)
                <a href="{{ $href }}" hreflang="{{ $locale }}" lang="{{ $locale }}" @class(['active' => app()->getLocale() === $locale])>{{ strtoupper($locale) }}</a>
            @endforeach
        </div>
    </div>
</header>

<main id="main-content">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="brand footer-brand"><span class="brand-mark" aria-hidden="true">K</span><span>KabulFit</span></div>
            <p>{{ __('site.footer_intro') }}</p>
        </div>
        <div>
            <h2>{{ __('site.explore') }}</h2>
            <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring">{{ __('site.custom_tailoring') }}</a>
        </div>
        <div>
            <h2>{{ __('site.contact') }}</h2>
            <p>info@kabulfit.com</p>
            <p>{{ __('site.locations') }}</p>
        </div>
    </div>
    <div class="container footer-bottom">© {{ date('Y') }} KabulFit. {{ __('site.rights') }}</div>
</footer>
</body>
</html>
