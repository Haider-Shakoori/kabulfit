@extends('layouts.app')

@section('content')
@php
    $tailoring = $assignment->tailoringRequest;
    $orderItem = $tailoring->orderItem;
    $order = $orderItem?->order;
@endphp

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('tailor.workspace') }}</p>
        <h1>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h1>
        <p>{{ __('tailor.status') }}: {{ __('tailor.status_'.$assignment->status) }}</p>
    </div>
</section>

<section class="section">
    <div class="container account-grid">
        <aside class="account-summary">
            <h2>{{ __('tailor.workspace') }}</h2>
            <nav class="address-list" aria-label="{{ __('tailor.workspace') }}">
                <a href="{{ route('tailor.index', ['locale' => app()->getLocale()]) }}">{{ __('tailor.assigned_work') }}</a>
                <a href="{{ route('account', ['locale' => app()->getLocale()]) }}">{{ __('auth.account') }}</a>
            </nav>
        </aside>

        <div class="account-main">
            <x-form-errors />
            @if (session('status'))
                <div class="account-panel">{{ session('status') }}</div>
            @endif

            <article class="account-panel">
                <h2>{{ __('tailor.assignment') }}</h2>
                <dl class="account-facts">
                    <div><dt>{{ __('tailor.request_reference') }}</dt><dd>{{ $tailoring->uuid }}</dd></div>
                    <div><dt>{{ __('tailor.assignment_reference') }}</dt><dd>{{ $assignment->uuid }}</dd></div>
                    <div><dt>{{ __('tailor.order') }}</dt><dd>{{ $order?->number ?? '—' }}</dd></div>
                    <div><dt>{{ __('tailor.customer') }}</dt><dd>{{ $tailoring->user->name }}</dd></div>
                    <div><dt>{{ __('tailor.product') }}</dt><dd>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</dd></div>
                    @if ($tailoring->variant)
                        <div><dt>{{ __('tailor.variant') }}</dt><dd>{{ $tailoring->variant->sku }}</dd></div>
                    @endif
                    <div><dt>{{ __('tailor.status') }}</dt><dd>{{ __('tailor.status_'.$assignment->status) }}</dd></div>
                    <div><dt>{{ __('tailor.assigned_at') }}</dt><dd>{{ $assignment->assigned_at?->format('Y-m-d H:i') }}</dd></div>
                </dl>

                @if ($orderItem?->tailoring_notes)
                    <h3>{{ __('tailor.customer_notes') }}</h3>
                    <p>{{ $orderItem->tailoring_notes }}</p>
                @endif
            </article>

            <article class="account-panel">
                <h2>{{ __('tailor.measurements') }}</h2>
                <dl class="account-facts">
                    @foreach ($orderItem?->measurements ?? [] as $measurement)
                        <div>
                            <dt>{{ $measurement->definition_name }}</dt>
                            <dd>{{ number_format((float) $measurement->value_cm, 2) }} cm</dd>
                        </div>
                    @endforeach
                </dl>
                <p class="muted">{{ __('tailor.immutable_snapshot_notice') }}</p>
            </article>

            @if ($nextStatuses !== [])
                <article class="account-panel">
                    <h2>{{ __('tailor.workflow') }}</h2>
                    <form method="post" action="{{ route('tailor.status', ['locale' => app()->getLocale(), 'assignment' => $assignment]) }}">
                        @csrf
                        <label>
                            <span>{{ __('tailor.move_to') }}</span>
                            <select name="status" required>
                                @foreach ($nextStatuses as $status)
                                    <option value="{{ $status }}">{{ __('tailor.status_'.$status) }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>{{ __('tailor.optional_status_note') }}</span>
                            <textarea name="note" rows="3" maxlength="3000"></textarea>
                        </label>
                        <button class="button button-primary" type="submit">{{ __('tailor.update_status') }}</button>
                    </form>
                </article>
            @endif

            <article class="account-panel">
                <h2>{{ __('tailor.internal_notes') }}</h2>
                <form method="post" action="{{ route('tailor.notes.store', ['locale' => app()->getLocale(), 'assignment' => $assignment]) }}">
                    @csrf
                    <label>
                        <span>{{ __('tailor.add_note') }}</span>
                        <textarea name="body" rows="4" maxlength="3000" placeholder="{{ __('tailor.note_placeholder') }}" required></textarea>
                    </label>
                    <button class="button button-primary" type="submit">{{ __('tailor.save_note') }}</button>
                </form>

                <div class="address-list">
                    @forelse ($assignment->notes as $note)
                        <div>
                            <strong>{{ $note->author?->name ?? __('tailor.system') }}</strong>
                            <span> · {{ $note->created_at?->format('Y-m-d H:i') }}</span>
                            <p>{{ $note->body }}</p>
                        </div>
                    @empty
                        <p>{{ __('tailor.no_notes') }}</p>
                    @endforelse
                </div>
            </article>

            <article class="account-panel">
                <h2>{{ __('tailor.activity') }}</h2>
                <div class="address-list">
                    @foreach ($assignment->events as $event)
                        <div>
                            <strong>{{ __('tailor.event_'.$event->type) }}</strong>
                            @if ($event->from_status || $event->to_status)
                                <span>
                                    · {{ $event->from_status ? __('tailor.status_'.$event->from_status) : '—' }}
                                    → {{ $event->to_status ? __('tailor.status_'.$event->to_status) : '—' }}
                                </span>
                            @endif
                            <p>{{ $event->actor?->name ?? __('tailor.system') }} · {{ $event->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <a class="button button-secondary" href="{{ route('tailor.index', ['locale' => app()->getLocale()]) }}">
                {{ __('tailor.back_to_dashboard') }}
            </a>
        </div>
    </div>
</section>
@endsection
