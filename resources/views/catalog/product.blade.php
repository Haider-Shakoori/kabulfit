@extends('layouts.app')

@section('content')
@php
    $translation = $product->translation();
    $categoryTranslation = $product->category->translation();
    $mediaItems = $product->media->values();
    $imageData = $mediaItems->map(fn ($media) => [
        'url' => $media->url(),
        'alt' => $media->translation()?->alt_text ?: $translation?->name,
    ])->values()->all();

    if ($imageData === []) {
        $imageData = [[
            'url' => asset('images/kabulfit-live/hero-heritage.png'),
            'alt' => $translation?->name,
        ]];
    }

    $activeVariants = $product->variants->where('is_active', true)->values();
    $variantData = $activeVariants->map(function ($variant) use ($product) {
        $priceMinor = $variant->sale_price_minor ?? $variant->price_minor ?? $product->currentPriceMinor();
        return [
            'sku' => $variant->sku,
            'size' => $variant->size?->code ?? __('site.custom'),
            'color' => $variant->color?->translation()?->name ?? '',
            'color_code' => $variant->color?->code ?? '',
            'color_hex' => $variant->color?->hex_value ?? '#d1d5db',
            'price_minor' => $priceMinor,
            'price_display' => $product->formattedPrice($priceMinor),
            'quantity' => $variant->availableQuantity(),
            'label' => $variant->option_key,
        ];
    })->values()->all();

    $initialVariant = collect($variantData)->first(fn ($variant) => $variant['quantity'] > 0) ?? collect($variantData)->first();
    $hasDiscount = ! is_null($product->sale_price_minor) && $product->sale_price_minor < $product->price_minor;
    $discountPercent = $hasDiscount && $product->price_minor > 0
        ? (int) round((1 - ($product->sale_price_minor / $product->price_minor)) * 100)
        : 0;
    $colors = collect($variantData)->filter(fn ($variant) => $variant['color_code'] !== '')->unique('color_code')->values();
    $sizes = collect($variantData)->filter(fn ($variant) => $variant['size'] !== '')->unique('size')->values();

    $locale = app()->getLocale();
    $backLabel = $locale === 'ps' ? 'بېرته پلورنځي ته' : ($locale === 'fa' ? 'بازگشت به فروشگاه' : 'Back to Shop');
    $selectColorLabel = $locale === 'ps' ? 'رنګ وټاکئ' : ($locale === 'fa' ? 'انتخاب رنگ' : 'Select Color');
    $selectSizeLabel = $locale === 'ps' ? 'اندازه وټاکئ' : ($locale === 'fa' ? 'انتخاب اندازه' : 'Select Size');
    $sizeGuideLabel = $locale === 'ps' ? 'د اندازې لارښود' : ($locale === 'fa' ? 'راهنمای اندازه' : 'Size Guide');
    $descriptionLabel = $locale === 'ps' ? 'تفصیل' : ($locale === 'fa' ? 'توضیحات' : 'Description');
    $detailsLabel = $locale === 'ps' ? 'ټوکر او پاملرنه' : ($locale === 'fa' ? 'پارچه و نگهداری' : 'Fabric & Care');
    $reviewsLabel = $locale === 'ps' ? 'نظرونه' : ($locale === 'fa' ? 'نظرات' : 'Reviews');
    $sizeGuideData = $sizeGuides->map(fn ($guide) => [
        'title' => $guide->localizedTitle($locale),
        'image' => $guide->localizedImageUrl($locale),
        'description' => $guide->localizedDescription($locale),
    ])->values()->all();
@endphp

