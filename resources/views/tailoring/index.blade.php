@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('measurements.custom_tailoring') }}</p>
        <h1>{{ __('measurements.tailoring_history') }}</h1>
        <p>{{ __('measurements.tailoring_history_intro') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="account-main">
            @forelse ($requests as $tailoring)
                @php($translation = $tailoring->product->translation())
                <article class="account-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">{{ __('measurements.status') }}: {{ __('measurements.status_'.$tailoring->status) }}</p>
                            <h2>{{ $translation?->name ?? $tailoring->product->sku }}</h2>
                        </div>
                        <a class="button button-secondary" href="{{ route('tailoring.show', ['locale' => app()->getLocale(), 'tailoring' => $tailoring->uuid]) }}">
                            {{ __('measurements.view_request') }}
                        </a>
                    </div>
                    <p>{{ __('measurements.request_reference') }}: {{ $tailoring->uuid }}</p>
                    @if ($tailoring->orderItem?->order)
                        <p>{{ __('measurements.order_number') }}: {{ $tailoring->orderItem->order->number }}</p>
                    @endif
                    <p>{{ __('measurements.created') }}: {{ $tailoring->created_at?->format('Y-m-d H:i') }}</p>
                </article>
            @empty
                <div class="account-panel">
                    <p>{{ __('measurements.no_tailoring_requests') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
