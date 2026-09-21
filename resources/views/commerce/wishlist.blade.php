@extends('layouts.app')

@section('content')
<section class="section">
    <div class="container">
        <h1>{{ __('commerce.wishlist') }}</h1>
        @if ($products->isEmpty())
            <p>{{ __('commerce.empty_wishlist') }}</p>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <div>
                        <x-product-card :product="$product" />
                        <form method="post" action="{{ route('wishlist.destroy', ['locale' => app()->getLocale(), 'slug' => $product->translation()?->slug]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="button button-secondary" type="submit">{{ __('commerce.remove') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
