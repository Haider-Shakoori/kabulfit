@extends('layouts.app')

@section('content')
<section class="section">
    <div class="container">
        <h1>{{ __('commerce.cart') }}</h1>

        @if (session('status'))<p class="status-pill">{{ session('status') }}</p>@endif

        @if ($cart->items->isEmpty())
            <p>{{ __('commerce.empty_cart') }}</p>
        @else
            <div class="commerce-list">
                @foreach ($cart->items as $item)
                    <article>
                        <div>
                            <strong>{{ $item->product->translation()?->name }}</strong>
                            <p>{{ $item->variant?->option_key }}</p>
                            @if ($item->tailoringRequest)
                                <span class="status-pill">{{ __('measurements.tailored') }}</span>
                                <p>{{ __('measurements.measurement_profile') }}: {{ $item->tailoringRequest->measurementProfile?->name }}</p>
                            @endif
                        </div>

                        <strong>{{ $item->product->formattedPrice($item->lineTotalMinor()) }}</strong>

                        @unless ($item->tailoringRequest)
                            <form method="post" action="{{ route('cart.items.update', ['locale' => app()->getLocale(), 'item' => $item]) }}">
                                @csrf
                                @method('PUT')
                                <input name="quantity" type="number" min="0" max="99" value="{{ $item->quantity }}">
                                <button class="button button-secondary">{{ __('commerce.quantity') }}</button>
                            </form>
                        @endunless

                        <form method="post" action="{{ route('cart.items.destroy', ['locale' => app()->getLocale(), 'item' => $item]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="button button-secondary">{{ __('commerce.remove') }}</button>
                        </form>
                    </article>
                @endforeach
            </div>

            <p><strong>{{ __('commerce.subtotal') }}: {{ $cart->items->first()->product->formattedPrice($cart->items->sum(fn ($item) => $item->lineTotalMinor())) }}</strong></p>
            <a class="button button-primary" href="{{ route('checkout', ['locale' => app()->getLocale()]) }}">{{ __('commerce.continue_checkout') }}</a>
        @endif
    </div>
</section>
@endsection
