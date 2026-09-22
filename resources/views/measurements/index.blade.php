@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('measurements.custom_tailoring') }}</p>
        <h1>{{ __('measurements.profiles') }}</h1>
        <p>{{ __('measurements.profiles_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <x-form-errors />
        @if (session('status'))<p class="status-pill">{{ session('status') }}</p>@endif

        <div class="button-row">
            @foreach (['perahan_tunban', 'dress', 'waistcoat'] as $type)
                <a class="button button-secondary" href="{{ route('measurements.create', ['locale' => app()->getLocale(), 'garment_type' => $type]) }}">
                    {{ __('measurements.garment_'.$type) }}
                </a>
            @endforeach
        </div>

        <div class="address-list">
            @forelse ($profiles as $profile)
                <article class="address-card">
                    <div class="address-card-heading">
                        <strong>{{ $profile->name }}</strong>
                        @if ($profile->is_default)<span class="status-pill">{{ __('measurements.default') }}</span>@endif
                    </div>
                    <p>{{ __('measurements.garment_'.$profile->garment_type) }} · {{ strtoupper($profile->display_unit) }}</p>
                    <p>{{ trans_choice('measurements.value_count', $profile->values->count(), ['count' => $profile->values->count()]) }}</p>
                    <div class="button-row">
                        <a class="button button-secondary" href="{{ route('measurements.edit', ['locale' => app()->getLocale(), 'profile' => $profile]) }}">{{ __('measurements.edit_profile') }}</a>
                        <form method="post" action="{{ route('measurements.destroy', ['locale' => app()->getLocale(), 'profile' => $profile]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-button danger-link" type="submit">{{ __('measurements.delete_profile') }}</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-state">{{ __('measurements.no_profiles') }}</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
