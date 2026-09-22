@extends('layouts.app')

@section('content')
@php
    $action = $profile
        ? route('measurements.update', ['locale' => app()->getLocale(), 'profile' => $profile])
        : route('measurements.store', ['locale' => app()->getLocale()]);
    $guide = $definitions->first()?->translation()?->guide_image_path;
@endphp

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('measurements.garment_'.$garmentType) }}</p>
        <h1>{{ $profile ? __('measurements.edit_profile') : __('measurements.new_profile') }}</h1>
        <p>{{ __('measurements.form_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container measurement-editor">
        <x-form-errors />
        <div class="measurement-guide-card">
            @if ($guide)
                <img src="{{ asset($guide) }}" width="640" height="720" alt="{{ __('measurements.guide_alt', ['garment' => __('measurements.garment_'.$garmentType)]) }}">
            @endif
            <div>
                <h2>{{ __('measurements.guide') }}</h2>
                <p>{{ __('measurements.guide_text') }}</p>
                <div class="button-row">
                    <a class="button button-secondary" href="{{ request()->fullUrlWithQuery(['unit' => 'cm']) }}">CM</a>
                    <a class="button button-secondary" href="{{ request()->fullUrlWithQuery(['unit' => 'in']) }}">IN</a>
                </div>
            </div>
        </div>

        <form method="post" action="{{ $action }}" class="auth-card measurement-form">
            @csrf
            @if ($profile) @method('PUT') @endif
            <input type="hidden" name="garment_type" value="{{ $garmentType }}">
            <input type="hidden" name="display_unit" value="{{ $unit }}">

            <label>{{ __('measurements.profile_name') }}
                <input name="name" required maxlength="100" value="{{ old('name', $profile?->name) }}">
            </label>

            <label class="filter-check">
                <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $profile?->is_default))>
                <span>{{ __('measurements.make_default') }}</span>
            </label>

            <div class="measurement-fields">
                @foreach ($definitions as $definition)
                    @php($name = $definition->translation()?->name ?? $definition->code)
                    <label class="measurement-field">
                        <span><strong>{{ $name }}</strong><small>{{ $definition->translation()?->instructions }}</small></span>
                        <input type="hidden" name="measurements[{{ $loop->index }}][code]" value="{{ $definition->code }}">
                        <input
                            name="measurements[{{ $loop->index }}][value]"
                            type="number"
                            inputmode="decimal"
                            step="{{ \App\Support\Measurements\MeasurementConverter::fromCm($definition->step_cm, $unit) }}"
                            min="{{ \App\Support\Measurements\MeasurementConverter::fromCm($definition->min_cm, $unit) }}"
                            max="{{ \App\Support\Measurements\MeasurementConverter::fromCm($definition->max_cm, $unit) }}"
                            value="{{ old('measurements.'.$loop->index.'.value', $values[$definition->code] ?? '') }}"
                            required
                        >
                        <small>{{ \App\Support\Measurements\MeasurementConverter::fromCm($definition->min_cm, $unit) }}–{{ \App\Support\Measurements\MeasurementConverter::fromCm($definition->max_cm, $unit) }} {{ $unit }}</small>
                    </label>
                @endforeach
            </div>

            <button class="button button-primary" type="submit">{{ __('measurements.save_profile') }}</button>
        </form>
    </div>
</section>
@endsection
