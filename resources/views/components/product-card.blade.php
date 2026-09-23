@props(['product', 'eager' => false])
@php($translation = $product->translation())
@php($media = $product->primaryMedia)
@php($secondaryMedia = $product->relationLoaded('media') ? $product->media->firstWhere('is_primary', false) : null)
@php($isBestSeller = str_starts_with($product->sku, 'LIVE-B-'))
@php($badge = $isBestSeller ? __('site.best_sellers') : ($product->is_featured ? __('site.featured_label') : null))
@php($badgeClass = $isBestSeller ? 'bg-[#00A651]' : 'bg-[#000000]')
@php($productUrl = route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]))

<div class="group relative cursor-pointer overflow-hidden rounded-[2rem] bg-white shadow-sm transition-all duration-500 hover:shadow-xl">
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
        @if ($media)
            <a href="{{ $productUrl }}" class="absolute inset-0 overflow-hidden" aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}">
                <img
                    src="{{ $media->url() }}"
                    alt="{{ $media->translation()?->alt_text }}"
                    class="h-full w-full scale-100 object-cover opacity-100 transition-transform duration-700"
                    loading="{{ $eager ? 'eager' : 'lazy' }}"
                    decoding="async"
                    @if($eager) fetchpriority="high" @endif
                >
            </a>

            @if ($secondaryMedia)
                <a href="{{ $productUrl }}" class="absolute inset-0 overflow-hidden opacity-0 transition-opacity duration-500 group-hover:opacity-100" aria-hidden="true" tabindex="-1">
                    <img
                        src="{{ $secondaryMedia->url() }}"
                        alt=""
                        class="h-full w-full object-cover opacity-100 transition-opacity duration-300"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            @endif
        @else
            <a href="{{ $productUrl }}" class="absolute inset-0 grid place-items-center bg-gradient-to-br from-stone-100 to-stone-200 text-4xl font-bold text-stone-400">KF</a>
        @endif

        @if ($badge)
            <div class="absolute left-3 top-3 flex flex-col gap-2">
                <div class="inline-flex items-center rounded-full border border-transparent px-3 py-1 text-xs font-semibold text-white shadow transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover:bg-primary/80 {{ $badgeClass }}">
                    {{ $badge }}
                </div>
            </div>
        @endif

        <div class="absolute right-3 top-3 flex translate-x-4 flex-col gap-2 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
            <a
                href="{{ route('wishlist', ['locale' => app()->getLocale()]) }}"
                class="inline-flex h-10 w-10 items-center justify-center gap-2 whitespace-nowrap rounded-full bg-white/90 text-sm font-medium text-secondary-foreground shadow-sm transition-colors hover:bg-white focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                aria-label="{{ __('commerce.wishlist') }}"
            >
                <x-icon name="heart" class="!h-5 !w-5" />
            </a>
            <a
                href="{{ $productUrl }}"
                class="inline-flex h-10 w-10 items-center justify-center gap-2 whitespace-nowrap rounded-full bg-white/90 text-sm font-medium text-secondary-foreground shadow-sm transition-colors hover:bg-white focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}"
            >
                <x-icon name="eye" class="!h-5 !w-5" />
            </a>
        </div>

        <div class="absolute inset-x-0 bottom-0 translate-y-4 p-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <a
                href="{{ $productUrl }}"
                class="inline-flex h-9 w-full items-center justify-center gap-2 whitespace-nowrap rounded-full bg-black/90 px-4 py-5 text-sm font-medium text-white shadow transition-colors hover:bg-black focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            >
                <x-icon name="eye" class="!h-5 !w-5" />
                {{ __('site.quick_look') }}
            </a>
        </div>
    </div>

    <div class="p-4">
        <h3 class="mb-1 line-clamp-1 font-medium text-gray-900 transition-colors group-hover:text-[#D91E36]">
            <a href="{{ $productUrl }}">{{ $translation?->name }}</a>
        </h3>
        <div class="flex items-center gap-2">
            <span class="font-bold text-gray-900">{{ $product->formattedPrice() }}</span>
        </div>
    </div>
</div>
