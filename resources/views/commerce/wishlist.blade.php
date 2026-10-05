@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $emptyText = $locale === 'ps' ? 'هغه محصولات چې خوښوئ دلته خوندي کړئ.' : ($locale === 'fa' ? 'محصولاتی را که دوست دارید اینجا ذخیره کنید.' : 'Save items you love to your wishlist.');
    $exploreLabel = $locale === 'ps' ? 'محصولات وپلټئ' : ($locale === 'fa' ? 'مشاهده محصولات' : 'Explore Products');
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">{{ __('commerce.wishlist') }} ({{ $products->count() }})</h1>

        @if($products->isEmpty())
            <div class="py-20 text-center">
                <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-full bg-gray-100 text-gray-400">
                    <x-icon name="heart" class="h-12 w-12" />
                </div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('commerce.empty_wishlist') }}</h2>
                <p class="mt-2 text-gray-500">{{ $emptyText }}</p>
                <a href="{{ route('shop', ['locale' => $locale]) }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white transition hover:opacity-90">
                    {{ $exploreLabel }}
                    <x-icon name="arrow-right" class="h-4 w-4 rtl:rotate-180" />
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
                @foreach($products as $product)
                    <div class="relative">
                        <x-product-card :product="$product" />
                        <form method="POST" action="{{ route('wishlist.destroy', ['locale' => $locale, 'slug' => $product->translation()?->slug]) }}" class="absolute end-3 top-3 z-20">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="grid h-10 w-10 place-items-center rounded-full border border-red-100 bg-white/95 text-red-500 shadow-sm transition hover:bg-red-50 hover:text-red-700" aria-label="{{ __('commerce.remove') }}">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
