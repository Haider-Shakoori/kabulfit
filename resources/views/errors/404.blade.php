<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,follow">
    <title>404 | KabulFit</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<main class="section"><div class="container prose-narrow"><p class="eyebrow">404</p><h1>Page not found</h1><p>The page may have moved or no longer exists.</p><a class="button button-primary" href="/{{ config('kabulfit.default_locale') }}">KabulFit home</a></div></main>
</body>
</html>
