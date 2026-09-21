@props([
    'action',
    'facets',
    'hideCategory' => false,
    'hideCollection' => false,
])

@php
    $options = $facets['options']->keyBy('code');
@endphp

<form class="catalog-filters" method="get" action="{{ $action }}" aria-label="{{ __('site.catalog_filters') }}">
    <div class="filter-search">
        <label for="catalog-q">{{ __('site.search') }}</label>
        <input id="catalog-q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('site.search_products') }}">
    </div>

    @unless($hideCategory)
        <div>
            <label for="catalog-category">{{ __('site.category') }}</label>
            <select id="catalog-category" name="category">
                <option value="">{{ __('site.all_categories') }}</option>
                @foreach ($facets['categories'] as $category)
                    @php($translation = $category->translation($facets['locale']))
                    <option value="{{ $translation?->slug }}" @selected(request('category') === $translation?->slug)>{{ $translation?->name }}</option>
                @endforeach
            </select>
        </div>
    @endunless

    @unless($hideCollection)
        <div>
            <label for="catalog-collection">{{ __('site.collection') }}</label>
            <select id="catalog-collection" name="collection">
                <option value="">{{ __('site.all_collections') }}</option>
                @foreach ($facets['collections'] as $collection)
                    @php($translation = $collection->translation($facets['locale']))
                    <option value="{{ $translation?->slug }}" @selected(request('collection') === $translation?->slug)>{{ $translation?->name }}</option>
                @endforeach
            </select>
        </div>
    @endunless

    @foreach (['size', 'color', 'embroidery'] as $code)
        @if ($option = $options->get($code))
            <div>
                <label for="catalog-{{ $code }}">{{ $option->translation($facets['locale'])?->name }}</label>
                <select id="catalog-{{ $code }}" name="{{ $code }}">
                    <option value="">{{ __('site.any_option') }}</option>
                    @foreach ($option->values as $value)
                        <option value="{{ $value->code }}" @selected(request($code) === $value->code)>{{ $value->translation($facets['locale'])?->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    @endforeach

    <div>
        <label for="catalog-price-min">{{ __('site.min_price') }}</label>
        <input id="catalog-price-min" inputmode="numeric" type="number" min="0" name="price_min" value="{{ request('price_min') }}">
    </div>

    <div>
        <label for="catalog-price-max">{{ __('site.max_price') }}</label>
        <input id="catalog-price-max" inputmode="numeric" type="number" min="0" name="price_max" value="{{ request('price_max') }}">
    </div>

    <div>
        <label for="catalog-sort">{{ __('site.sort_by') }}</label>
        <select id="catalog-sort" name="sort">
            <option value="featured" @selected(request('sort', 'featured') === 'featured')>{{ __('site.sort_featured') }}</option>
            <option value="newest" @selected(request('sort') === 'newest')>{{ __('site.sort_newest') }}</option>
            <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('site.sort_price_asc') }}</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('site.sort_price_desc') }}</option>
            <option value="name" @selected(request('sort') === 'name')>{{ __('site.sort_name') }}</option>
        </select>
    </div>

    <label class="filter-checkbox">
        <input type="checkbox" name="in_stock" value="1" @checked(request('in_stock') === '1')>
        <span>{{ __('site.in_stock_only') }}</span>
    </label>

    <div class="filter-actions">
        <button class="button button-primary" type="submit">{{ __('site.apply_filters') }}</button>
        <a class="text-link" href="{{ $action }}">{{ __('site.clear_filters') }}</a>
    </div>
</form>