<div
    class="min-h-screen"
    x-data='{
        images: @js($imageData),
        variants: @js($variantData),
        selectedImage: 0,
        selectedSku: @js($initialVariant["sku"] ?? ""),
        selectedColor: @js($initialVariant["color_code"] ?? ""),
        selectedSize: @js($initialVariant["size"] ?? ""),
        quantity: 1,
        fullscreen: false,
        sizeGuideOpen: false,
        sizeGuideIndex: 0,
        sizeGuides: @js($sizeGuideData),
        tab: "description",
        get selectedVariant() {
            return this.variants.find(v => v.sku === this.selectedSku) || this.variants[0] || null;
        },
        chooseColor(code) {
            this.selectedColor = code;
            const match = this.variants.find(v => v.color_code === code && (!this.selectedSize || v.size === this.selectedSize) && v.quantity > 0)
                || this.variants.find(v => v.color_code === code && v.quantity > 0)
                || this.variants.find(v => v.color_code === code);
            if (match) {
                this.selectedSku = match.sku;
                this.selectedSize = match.size;
                this.quantity = Math.min(this.quantity, Math.max(match.quantity, 1));
            }
        },
        chooseSize(size) {
            this.selectedSize = size;
            const match = this.variants.find(v => v.size === size && (!this.selectedColor || v.color_code === this.selectedColor) && v.quantity > 0)
                || this.variants.find(v => v.size === size && v.quantity > 0)
                || this.variants.find(v => v.size === size);
            if (match) {
                this.selectedSku = match.sku;
                this.selectedColor = match.color_code;
                this.quantity = Math.min(this.quantity, Math.max(match.quantity, 1));
            }
        },
        total() {
            const unit = this.selectedVariant?.price_minor ?? {{ $product->currentPriceMinor() }};
            return ((unit * this.quantity) / 100).toFixed(2) + " {{ $product->currency }}";
        }
    }'
