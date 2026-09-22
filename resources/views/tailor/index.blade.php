@extends('layouts.app')

@section('content')
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ __('tailor.workspace') }}</p>
        <h1>{{ __('tailor.dashboard') }}</h1>
        <p>{{ __('tailor.dashboard_intro') }}</p>
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
            <article class="account-panel">
                <div class="panel-heading">
                    <div>
                        <h2>{{ __('tailor.assigned_work') }}</h2>
                        <p>{{ __('tailor.queue_hint') }}</p>
                    </div>
                </div>

                <div class="address-list">
                    <a href="{{ route('tailor.index', ['locale' => app()->getLocale()]) }}">{{ __('tailor.all') }}</a>
                    @foreach ($statuses as $status)
                        <a href="{{ route('tailor.index', ['locale' => app()->getLocale(), 'status' => $status]) }}">
                            {{ __('tailor.status_'.$status) }}
                        </a>
                    @endforeach
                </div>
            </article>

            @forelse ($assignments as $assignment)
                @php($tailoring = $assignment->tailoringRequest)
                <article class="account-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">{{ __('tailor.status_'.$assignment->status) }}</p>
                            <h2>{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h2>
                            <p>
                                {{ __('tailor.order') }}:
                                {{ $tailoring->orderItem?->order?->number ?? '—' }}
                                · {{ __('tailor.customer') }}:
                                {{ $tailoring->user->name }}
                            </p>
                            <p class="muted">{{ $assignment->uuid }}</p>
                        </div>
                        <a class="button button-secondary" href="{{ route('tailor.show', ['locale' => app()->getLocale(), 'assignment' => $assignment]) }}">
                            {{ __('tailor.open') }}
                        </a>
                    </div>
                </article>
            @empty
                <article class="account-panel">
                    <h2>{{ __('tailor.no_assignments') }}</h2>
                    <p>{{ __('tailor.no_assignments_hint') }}</p>
                </article>
            @endforelse

            {{ $assignments->links() }}
        </div>
    </div>
</section>
@endsection
