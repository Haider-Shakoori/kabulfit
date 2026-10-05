<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111827">
    <meta name="color-scheme" content="light">
    <title>{{ $seo->title ?? 'KabulFit Admin' }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/png" href="{{ asset('images/kabulfit-live/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
@php
    $adminLinks = [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => 'dashboard.view', 'match' => 'admin.dashboard'],
        ['icon' => 'package', 'label' => 'Products', 'route' => 'admin.products.index', 'permission' => 'products.manage', 'match' => 'admin.products.*'],
        ['icon' => 'bag', 'label' => 'Orders', 'route' => 'admin.orders.index', 'permission' => 'orders.manage', 'match' => 'admin.orders.*'],
        ['icon' => 'ruler', 'label' => 'Measurements', 'route' => 'admin.measurements.index', 'permission' => 'measurements.manage', 'match' => 'admin.measurements.*'],
        ['icon' => 'tag', 'label' => 'Categories', 'route' => 'admin.content.index', 'permission' => 'content.manage', 'match' => 'admin.categories.*'],
        ['icon' => 'users', 'label' => 'Customers', 'route' => 'admin.customers.index', 'permission' => 'customers.manage', 'match' => 'admin.customers.*'],
        ['icon' => 'ruler', 'label' => 'Measurement Guides', 'route' => 'admin.measurements.index', 'permission' => 'measurements.manage', 'match' => 'admin.measurement-guides.*'],
        ['icon' => 'ruler', 'label' => 'Size Guides', 'route' => 'admin.content.index', 'permission' => 'content.manage', 'match' => 'admin.size-guides.*'],
        ['icon' => 'truck', 'label' => 'Shipping Rates', 'route' => 'admin.settings.index', 'permission' => 'settings.manage', 'match' => 'admin.shipping-rates.*'],
        ['icon' => 'settings', 'label' => 'Settings', 'route' => 'admin.settings.index', 'permission' => 'settings.manage', 'match' => 'admin.settings.*'],
    ];
@endphp

<div x-data="{ mobileAdminOpen: false }" @keydown.escape.window="mobileAdminOpen = false">
    <header class="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between bg-gray-900 px-4 text-white shadow-lg lg:hidden">
        <button type="button" class="grid h-10 w-10 place-items-center rounded-lg hover:bg-gray-800" @click="mobileAdminOpen = true" aria-label="Open admin menu">
            <x-icon name="menu" class="h-6 w-6" />
        </button>
        <h1 class="text-base font-bold">Admin Dashboard</h1>
        <span class="h-10 w-10"></span>
    </header>

    <aside class="fixed inset-y-0 start-0 z-40 hidden w-64 flex-col bg-gray-900 text-white lg:flex">
        <div class="border-b border-gray-800 p-6">
            <h1 class="text-xl font-bold">Admin Panel</h1>
            <p class="mt-1 truncate text-sm text-gray-400">{{ auth()->user()->email }}</p>
        </div>

        <nav class="flex-1 overflow-y-auto p-4">
            @foreach($adminLinks as $item)
                @if(auth()->user()->hasPermission($item['permission']))
                    <a href="{{ route($item['route'], ['locale' => app()->getLocale()]) }}"
                       @class([
                           'mb-2 flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition',
                           'bg-gray-800' => request()->routeIs($item['match']),
                           'hover:bg-gray-800' => ! request()->routeIs($item['match']),
                       ])>
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="mt-auto border-t border-gray-800 p-4">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="mb-2 flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800">
                <x-icon name="home" class="h-5 w-5" />
                <span>Back to Store</span>
            </a>
            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800">
                    <x-icon name="logout" class="h-5 w-5" />
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div x-show="mobileAdminOpen" x-cloak class="fixed inset-0 z-[80] lg:hidden">
        <button type="button" class="absolute inset-0 bg-black/50" @click="mobileAdminOpen = false" aria-label="Close admin menu"></button>
        <aside x-transition class="absolute inset-y-0 start-0 flex w-72 max-w-[85vw] flex-col bg-gray-900 text-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-gray-800 p-6">
                <div>
                    <h2 class="text-lg font-bold">Admin Panel</h2>
                    <p class="mt-1 max-w-[190px] truncate text-sm text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg hover:bg-gray-800" @click="mobileAdminOpen = false">
                    <x-icon name="close" class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4">
                @foreach($adminLinks as $item)
                    @if(auth()->user()->hasPermission($item['permission']))
                        <a href="{{ route($item['route'], ['locale' => app()->getLocale()]) }}"
                           @click="mobileAdminOpen = false"
                           @class([
                               'mb-2 flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition',
                               'bg-gray-800' => request()->routeIs($item['match']),
                               'hover:bg-gray-800' => ! request()->routeIs($item['match']),
                           ])>
                            <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-auto border-t border-gray-800 p-4">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="mb-2 flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800">
                    <x-icon name="home" class="h-5 w-5" />
                    <span>Back to Store</span>
                </a>
                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800">
                        <x-icon name="logout" class="h-5 w-5" />
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <main class="min-h-screen pt-16 lg:ms-64 lg:pt-0">
        @yield('content')
    </main>
</div>
</body>
</html>
