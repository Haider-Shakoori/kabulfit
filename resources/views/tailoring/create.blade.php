@extends('layouts.app')

@section('content')
@php($translation = $product->translation())
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('measurements.custom_tailoring') }}</p>
        <h1>{{ __('measurements.tailor_product', ['product' => $translation?->name]) }}</h1>
        <p>{{ __('measurements.tailoring_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container auth-shell">
        <div class="auth-card">
            <x-form-errors />

            @if ($profiles->isEmpty())
                <p>{{ __('measurements.no_matching_profile') }}</p>
                <a class="button button-primary" href="{{ route('measurements.create', ['locale' => app()->getLocale(), 'garment_type' => $product->measurement_garment_type]) }}">
                    {{ __('measurements.create_profile_first') }}
                </a>
            @else
                <form method="post" action="{{ route('tailoring.store', ['locale' => app()->getLocale(), 'slug' => $translation?->slug]) }}">
                    @csrf

                    @if ($product->variants->isNotEmpty())
                        <label>{{ __('commerce.option') }}
                            <select name="variant_sku" required>
                                @foreach ($product->variants->where('is_active', true) as $variant)
                                    <option value="{{ $variant->sku }}" @disabled($variant->availableQuantity() < 1)>
                                        {{ $variant->option_key }} — {{ $product->formattedPrice($variant->currentPriceMinor()) }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    @endif

                    <label>{{ __('measurements.choose_profile') }}
                        <select name="measurement_profile_uuid" required>
                            @foreach ($profiles as $profile)
                                <option value="{{ $profile->uuid }}">{{ $profile->name }}@if($profile->is_default) — {{ __('measurements.default') }}@endif</option>
                            @endforeach
                        </select>
                    </label>

                    <label>{{ __('measurements.notes') }}
                        <textarea name="notes" rows="5" maxlength="2000" placeholder="{{ __('measurements.notes_placeholder') }}"></textarea>
                    </label>

                    <button class="button button-primary" type="submit">{{ __('measurements.add_tailored_to_cart') }}</button>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection
