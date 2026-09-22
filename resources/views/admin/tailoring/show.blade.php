@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Tailoring request</p>
        <h1>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h1>
        <p>{{ $tailoring->user->email }} · {{ $tailoring->status }}</p>
    </div>
</section>

<section class="section">
    <div class="container account-grid">
        @include('admin._nav')

        <div class="account-main">
            <x-form-errors />
            @if (session('status'))
                <div class="account-panel">{{ session('status') }}</div>
            @endif

            <section class="account-panel">
                <h2>Request</h2>
                <p>UUID: {{ $tailoring->uuid }}</p>
                <p>Profile: {{ $tailoring->orderItem?->measurement_profile_name ?? $tailoring->measurementProfile?->name ?? '—' }}</p>
                <p>Notes: {{ $tailoring->orderItem?->tailoring_notes ?? $tailoring->customer_notes ?: '—' }}</p>
                @if ($tailoring->orderItem?->order)
                    <p>
                        Order:
                        <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $tailoring->orderItem->order]) }}">
                            {{ $tailoring->orderItem->order->number }}
                        </a>
                        · {{ __('tailor.payment_status') }}: {{ $tailoring->orderItem->order->payment_status }}
                    </p>
                @endif
            </section>

            <section class="account-panel">
                <h2>Measurements</h2>
                @if ($tailoring->orderItem)
                    @foreach ($tailoring->orderItem->measurements as $measurement)
                        <p>{{ $measurement->definition_name }}: {{ $measurement->value_cm }} cm</p>
                    @endforeach
                    <p class="muted">{{ __('tailor.immutable_snapshot_notice') }}</p>
                @elseif ($tailoring->measurementProfile)
                    @foreach ($tailoring->measurementProfile->values as $value)
                        <p>{{ $value->definition->translation()?->name }}: {{ $value->value_cm }} cm</p>
                    @endforeach
                @endif
            </section>

            @if ($tailoring->status === 'ordered')
                <section class="account-panel">
                    <h2>{{ __('tailor.assignment_to') }}</h2>
                    @if ($tailoring->assignment)
                        <p>
                            {{ __('tailor.current_tailor') }}:
                            <strong>{{ $tailoring->assignment->tailor->name }}</strong>
                            · {{ __('tailor.status_'.$tailoring->assignment->status) }}
                        </p>
                        <p class="muted">{{ __('tailor.reassignment_hint') }}</p>
                    @endif

                    @if ($tailors->isNotEmpty())
                        <form method="post" action="{{ route('admin.tailoring.assign', ['locale' => app()->getLocale(), 'tailoring' => $tailoring]) }}">
                            @csrf
                            <label>
                                <span>{{ __('tailor.select_tailor') }}</span>
                                <select name="tailor_uuid" required>
                                    @foreach ($tailors as $tailor)
                                        <option value="{{ $tailor->uuid }}" @selected($tailoring->assignment?->tailor?->uuid === $tailor->uuid)>
                                            {{ $tailor->name }} · {{ $tailor->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <button class="button button-primary" type="submit">{{ __('tailor.save_assignment') }}</button>
                        </form>
                    @else
                        <p>{{ __('tailor.no_tailors_available') }}</p>
                    @endif
                </section>
            @endif

            @if ($tailoring->assignment)
                <section class="account-panel">
                    <h2>{{ __('tailor.activity') }}</h2>
                    @foreach ($tailoring->assignment->events as $event)
                        <p>
                            {{ __('tailor.event_'.$event->type) }}
                            · {{ $event->actor?->name ?? __('tailor.system') }}
                            · {{ $event->created_at?->format('Y-m-d H:i') }}
                        </p>
                    @endforeach
                </section>
            @endif

            @if ($tailoring->status === 'ready')
                <form method="post" action="{{ route('admin.tailoring.cancel', ['locale' => app()->getLocale(), 'tailoring' => $tailoring]) }}">
                    @csrf
                    <button class="button button-secondary" type="submit">Cancel un-ordered request</button>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection
