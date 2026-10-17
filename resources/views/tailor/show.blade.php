@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $tailoring = $assignment->tailoringRequest;
    $orderItem = $tailoring->orderItem;
    $order = $orderItem?->order;
    $statusClasses = [
        'assigned' => 'bg-yellow-100 text-yellow-700',
        'accepted' => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-purple-100 text-purple-700',
        'fitting' => 'bg-cyan-100 text-cyan-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusClass = $statusClasses[$assignment->status] ?? 'bg-gray-100 text-gray-700';
    $labels = [
        'back' => __('tailor.back_to_dashboard'),
        'overview' => $locale === 'ps' ? 'لنډیز' : ($locale === 'fa' ? 'نمای کلی' : 'Overview'),
        'workflow' => __('tailor.workflow'),
        'notes' => __('tailor.internal_notes'),
        'activity' => __('tailor.activity'),
        'measurements' => __('tailor.measurements'),
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]" x-data="{ tab: 'overview' }">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('tailor.index', ['locale' => $locale]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $labels['back'] }}
        </a>

        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        <x-form-errors />

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#2A6867]">{{ __('tailor.workspace') }}</p>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h1>
                <p class="mt-1 text-xs text-gray-400">{{ $assignment->uuid }}</p>
            </div>
            <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">{{ __('tailor.status_'.$assignment->status) }}</span>
        </div>

        <div class="grid gap-6 lg:grid-cols-4">
            <aside class="self-start lg:sticky lg:top-36">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="p-5">
                        <p class="text-xs text-gray-500">{{ __('tailor.customer') }}</p>
                        <strong class="mt-1 block text-gray-900">{{ $tailoring->user->name }}</strong>
                        <div class="my-4 border-t border-gray-100"></div>
                        <p class="text-xs text-gray-500">{{ __('tailor.order') }}</p>
                        <strong class="mt-1 block text-gray-900">{{ $order?->number ?? '—' }}</strong>
                        <div class="my-4 border-t border-gray-100"></div>
                        <p class="text-xs text-gray-500">{{ __('tailor.assigned_at') }}</p>
                        <strong class="mt-1 block text-sm text-gray-900">{{ $assignment->assigned_at?->translatedFormat('F j, Y · H:i') }}</strong>
                    </div>
                    <div class="grid border-t border-gray-100">
                        @foreach([
                            ['overview', $labels['overview']],
                            ['measurements', $labels['measurements']],
                            ['workflow', $labels['workflow']],
                            ['notes', $labels['notes']],
                            ['activity', $labels['activity']],
                        ] as [$tabKey, $tabLabel])
                            <button type="button" @click="tab = '{{ $tabKey }}'" class="border-b border-gray-100 px-5 py-3 text-start text-sm font-medium transition last:border-b-0" :class="tab === '{{ $tabKey }}' ? 'bg-[#881C27]/5 text-[#881C27]' : 'text-gray-600 hover:bg-gray-50'">{{ $tabLabel }}</button>
                        @endforeach
                    </div>
                </div>
            </aside>

            <main class="min-w-0 lg:col-span-3">
                <section x-show="tab === 'overview'" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('tailor.assignment') }}</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach([
                            [__('tailor.request_reference'), $tailoring->uuid],
                            [__('tailor.assignment_reference'), $assignment->uuid],
                            [__('tailor.order'), $order?->number ?? '—'],
                            [__('tailor.customer'), $tailoring->user->name],
                            [__('tailor.product'), $tailoring->product->translation()?->name ?? $tailoring->product->sku],
                            [__('tailor.variant'), $tailoring->variant?->sku ?? '—'],
                        ] as [$label, $value])
                            <div class="rounded-xl bg-gray-50 p-4">
                                <span class="text-xs text-gray-500">{{ $label }}</span>
                                <strong class="mt-1 block break-words text-sm text-gray-900">{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                    @if($orderItem?->tailoring_notes)
                        <div class="mt-5 rounded-xl border border-amber-100 bg-amber-50 p-4">
                            <h3 class="text-sm font-semibold text-amber-800">{{ __('tailor.customer_notes') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-amber-700">{{ $orderItem->tailoring_notes }}</p>
                        </div>
                    @endif
                </section>

                <section x-show="tab === 'measurements'" x-cloak class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('tailor.measurements') }}</h2>
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]"><x-icon name="ruler" class="h-5 w-5" /></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach($orderItem?->measurements ?? [] as $measurement)
                            <div class="rounded-xl bg-[#FDFBF7] p-4">
                                <span class="block text-xs text-gray-500">{{ $measurement->definition_name }}</span>
                                <strong class="mt-1 block text-base text-gray-900">{{ number_format((float) $measurement->value_cm, 2) }} cm</strong>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-700">{{ __('tailor.immutable_snapshot_notice') }}</div>
                </section>

                <section x-show="tab === 'workflow'" x-cloak class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('tailor.workflow') }}</h2>
                    @if($nextStatuses !== [])
                        <form method="POST" action="{{ route('tailor.status', ['locale' => $locale, 'assignment' => $assignment]) }}" class="mt-5 space-y-4">
                            @csrf
                            <label class="grid gap-1.5">
                                <span class="text-sm font-medium text-gray-700">{{ __('tailor.move_to') }}</span>
                                <select name="status" required class="rounded-xl border border-gray-200 bg-white px-3 py-3 text-sm outline-none focus:border-[#881C27]">
                                    @foreach($nextStatuses as $status)
                                        <option value="{{ $status }}">{{ __('tailor.status_'.$status) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="grid gap-1.5">
                                <span class="text-sm font-medium text-gray-700">{{ __('tailor.optional_status_note') }}</span>
                                <textarea name="note" rows="4" maxlength="3000" class="rounded-xl border border-gray-200 px-3 py-3 text-sm outline-none focus:border-[#881C27]"></textarea>
                            </label>
                            <button type="submit" class="inline-flex rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white">{{ __('tailor.update_status') }}</button>
                        </form>
                    @else
                        <div class="mt-5 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">{{ __('tailor.status_'.$assignment->status) }}</div>
                    @endif
                </section>

                <section x-show="tab === 'notes'" x-cloak class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('tailor.internal_notes') }}</h2>
                    <form method="POST" action="{{ route('tailor.notes.store', ['locale' => $locale, 'assignment' => $assignment]) }}" class="mt-5">
                        @csrf
                        <textarea name="body" rows="4" maxlength="3000" placeholder="{{ __('tailor.note_placeholder') }}" required class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm outline-none focus:border-[#881C27]"></textarea>
                        <button type="submit" class="mt-3 rounded-xl bg-[#881C27] px-5 py-2.5 text-sm font-semibold text-white">{{ __('tailor.save_note') }}</button>
                    </form>

                    <div class="mt-6 space-y-3">
                        @forelse($assignment->notes as $note)
                            <div class="rounded-xl bg-gray-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="text-sm text-gray-900">{{ $note->author?->name ?? __('tailor.system') }}</strong>
                                    <span class="text-xs text-gray-400">{{ $note->created_at?->translatedFormat('F j, Y · H:i') }}</span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $note->body }}</p>
                            </div>
                        @empty
                            <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">{{ __('tailor.no_notes') }}</p>
                        @endforelse
                    </div>
                </section>

                <section x-show="tab === 'activity'" x-cloak class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('tailor.activity') }}</h2>
                    <div class="relative mt-6 space-y-5 before:absolute before:bottom-2 before:start-[11px] before:top-2 before:w-px before:bg-gray-200">
                        @foreach($assignment->events as $event)
                            <div class="relative flex gap-4">
                                <span class="relative z-10 mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-r from-[#881C27] to-[#2A6867] text-[10px] text-white">✓</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ __('tailor.event_'.$event->type) }}</p>
                                    @if($event->from_status || $event->to_status)
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $event->from_status ? __('tailor.status_'.$event->from_status) : '—' }}
                                            → {{ $event->to_status ? __('tailor.status_'.$event->to_status) : '—' }}
                                        </p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-400">{{ $event->actor?->name ?? __('tailor.system') }} · {{ $event->created_at?->translatedFormat('F j, Y · H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
@endsection
