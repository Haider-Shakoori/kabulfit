@php($translation = $product->translation())
@php($media = $product->primaryMedia)
@php($colors = $product->variants->pluck('color')->filter()->unique('id')->take(5))

<article class="group relative overflow-hidden rounded-[2rem] bg-white shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl">
    <a
        class="relative block aspect-[3/4] overflow-hidden bg-gray-100 [&_picture]:block [&_picture]:h-full [&_picture]:w-full [&_img]:h-full [&_img]:w-full [&_img]:object-cover [&_img]:transition-transform [&_img]:duration-700 group-hover:[&_img]:scale-110"
        href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}"
        aria-label="{{ __('site.view_product', ['product' => $translation?->name]) }}"
    >
        @if ($product->is_featured)
            <span class="absolute left-3 top-3 z-10 rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">
                {{ __('site.featured_label') }}
            </span>
        @endif

        @if ($media)
            <x-responsive-product-image
                :media="$media"
                :alt="$media->translation()?->alt_text"
                sizes="(max-width: 767px) 50vw, 25vw"
            />
        @else
            <span class="absolute inset-0 bg-gradient-to-br from-stone-100 to-stone-200" aria-hidden="true"></span>
            <span class="absolute inset-0 grid place-items-center text-4xl font-bold text-stone-400" aria-hidden="true">KF</span>
        @endif
    </a>

    <div class="p-4">
        <h3 class="mb-1 line-clamp-1 text-base font-medium text-gray-900 transition-colors group-hover:text-[#D91E36]">
            <a href="{{ route('products.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">
                {{ $translation?->name }}
            </a>
        </h3>

        <div class="flex items-center justify-between gap-3">
            <strong class="text-base font-bold text-gray-900">{{ $product->formattedPrice() }}</strong>

            @if ($colors->isNotEmpty())
                <span class="flex items-center -space-x-1" aria-label="{{ __('site.color') }}">
                    @foreach ($colors as $color)
                        <span
                            class="h-4 w-4 rounded-full border-2 border-white shadow-sm"
                            style="background-color: {{ $color->hex_value }}"
                            title="{{ $color->translation()?->name }}"
                        ></span>
                    @endforeach
                </span>
            @endif
        </div>
    </div>
</article>
