@php($translation = $product->translation())
<article class="product-card">
    <a class="product-media" href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" aria-label="{{ $translation?->name }}">
        <span aria-hidden="true">KF</span>
    </a>
    <div class="product-card-body">
        <p class="eyebrow">{{ $product->category?->translation()?->name }}</p>
        <h3><a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">{{ $translation?->name }}</a></h3>
        <p class="muted">{{ $translation?->short_description }}</p>
        <div class="product-card-footer">
            <strong>{{ $product->formattedPrice() }}</strong>
            <span class="stock {{ $product->stock_quantity > 0 ? 'in-stock' : 'out-stock' }}">
                {{ $product->stock_quantity > 0 ? __('site.in_stock') : __('site.out_of_stock') }}
            </span>
        </div>
    </div>
</article>
