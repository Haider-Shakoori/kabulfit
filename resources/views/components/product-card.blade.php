@php
    $translation = $product->translation();
    $media = $product->primaryMedia();
@endphp
<article class="product-card">
    <a class="product-media" href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}">
        @if ($product->is_featured)
            <span class="product-badge">{{ __('site.featured_label') }}</span>
        @endif
        @if ($media)
            <img src="{{ $media->url() }}" alt="{{ $media->translation()?->alt_text }}" width="{{ $media->width }}" height="{{ $media->height }}" loading="lazy" decoding="async">
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
            <span class="stock {{ $product->availableStock() > 0 ? 'in-stock' : 'out-stock' }}">
                {{ $product->availableStock() > 0 ? __('site.in_stock') : __('site.out_of_stock') }}
            </span>
        </div>
    </div>
</article>
