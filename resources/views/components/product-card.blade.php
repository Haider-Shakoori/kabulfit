@php($translation = $product->translation())
@php($media = $product->primaryMedia)
<article class="product-card">
    <a class="product-media" href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}">
        @if ($product->is_featured)
            <span class="product-badge">{{ __('site.featured_label') }}</span>
        @endif
        @if ($media)
            <x-responsive-product-image
                :media="$media"
                :alt="$media->translation()?->alt_text"
                sizes="(max-width: 430px) 100vw, (max-width: 1024px) 50vw, 25vw"
            />
        @else
            <span class="product-media-pattern" aria-hidden="true"></span>
            <span class="product-monogram" aria-hidden="true">KF</span>
        @endif
    </a>
    <div class="product-card-body">
        <p class="product-category">{{ $product->category?->translation()?->name }}</p>
        <h3><a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">{{ $translation?->name }}</a></h3>
        <p class="muted product-description">{{ $translation?->short_description }}</p>
        <div class="product-card-footer">
            <strong class="product-price-card">{{ $product->formattedPrice() }}</strong>
            <span class="stock {{ $product->isInStock() ? 'in-stock' : 'out-stock' }}">
                {{ $product->isInStock() ? __('site.in_stock') : __('site.out_of_stock') }}
            </span>
        </div>
    </div>
</article>
