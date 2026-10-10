@php($translation = $product->translation())
@php($media = $product->primaryMedia)
@php($hasDiscount = ! is_null($product->sale_price_minor) && $product->sale_price_minor < $product->price_minor)
@php($discountPercent = $hasDiscount && $product->price_minor > 0 ? (int) round((1 - ($product->sale_price_minor / $product->price_minor)) * 100) : 0)
@php($colors = $product->variants->pluck('color')->filter()->unique('id')->take(5))
<article class="group animate-fadeInUp overflow-hidden rounded-[2rem] bg-white shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl">
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
        <a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" class="absolute inset-0">
            @if($media)
                <x-responsive-product-image
                    :media="$media"
                    :alt="$media->translation()?->alt_text"
                    sizes="(max-width: 430px) 50vw, (max-width: 1024px) 50vw, 25vw"
                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
            @else
                <span class="product-media-pattern absolute inset-0" aria-hidden="true"></span>
                <span class="absolute inset-0 grid place-items-center text-5xl font-bold text-[#2A6867]/30">KF</span>
            @endif
        </a>

        <div class="absolute start-3 top-3 flex flex-col gap-2">
            @if($hasDiscount)
                <span class="rounded-full bg-[#D91E36] px-3 py-1 text-xs font-semibold text-white">-{{ $discountPercent }}%</span>
            @endif
            @if($product->is_featured)
                <span class="rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">{{ __('site.featured_label') }}</span>
            @endif
        </div>

        <div class="absolute end-3 top-3 flex translate-x-4 flex-col gap-2 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100 rtl:-translate-x-4 rtl:group-hover:translate-x-0">
            <a href="{{ route('wishlist', ['locale' => app()->getLocale()]) }}" class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-gray-700 shadow-sm hover:bg-white" aria-label="{{ __('commerce.wishlist') }}"><x-icon name="heart" class="h-5 w-5" /></a>
            <a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-gray-700 shadow-sm hover:bg-white" aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}"><x-icon name="eye" class="h-5 w-5" /></a>
        </div>

        <div class="absolute inset-x-0 bottom-0 translate-y-4 p-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" class="base44-dark-cta flex w-full items-center justify-center gap-2 rounded-full px-4 py-3 text-sm font-semibold">
                <x-icon name="eye" class="h-5 w-5" /> Quick Look
            </a>
        </div>
    </div>

    <div class="p-4">
        @if($colors->isNotEmpty())
            <div class="mb-3 flex gap-1">
                @foreach($colors as $color)
                    <span class="h-4 w-4 rounded-full border-2 border-white shadow-sm" style="background-color: {{ $color->hex_value ?? '#d1d5db' }}" title="{{ $color->translation()?->name }}"></span>
                @endforeach
            </div>
        @endif

        <h3 class="mb-1 line-clamp-1 font-medium text-gray-900 transition-colors group-hover:text-[#D91E36]">
            <a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">{{ $translation?->name }}</a>
        </h3>

        <div class="flex items-center gap-2">
            <strong class="{{ $hasDiscount ? 'text-[#D91E36]' : 'text-gray-900' }}">{{ $product->formattedPrice() }}</strong>
            @if($hasDiscount)
                <span class="text-sm text-gray-600 line-through">{{ $product->formattedPrice($product->price_minor) }}</span>
            @endif
        </div>
    </div>
</article>
