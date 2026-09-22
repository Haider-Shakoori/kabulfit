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
        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif

        <div class="section-heading">
            <div><h2>{{ __('measurements.profiles') }}</h2></div>
            <a class="button button-primary" href="{{ route('measurements.create', ['locale' => app()->getLocale()]) }}">{{ __('measurements.new_profile') }}</a>
        </div>

        <div class="account-grid">
            <div class="account-main">
                @forelse ($profiles as $profile)
                    <article class="account-panel">
                        <h2>{{ $profile->name }}</h2>
                        <p>{{ __('measurements.garment_type') }}: {{ __('measurements.garment_'.$profile->garment_type) }} · {{ $profile->display_unit }}</p>
                        @if ($profile->is_default)<p><strong>{{ __('measurements.default_profile') }}</strong></p>@endif
                        <dl class="account-facts">
                            @foreach ($profile->values as $value)
                                <div>
                                    <dt>{{ $value->definition->translation()?->name }}</dt>
                                    <dd>{{ \App\Support\Measurements\MeasurementConverter::fromCm($value->value_cm, $profile->display_unit) }} {{ $profile->display_unit }}</dd>
                                </div>
                            @endforeach
                        </dl>
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
                    <div class="account-panel">
                        <p>{{ __('measurements.no_profiles') }}</p>
                        <a class="button button-primary" href="{{ route('measurements.create', ['locale' => app()->getLocale()]) }}">{{ __('measurements.new_profile') }}</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
