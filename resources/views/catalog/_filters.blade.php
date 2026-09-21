<form class="catalog-filters" method="GET" action="{{ $action }}">
    <div class="catalog-search">
        <label for="catalog-q">{{ __('site.search_products') }}</label>
        <input id="catalog-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('site.search_placeholder') }}">
    </div>

    @if (empty($lockedCategory))
        <div>
            <label for="catalog-category">{{ __('site.category') }}</label>
            <select id="catalog-category" name="category">
                <option value="">{{ __('site.all_categories') }}</option>
                @foreach ($filterOptions['categories'] as $option)
                    @php($optionTranslation = $option->translation())
                    <option value="{{ $optionTranslation?->slug }}" @selected(($filters['category'] ?? '') === $optionTranslation?->slug)>{{ $optionTranslation?->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if (empty($lockedCollection))
        <div>
            <label for="catalog-collection">{{ __('site.collection') }}</label>
            <select id="catalog-collection" name="collection">
                <option value="">{{ __('site.all_collections') }}</option>
                @foreach ($filterOptions['collections'] as $option)
                    @php($optionTranslation = $option->translation())
                    <option value="{{ $optionTranslation?->slug }}" @selected(($filters['collection'] ?? '') === $optionTranslation?->slug)>{{ $optionTranslation?->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div>
        <label for="catalog-size">{{ __('site.size') }}</label>
        <select id="catalog-size" name="size">
            <option value="">{{ __('site.all_sizes') }}</option>
            @foreach ($filterOptions['sizes'] as $size)
                <option value="{{ $size->code }}" @selected(($filters['size'] ?? '') === $size->code)>{{ $size->code }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="catalog-color">{{ __('site.color') }}</label>
        <select id="catalog-color" name="color">
            <option value="">{{ __('site.all_colors') }}</option>
            @foreach ($filterOptions['colors'] as $color)
                @php($colorTranslation = $color->translation())
                <option value="{{ $colorTranslation?->slug }}" @selected(($filters['color'] ?? '') === $colorTranslation?->slug)>{{ $colorTranslation?->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="catalog-min-price">{{ __('site.min_price') }}</label>
        <input id="catalog-min-price" inputmode="decimal" name="min_price" value="{{ $filters['min_price'] ?? '' }}">
    </div>

    <div>
        <label for="catalog-max-price">{{ __('site.max_price') }}</label>
        <input id="catalog-max-price" inputmode="decimal" name="max_price" value="{{ $filters['max_price'] ?? '' }}">
    </div>

    <div>
        <label for="catalog-sort">{{ __('site.sort_by') }}</label>
        <select id="catalog-sort" name="sort">
            <option value="featured" @selected(($filters['sort'] ?? 'featured') === 'featured')>{{ __('site.sort_featured') }}</option>
            <option value="newest" @selected(($filters['sort'] ?? '') === 'newest')>{{ __('site.sort_newest') }}</option>
            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ __('site.sort_price_asc') }}</option>
            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ __('site.sort_price_desc') }}</option>
        </select>
    </div>

    <label class="catalog-checkbox">
        <input type="checkbox" name="in_stock" value="1" @checked(! empty($filters['in_stock']))>
        <span>{{ __('site.in_stock_only') }}</span>
    </label>

    <div class="catalog-filter-actions">
        <button class="button button-primary" type="submit">{{ __('site.apply_filters') }}</button>
        <a class="button button-secondary" href="{{ $action }}">{{ __('site.clear_filters') }}</a>
    </div>
</form>
