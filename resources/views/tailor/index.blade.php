@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $labels = [
        'all' => __('tailor.all'),
        'queue' => $locale === 'ps' ? 'د کار قطار' : ($locale === 'fa' ? 'صف کار' : 'Work Queue'),
        'open' => __('tailor.open'),
        'customer' => __('tailor.customer'),
        'order' => __('tailor.order'),
        'assigned' => __('tailor.assigned_at'),
    ];
    $statusClasses = [
        'assigned' => 'bg-yellow-100 text-yellow-700',
        'accepted' => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-purple-100 text-purple-700',
        'fitting' => 'bg-cyan-100 text-cyan-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/75">{{ __('tailor.workspace') }}</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ __('tailor.dashboard') }}</h1>
                    <p class="mt-2 max-w-2xl text-white/80">{{ __('tailor.dashboard_intro') }}</p>
                </div>
                <a href="{{ route('account', ['locale' => $locale]) }}" class="rounded-xl border border-white/40 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white hover:text-gray-900">{{ __('auth.account') }}</a>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="mb-6 flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('tailor.index', ['locale' => $locale]) }}" @class([
                'shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition',
                'bg-[#881C27] text-white' => empty($selectedStatus),
                'bg-white text-gray-600 shadow-sm hover:text-[#881C27]' => ! empty($selectedStatus),
            ])>{{ $labels['all'] }}</a>
            @foreach($statuses as $status)
                <a href="{{ route('tailor.index', ['locale' => $locale, 'status' => $status]) }}" @class([
                    'shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition',
                    'bg-[#881C27] text-white' => $selectedStatus === $status,
                    'bg-white text-gray-600 shadow-sm hover:text-[#881C27]' => $selectedStatus !== $status,
                ])>{{ __('tailor.status_'.$status) }}</a>
            @endforeach
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($assignments as $assignment)
                @php
                    $tailoring = $assignment->tailoringRequest;
                    $statusClass = $statusClasses[$assignment->status] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="border-b border-gray-100 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ __('tailor.status_'.$assignment->status) }}</span>
                            <span class="text-xs text-gray-400">{{ $assignment->assigned_at?->translatedFormat('M j') }}</span>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold text-gray-900">{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h2>
                        <p class="mt-1 text-xs text-gray-400">{{ $assignment->uuid }}</p>
                    </div>

                    <div class="space-y-3 p-5 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-gray-500">{{ $labels['customer'] }}</span>
                            <strong class="text-gray-900">{{ $tailoring->user->name }}</strong>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-gray-500">{{ $labels['order'] }}</span>
                            <strong class="text-gray-900">{{ $tailoring->orderItem?->order?->number ?? '—' }}</strong>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-gray-500">{{ __('tailor.variant') }}</span>
                            <strong class="text-gray-900">{{ $tailoring->variant?->sku ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 p-5">
                        <a href="{{ route('tailor.show', ['locale' => $locale, 'assignment' => $assignment]) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-3 text-sm font-semibold text-white">
                            {{ $labels['open'] }}
                            <span class="rtl:rotate-180">›</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-gray-300 bg-white py-16 text-center shadow-sm">
                    <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-[#881C27]/10"><x-icon name="ruler" class="h-9 w-9 text-[#881C27]" /></span>
                    <h2 class="mt-6 text-2xl font-semibold text-gray-900">{{ __('tailor.no_assignments') }}</h2>
                    <p class="mt-2 text-gray-500">{{ __('tailor.no_assignments_hint') }}</p>
                </div>
            @endforelse
        </div>

        @if($assignments->hasPages())
            <div class="mt-8">{{ $assignments->links() }}</div>
        @endif
    </div>
</div>
@endsection
