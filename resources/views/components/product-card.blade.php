@php($translation = $product->translation())
@php($media = $product->primaryMedia)
@php($secondaryMedia = $product->relationLoaded('media') ? $product->media->firstWhere('is_primary', false) : null)
@php($isBestSeller = str_starts_with($product->sku, 'LIVE-B-'))
@php($badge = $isBestSeller ? __('site.best_sellers') : ($product->is_featured ? __('site.featured_label') : null))
@php($badgeClass = $isBestSeller ? 'bg-[#00A651]' : 'bg-[#000000]')
@php($productUrl = route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]))

<article class="group relative cursor-pointer overflow-hidden rounded-[2rem] bg-white shadow-sm transition-all duration-500 hover:shadow-xl">
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
        <a
            href="{{ $productUrl }}"
            aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}"
            class="absolute inset-0 block"
        >
            @if ($media)
                <span class="absolute inset-0 overflow-hidden [&_picture]:block [&_picture]:h-full [&_picture]:w-full [&_img]:h-full [&_img]:w-full [&_img]:object-cover [&_img]:transition-transform [&_img]:duration-700 group-hover:[&_img]:scale-110">
                    <x-responsive-product-image
                        :media="$media"
                        :alt="$media->translation()?->alt_text"
                        sizes="(max-width: 767px) 50vw, 25vw"
                        loading="lazy"
                    />
                </span>

                @if ($secondaryMedia)
                    <span class="absolute inset-0 overflow-hidden opacity-0 transition-opacity duration-500 group-hover:opacity-100 [&_picture]:block [&_picture]:h-full [&_picture]:w-full [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
                        <x-responsive-product-image
                            :media="$secondaryMedia"
                            :alt="$secondaryMedia->translation()?->alt_text"
                            sizes="(max-width: 767px) 50vw, 25vw"
                        />
                    </span>
                @endif
            @else
                <span class="absolute inset-0 bg-gradient-to-br from-stone-100 to-stone-200" aria-hidden="true"></span>
                <span class="absolute inset-0 grid place-items-center text-4xl font-bold text-stone-400" aria-hidden="true">KF</span>
            @endif
        </a>

        @if ($badge)
            <span class="absolute left-3 top-3 z-10 rounded-full px-3 py-1 text-xs font-semibold text-white {{ $badgeClass }}">
                {{ $badge }}
            </span>
        @endif

        <div class="absolute right-3 top-3 z-20 flex translate-x-4 flex-col gap-2 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
            <a
                href="{{ route('wishlist', ['locale' => app()->getLocale()]) }}"
                class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-gray-800 shadow-sm transition hover:bg-white"
                aria-label="{{ __('commerce.wishlist') }}"
            >
                <x-icon name="heart" class="!h-5 !w-5" />
            </a>
            <a
                href="{{ $productUrl }}"
                class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-gray-800 shadow-sm transition hover:bg-white"
                aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}"
            >
                <x-icon name="eye" class="!h-5 !w-5" />
            </a>
        </div>

        <div class="absolute inset-x-0 bottom-0 z-20 translate-y-4 p-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <a
                href="{{ $productUrl }}"
                class="flex w-full items-center justify-center rounded-full bg-black/90 px-4 py-3 text-sm font-medium text-white shadow transition hover:bg-black"
            >
                <x-icon name="eye" class="!mr-2 !h-5 !w-5" />
                {{ __('site.quick_look') }}
            </a>
        </div>
    </div>

    <div class="p-4">
        <h3 class="mb-1 line-clamp-1 text-base font-medium text-gray-900 transition-colors group-hover:text-[#D91E36]">
            <a href="{{ $productUrl }}">{{ $translation?->name }}</a>
        </h3>
        <div class="flex items-center gap-2">
            <span class="text-base font-bold text-gray-900">{{ $product->formattedPrice() }}</span>
        </div>
    </div>
</article>