>
    <div class="mx-auto max-w-7xl px-4 py-8">
        <a href="{{ route('shop', ['locale' => app()->getLocale()]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $backLabel }}
        </a>

        <nav class="mb-8 flex flex-wrap items-center gap-2 text-sm text-gray-500" aria-label="{{ __('site.breadcrumbs') }}">
            <a class="hover:text-[#881C27]" href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('site.home') }}</a>
            <span>›</span>
            <a class="hover:text-[#881C27]" href="{{ route('shop', ['locale' => app()->getLocale()]) }}">{{ __('site.shop') }}</a>
            <span>›</span>
            <a class="hover:text-[#881C27]" href="{{ route('categories.show', ['locale' => app()->getLocale(), 'slug' => $categoryTranslation?->slug]) }}">{{ $categoryTranslation?->name }}</a>
            <span>›</span>
            <span class="text-gray-900">{{ $translation?->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-12">
            <div class="min-w-0 space-y-4">
                <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-100">
                    @forelse($mediaItems as $index => $media)
                        <button
                            type="button"
                            class="absolute inset-0 h-full w-full"
                            x-show="selectedImage === {{ $index }}"
                            x-transition.opacity
                            @click="fullscreen = true"
                        >
                            <x-responsive-product-image
                                :media="$media"
                                :alt="$media->translation()?->alt_text ?: $translation?->name"
                                :loading="$index === 0 ? 'eager' : 'lazy'"
                                :fetchpriority="$index === 0 ? 'high' : null"
                                sizes="(max-width: 1024px) 100vw, 50vw"
                                class="h-full w-full object-contain"
                            />
                        </button>
                    @empty
                        <button type="button" class="absolute inset-0 h-full w-full" @click="fullscreen = true">
                            <img src="{{ asset('images/kabulfit-live/hero-heritage.png') }}" alt="{{ $translation?->name }}" class="h-full w-full object-contain">
                        </button>
                    @endforelse

                    <div class="absolute start-4 top-4 flex flex-col gap-2">
                        @if($hasDiscount)
                            <span class="rounded-full bg-[#881C27] px-3 py-1 text-xs font-semibold text-white">-{{ $discountPercent }}%</span>
                        @endif
                        @if($product->is_featured)
                            <span class="rounded-full bg-[#D4AF37] px-3 py-1 text-xs font-semibold text-black">{{ __('site.featured_label') }}</span>
                        @endif
                    </div>

                    <template x-if="images.length > 1">
                        <div>
                            <button type="button" class="absolute start-4 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-xl shadow" @click.stop="selectedImage = selectedImage === 0 ? images.length - 1 : selectedImage - 1">‹</button>
                            <button type="button" class="absolute end-4 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-xl shadow" @click.stop="selectedImage = selectedImage === images.length - 1 ? 0 : selectedImage + 1">›</button>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-5 gap-3" x-show="images.length > 1">
                    <template x-for="(image, index) in images" :key="'thumb-' + image.url">
                        <button
                            type="button"
                            class="aspect-square overflow-hidden rounded-lg border-2 bg-gray-50 transition"
                            :class="selectedImage === index ? 'border-[#881C27] ring-2 ring-[#881C27]/20' : 'border-transparent'"
                            @click="selectedImage = index"
                        >
                            <img :src="image.url" alt="" class="h-full w-full object-contain" loading="lazy">
                        </button>
                    </template>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <p class="mb-2 text-sm font-medium uppercase tracking-[0.16em] text-[#2A6867]">{{ $categoryTranslation?->name }}</p>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">{{ $translation?->name }}</h1>

                    <div class="mt-4 flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-[#881C27]" x-text="selectedVariant?.price_display || @js($product->formattedPrice())"></span>
                        @if($hasDiscount)
                            <span class="text-xl text-gray-400 line-through">{{ $product->formattedPrice($product->price_minor) }}</span>
                        @endif
                    </div>
                </div>

                <div x-data="{ expanded: false }" class="text-gray-600 leading-relaxed">
                    <p x-show="expanded">{{ $translation?->description }}</p>
                    <p x-show="!expanded">{{ str($translation?->description)->limit(200) }}</p>
                    @if(str($translation?->description ?? '')->length() > 200)
                        <button type="button" class="mt-2 text-sm font-semibold text-[#881C27] hover:underline" @click="expanded = !expanded" x-text="expanded ? 'See Less' : 'See More'"></button>
                    @endif
                </div>

                @if($colors->isNotEmpty())
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold">{{ $selectColorLabel }}</p>
                            <span class="text-xs text-gray-500" x-text="selectedVariant?.color || ''"></span>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @foreach($colors as $color)
                                <button
                                    type="button"
                                    class="h-10 w-10 rounded-full border-2 transition"
                                    style="background-color: {{ $color['color_hex'] }}"
                                    :class="selectedColor === @js($color['color_code']) ? 'border-[#881C27] ring-4 ring-[#881C27]/20' : 'border-gray-200'"
                                    @click="chooseColor(@js($color['color_code']))"
                                    title="{{ $color['color'] }}"
                                ></button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($sizes->isNotEmpty())
                    <div>
                        <div class="mb-3 flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold">{{ $selectSizeLabel }}</p>
                            @if($sizeGuides->isNotEmpty())
                                <button type="button" @click="sizeGuideOpen = true; sizeGuideIndex = 0" class="inline-flex items-center gap-1 text-sm font-bold text-[#881C27] hover:underline">
                                    <x-icon name="ruler" class="h-4 w-4" />
                                    {{ $sizeGuideLabel }}
                                </button>
                            @else
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}#tailoring" class="inline-flex items-center gap-1 text-sm font-bold text-[#881C27] hover:underline">
                                    <x-icon name="ruler" class="h-4 w-4" />
                                    {{ $sizeGuideLabel }}
                                </a>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $size)
                                <button
                                    type="button"
                                    class="min-w-12 rounded-lg border px-4 py-2 text-sm font-medium transition"
                                    :class="selectedSize === @js($size['size']) ? 'border-transparent bg-[#881C27] text-white' : 'border-gray-300 text-gray-700 hover:border-[#881C27] hover:text-[#881C27]'"
                                    @click="chooseSize(@js($size['size']))"
                                >{{ $size['size'] }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($product->tailoring_enabled)
                    <a href="{{ route('tailoring.create', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 px-5 py-3 font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                        <x-icon name="ruler" class="h-5 w-5" />
                        {{ __('measurements.tailor_this_outfit') }}
                    </a>
                @endif

                <div>
                    <p class="mb-3 text-sm font-semibold">{{ __('commerce.quantity') }}</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center overflow-hidden rounded-lg border border-gray-200 bg-white">
                            <button type="button" class="grid h-11 w-11 place-items-center text-gray-600 disabled:opacity-30" @click="quantity = Math.max(1, quantity - 1)" :disabled="quantity <= 1"><x-icon name="minus" class="h-4 w-4" /></button>
                            <span class="w-12 text-center font-semibold" x-text="quantity"></span>
                            <button type="button" class="grid h-11 w-11 place-items-center text-gray-600 disabled:opacity-30" @click="quantity = Math.min((selectedVariant?.quantity || 99), quantity + 1)" :disabled="selectedVariant && quantity >= selectedVariant.quantity"><x-icon name="plus" class="h-4 w-4" /></button>
                        </div>
                        <span class="rounded-full border px-3 py-1 text-xs font-medium" :class="(selectedVariant?.quantity || {{ $product->availableStock() }}) > 0 ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'" x-text="(selectedVariant?.quantity || {{ $product->availableStock() }}) > 0 ? @js(__('site.in_stock')) : @js(__('site.out_of_stock'))"></span>
                    </div>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <div class="flex items-center justify-between text-lg">
                        <span class="text-gray-600">{{ __('commerce.total') }}:</span>
                        <strong class="text-2xl text-[#881C27]" x-text="total()"></strong>
                    </div>
                </div>

                @auth
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('cart.items.store', ['locale' => app()->getLocale()]) }}" class="min-w-0 flex-1">
                            @csrf
                            <input type="hidden" name="product_slug" value="{{ $translation?->slug }}">
                            @if($variantData !== [])
                                <input type="hidden" name="variant_sku" :value="selectedSku">
                            @endif
                            <input type="hidden" name="quantity" :value="quantity">
                            <button
                                type="submit"
                                class="flex min-h-14 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-lg font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="selectedVariant && selectedVariant.quantity < 1"
                            >
                                <x-icon name="bag" class="h-5 w-5" />
                                {{ __('commerce.add_to_cart') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('wishlist.store', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <input type="hidden" name="product_slug" value="{{ $translation?->slug }}">
                            <button type="submit" class="grid h-14 w-14 place-items-center rounded-xl border-2 border-gray-300 text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]" aria-label="{{ __('commerce.add_to_wishlist') }}"><x-icon name="heart" class="h-5 w-5" /></button>
                        </form>
                        <button type="button" class="grid h-14 w-14 place-items-center rounded-xl border-2 border-gray-300 text-gray-700 transition hover:border-[#2A6867] hover:text-[#2A6867]" @click="navigator.share ? navigator.share({title: @js($translation?->name), url: window.location.href}) : navigator.clipboard?.writeText(window.location.href)" aria-label="Share"><x-icon name="share" class="h-5 w-5" /></button>
                    </div>
                @else
                    <a class="flex min-h-14 w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-lg font-semibold text-white" href="{{ route('login', ['locale' => app()->getLocale()]) }}">{{ __('commerce.sign_in_to_buy') }}</a>
                @endauth

                <div class="grid grid-cols-3 gap-4 border-t border-gray-100 pt-5">
                    @foreach([
                        ['truck', __('site.global_shipping'), __('site.global_shipping_text')],
                        ['shield', __('site.quality_assured'), '100%'],
                        ['ruler', __('site.custom_sizing'), __('site.perfect_fit_guarantee')],
                    ] as [$iconName, $label, $description])
                        <div class="text-center">
                            <span class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]"><x-icon :name="$iconName" class="h-5 w-5" /></span>
                            <p class="mt-2 text-sm font-medium text-gray-900">{{ $label }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <section class="mt-16" x-data="{ tab: 'description' }">
            <div class="flex overflow-x-auto border-b border-gray-200">
                <button type="button" class="shrink-0 border-b-2 px-6 py-4 text-sm font-medium" :class="tab === 'description' ? 'border-[#881C27] text-[#881C27]' : 'border-transparent text-gray-500'" @click="tab = 'description'">{{ $descriptionLabel }}</button>
                <button type="button" class="shrink-0 border-b-2 px-6 py-4 text-sm font-medium" :class="tab === 'details' ? 'border-[#881C27] text-[#881C27]' : 'border-transparent text-gray-500'" @click="tab = 'details'">{{ $detailsLabel }}</button>
                <button type="button" class="shrink-0 border-b-2 px-6 py-4 text-sm font-medium" :class="tab === 'reviews' ? 'border-[#881C27] text-[#881C27]' : 'border-transparent text-gray-500'" @click="tab = 'reviews'">{{ $reviewsLabel }} (0)</button>
            </div>
            <div class="py-8">
                <div x-show="tab === 'description'" class="max-w-4xl whitespace-pre-line leading-8 text-gray-700">{{ $translation?->description }}</div>
                <div x-show="tab === 'details'" class="grid gap-8 md:grid-cols-2">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('site.quality_assured') }}</h3>
                        <ul class="mt-4 space-y-3 text-gray-600">
                            <li>• {{ __('site.quality_assured_text') }}</li>
                            <li>• {{ __('site.handcrafted_text') }}</li>
                            <li>• {{ __('site.custom_sizing_text') }}</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('site.measurement_ready') }}</h3>
                        <p class="mt-4 leading-7 text-gray-600">{{ __('site.measurement_ready_text') }}</p>
                    </div>
                </div>
                <div x-show="tab === 'reviews'" class="py-8 text-center">
                    <x-icon name="star" class="mx-auto h-12 w-12 text-gray-300" />
                    <p class="mt-4 text-gray-500">{{ $locale === 'ps' ? 'تر اوسه نظر نشته' : ($locale === 'fa' ? 'هنوز نظری ثبت نشده است' : 'No reviews yet') }}</p>
                </div>
            </div>
        </section>

        @if($relatedProducts->isNotEmpty())
            <section class="mt-16">
                <h2 class="mb-8 text-2xl font-bold text-gray-900">{{ __('site.related_products') }}</h2>
                <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($recentlyViewed->isNotEmpty())
            <section class="mt-16">
                <div class="mb-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2A6867]">{{ __('site.recently_viewed_label') }}</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ __('site.recently_viewed') }}</h2>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
                    @foreach($recentlyViewed as $recentProduct)
                        <x-product-card :product="$recentProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <div x-show="sizeGuideOpen" x-cloak class="fixed inset-0 z-[105] grid place-items-center p-4" @keydown.escape.window="sizeGuideOpen = false">
        <button type="button" class="absolute inset-0 bg-black/55" @click="sizeGuideOpen = false" aria-label="Close size guide"></button>
        <div x-transition class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-2xl font-bold text-gray-900">{{ $sizeGuideLabel }}</h2>
                <button type="button" @click="sizeGuideOpen = false" class="grid h-10 w-10 place-items-center rounded-full bg-gray-100"><x-icon name="close" class="h-5 w-5" /></button>
            </div>

            <template x-if="sizeGuides.length > 1">
                <div class="mb-5 flex flex-wrap gap-2">
                    <template x-for="(guide, index) in sizeGuides" :key="'guide-tab-' + index">
                        <button type="button" class="rounded-lg px-4 py-2 text-sm font-medium transition" :class="sizeGuideIndex === index ? 'bg-[#881C27] text-white' : 'bg-gray-100 text-gray-600'" @click="sizeGuideIndex = index" x-text="guide.title"></button>
                    </template>
                </div>
            </template>

            <template x-if="sizeGuides[sizeGuideIndex]">
                <div>
                    <img :src="sizeGuides[sizeGuideIndex].image" :alt="sizeGuides[sizeGuideIndex].title" class="w-full rounded-xl border border-gray-100 bg-gray-50 object-contain">
                    <p x-show="sizeGuides[sizeGuideIndex].description" class="mt-5 leading-7 text-gray-600" x-text="sizeGuides[sizeGuideIndex].description"></p>
                </div>
            </template>
        </div>
    </div>

    <div x-show="fullscreen" x-cloak class="fixed inset-0 z-[100] grid place-items-center bg-black/95 p-4" @keydown.escape.window="fullscreen = false">
        <button type="button" class="absolute end-5 top-5 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-2xl text-white" @click="fullscreen = false">×</button>
        <button type="button" class="absolute start-5 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-2xl text-gray-900" @click="selectedImage = selectedImage === 0 ? images.length - 1 : selectedImage - 1">‹</button>
        <img :src="images[selectedImage]?.url" :alt="images[selectedImage]?.alt" class="max-h-[92vh] max-w-[92vw] object-contain">
        <button type="button" class="absolute end-5 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-2xl text-gray-900" @click="selectedImage = selectedImage === images.length - 1 ? 0 : selectedImage + 1">›</button>
    </div>
</div>
@endsection
