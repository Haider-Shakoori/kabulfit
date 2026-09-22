@extends('layouts.app')

@section('content')
@php
    $translation = $tailoring->product->translation();
    $orderItem = $tailoring->orderItem;
    $profile = $tailoring->measurementProfile;
@endphp

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('measurements.custom_tailoring') }}</p>
        <h1>{{ __('measurements.tailoring_request') }}</h1>
        <p>{{ $translation?->name ?? $tailoring->product->sku }}</p>
    </div>
</section>

<section class="section">
    <div class="container account-grid">
        <div class="account-main">
            <article class="account-panel">
                <dl class="account-facts">
                    <div><dt>{{ __('measurements.request_reference') }}</dt><dd>{{ $tailoring->uuid }}</dd></div>
                    <div><dt>{{ __('measurements.status') }}</dt><dd>{{ __('measurements.status_'.$tailoring->status) }}</dd></div>
                    <div><dt>{{ __('measurements.product') }}</dt><dd>{{ $translation?->name ?? $tailoring->product->sku }}</dd></div>
                    @if ($tailoring->variant)
                        <div><dt>{{ __('measurements.variant') }}</dt><dd>{{ $tailoring->variant->sku }}</dd></div>
                    @endif
                    <div><dt>{{ __('measurements.measurement_profile') }}</dt><dd>{{ $orderItem?->measurement_profile_name ?? $profile?->name ?? '—' }}</dd></div>
                    @if ($orderItem?->order)
                        <div><dt>{{ __('measurements.order_number') }}</dt><dd>{{ $orderItem->order->number }}</dd></div>
                    @endif
                    <div><dt>{{ __('measurements.created') }}</dt><dd>{{ $tailoring->created_at?->format('Y-m-d H:i') }}</dd></div>
                </dl>

                @if ($orderItem?->tailoring_notes ?? $tailoring->customer_notes)
                    <h2>{{ __('measurements.notes') }}</h2>
                    <p>{{ $orderItem?->tailoring_notes ?? $tailoring->customer_notes }}</p>
                @endif
            </article>

            <article class="account-panel">
                <h2>{{ $orderItem ? __('measurements.measurements_at_order') : __('measurements.current_measurements') }}</h2>
                <dl class="account-facts">
                    @if ($orderItem)
                        @foreach ($orderItem->measurements as $measurement)
                            <div>
                                <dt>{{ $measurement->definition_name }}</dt>
                                <dd>{{ number_format((float) $measurement->value_cm, 2) }} cm</dd>
                            </div>
                        @endforeach
                    @elseif ($profile)
                        @foreach ($profile->values as $value)
                            <div>
                                <dt>{{ $value->definition->translation()?->name }}</dt>
                                <dd>{{ \App\Support\Measurements\MeasurementConverter::fromCm($value->value_cm, $profile->display_unit) }} {{ $profile->display_unit }}</dd>
                            </div>
                        @endforeach
                    @endif
                </dl>
                @if ($orderItem)
                    <p class="muted">{{ __('measurements.snapshot_notice') }}</p>
                @endif
            </article>

            <a class="button button-secondary" href="{{ route('tailoring.index', ['locale' => app()->getLocale()]) }}">
                {{ __('measurements.back_to_history') }}
            </a>
        </div>
    </div>
</section>
@endsection
