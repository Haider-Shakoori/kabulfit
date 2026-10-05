@php
    $selectedCategories = collect($filters['categories'] ?? [])
        ->when(! empty($filters['category']), fn ($items) => $items->push($filters['category']))
        ->filter()
        ->unique()
        ->values();
    $selectedSizes = collect($filters['sizes'] ?? [])
        ->when(! empty($filters['size']), fn ($items) => $items->push($filters['size']))
        ->filter()
        ->map(fn ($size) => strtoupper($size))
        ->unique()
        ->values();
    $activeFilterCount = (! empty($filters['q']) ? 1 : 0)
        + $selectedCategories->count()
        + $selectedSizes->count()
        + (! empty($filters['color']) ? 1 : 0)
        + (! empty($filters['min_price']) || ! empty($filters['max_price']) ? 1 : 0)
        + (! empty($filters['in_stock']) ? 1 : 0);
    $locale = app()->getLocale();
    $searchPlaceholder = $locale === 'ps' ? 'محصولات وپلټئ...' : ($locale === 'fa' ? 'جستجوی محصولات...' : 'Search products...');
    $categoriesLabel = $locale === 'ps' ? 'کټګورۍ' : ($locale === 'fa' ? 'دسته‌بندی‌ها' : 'Categories');
    $sizesLabel = $locale === 'ps' ? 'اندازې' : ($locale === 'fa' ? 'اندازه‌ها' : 'Sizes');
    $productsLabel = $locale === 'ps' ? 'محصولات' : ($locale === 'fa' ? 'محصولات' : 'products');
    $clearLabel = $locale === 'ps' ? 'ټول فلټرونه پاک کړئ' : ($locale === 'fa' ? 'پاک کردن همه فیلترها' : 'Clear All Filters');
@endphp

<section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-4 text-white sm:py-5">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-2xl font-bold sm:text-3xl">{{ $pageTitle }}</h1>
        @if(! empty($pageDescription))
            <p class="mx-auto mt-1 max-w-2xl text-xs text-white/80 sm:text-sm">{{ $pageDescription }}</p>
        @endif
        @if(! empty($pageBadge))
            <span class="mt-3 inline-flex rounded-full bg-[#D4AF37] px-3 py-1 text-xs font-semibold text-black">{{ $pageBadge }}</span>
        @endif
    </div>
</section>

<section
    class="mx-auto max-w-7xl px-4 py-6 sm:py-8"
    x-data="{ filtersOpen: false, grid: 4 }"
>
    <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row">
        <aside class="hidden w-72 shrink-0 lg:block">
            <div class="sticky top-36 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ __('site.filter') }}</h2>
                    @if($activeFilterCount > 0)
                        <span class="rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-2.5 py-1 text-xs font-semibold text-white">{{ $activeFilterCount }}</span>
                    @endif
                </div>
                @include('catalog._base44-filter-form')
            </div>
        </aside>

        <main class="min-w-0 flex-1">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:mb-6 sm:p-4">
                <div class="flex items-center gap-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border-2 border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-transparent hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white lg:hidden"
                        @click="filtersOpen = true"
                    >
                        <span aria-hidden="true">☰</span>
                        {{ __('site.filter') }}
                        @if($activeFilterCount > 0)
                            <span class="rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-2 py-0.5 text-[11px] text-white">{{ $activeFilterCount }}</span>
                        @endif
                    </button>
                    <p class="text-xs text-gray-500 sm:text-sm">{{ $products->total() }} {{ $productsLabel }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ $action }}" class="flex items-center gap-2">
                        @foreach(request()->except(['sort', 'page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label class="sr-only" for="catalog-sort-toolbar">{{ __('site.sort_by') }}</label>
                        <select id="catalog-sort-toolbar" name="sort" class="w-40 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 sm:w-44 sm:text-sm" onchange="this.form.submit()">
                            <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ $locale === 'ps' ? 'تازه لومړی' : ($locale === 'fa' ? 'جدیدترین' : 'Newest First') }}</option>
                            <option value="featured" @selected(($filters['sort'] ?? '') === 'featured')>{{ __('site.sort_featured') }}</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ __('site.sort_price_asc') }}</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ __('site.sort_price_desc') }}</option>
                        </select>
                    </form>

                    <div class="hidden overflow-hidden rounded-lg border md:flex">
                        <button type="button" class="grid h-9 w-10 place-items-center" :class="grid === 3 ? 'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white' : 'bg-white text-gray-600'" @click="grid = 3" aria-label="Three columns">▦</button>
                        <button type="button" class="grid h-9 w-10 place-items-center" :class="grid === 4 ? 'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white' : 'bg-white text-gray-600'" @click="grid = 4" aria-label="Four columns">▦</button>
                    </div>
                </div>
            </div>

            @if($activeFilterCount > 0)
                <div class="mb-6 flex flex-wrap gap-2">
                    @if(! empty($filters['q']))
                        <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">Search: {{ $filters['q'] }}</span>
                    @endif
                    @foreach($selectedCategories as $slug)
                        @php($categoryOption = $filterOptions['categories']->first(fn ($item) => $item->translation()?->slug === $slug))
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">{{ $categoryOption?->translation()?->name ?? $slug }}</span>
                    @endforeach
                    @foreach($selectedSizes as $size)
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">{{ __('site.size') }}: {{ $size }}</span>
                    @endforeach
                    @if(! empty($filters['color']))
                        @php($colorOption = $filterOptions['colors']->first(fn ($item) => $item->translation()?->slug === $filters['color']))
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">{{ $colorOption?->translation()?->name ?? $filters['color'] }}</span>
                    @endif
                    <a href="{{ $action }}" class="inline-flex rounded-full border border-gray-300 px-3 py-1 text-xs font-medium text-gray-600 transition hover:border-[#881C27] hover:text-[#881C27]">{{ $clearLabel }}</a>
                </div>
            @endif

            @if($products->count())
                <div
                    class="grid grid-cols-2 gap-4 sm:gap-6"
                    :class="grid === 3 ? 'md:grid-cols-3' : 'md:grid-cols-4'"
                >
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-white py-20 text-center shadow-sm">
                    <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-gray-100 text-4xl text-gray-400">⌕</div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">{{ __('site.no_results') }}</h3>
                    <p class="mt-2 text-gray-500">{{ $locale === 'ps' ? 'خپل فلټرونه یا د لټون شرایط تنظیم کړئ' : ($locale === 'fa' ? 'فیلترها یا عبارات جستجو را تنظیم کنید' : 'Try adjusting your filters or search terms') }}</p>
                    <a href="{{ $action }}" class="mt-6 inline-flex rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white">{{ $clearLabel }}</a>
                </div>
            @endif

            @if($products->hasPages())
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </main>
    </div>

    <div x-show="filtersOpen" x-cloak class="fixed inset-0 z-[80] lg:hidden">
        <button type="button" class="absolute inset-0 bg-black/40" @click="filtersOpen = false" aria-label="Close filters"></button>
        <aside x-transition class="absolute inset-y-0 start-0 w-[88%] max-w-sm overflow-y-auto bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold">{{ __('site.filter') }}</h2>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100" @click="filtersOpen = false">×</button>
            </div>
            @include('catalog._base44-filter-form')
        </aside>
    </div>
</section>
