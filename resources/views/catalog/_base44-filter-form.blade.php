<form method="GET" action="{{ $action }}" class="space-y-8">
    @if(! empty($lockedCategory))
        <input type="hidden" name="category" value="{{ $lockedCategory }}">
    @endif
    @if(! empty($lockedCollection))
        <input type="hidden" name="collection" value="{{ $lockedCollection }}">
    @endif

    <div>
        <label for="catalog-q" class="mb-3 block text-sm font-semibold">{{ __('site.search_products') }}</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-3 grid place-items-center text-gray-400">⌕</span>
            <input id="catalog-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-lg border border-gray-200 bg-white py-2.5 ps-10 pe-3 text-sm outline-none transition focus:border-[#881C27]">
        </div>
    </div>

    @if(empty($lockedCategory))
        <div>
            <p class="mb-3 text-sm font-semibold">{{ $categoriesLabel }}</p>
            <div class="space-y-3">
                @foreach($filterOptions['categories'] as $option)
                    @php($optionTranslation = $option->translation())
                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $optionTranslation?->slug }}"
                            @checked($selectedCategories->contains($optionTranslation?->slug))
                            class="h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]"
                        >
                        <span class="text-sm text-gray-700 transition hover:text-[#881C27]">{{ $optionTranslation?->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <p class="mb-3 text-sm font-semibold">{{ $priceLabel }}</p>
        <div class="grid grid-cols-2 gap-2">
            <label>
                <span class="sr-only">{{ __('site.min_price') }}</span>
                <input inputmode="decimal" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="{{ __('site.min_price') }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-[#881C27]">
            </label>
            <label>
                <span class="sr-only">{{ __('site.max_price') }}</span>
                <input inputmode="decimal" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="{{ __('site.max_price') }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-[#881C27]">
            </label>
        </div>
    </div>

    <div>
        <p class="mb-3 text-sm font-semibold">{{ $sizesLabel }}</p>
        <div class="flex flex-wrap gap-2">
            @foreach($filterOptions['sizes'] as $size)
                <label class="cursor-pointer">
                    <input type="checkbox" name="sizes[]" value="{{ $size->code }}" @checked($selectedSizes->contains(strtoupper($size->code))) class="peer sr-only">
                    <span class="inline-flex min-w-10 items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition peer-checked:border-transparent peer-checked:bg-gradient-to-r peer-checked:from-[#881C27] peer-checked:to-[#2A6867] peer-checked:text-white">{{ $size->code }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label for="catalog-color" class="mb-3 block text-sm font-semibold">{{ __('site.color') }}</label>
        <select id="catalog-color" name="color" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#881C27]">
            <option value="">{{ __('site.all_colors') }}</option>
            @foreach($filterOptions['colors'] as $color)
                @php($colorTranslation = $color->translation())
                <option value="{{ $colorTranslation?->slug }}" @selected(($filters['color'] ?? '') === $colorTranslation?->slug)>{{ $colorTranslation?->name }}</option>
            @endforeach
        </select>
    </div>

    <label class="flex cursor-pointer items-center gap-3">
        <input type="checkbox" name="in_stock" value="1" @checked(! empty($filters['in_stock'])) class="h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]">
        <span class="text-sm font-medium text-gray-700">{{ __('site.in_stock_only') }}</span>
    </label>

    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}">

    <div class="grid gap-2">
        <button type="submit" class="w-full rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">{{ __('site.apply_filters') }}</button>
        @if($activeFilterCount > 0)
            <a href="{{ $action }}" class="inline-flex w-full items-center justify-center rounded-full border-2 border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-transparent hover:bg-gradient-to-r hover:from-[#881C27] hover:to-[#2A6867] hover:text-white">{{ $clearLabel }}</a>
        @endif
    </div>
</form>
