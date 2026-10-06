@extends('layouts.admin')

@section('content')
@php
    $order = $tailoring->orderItem?->order;
@endphp

<div class="p-4 sm:p-6 lg:p-8">
    <a href="{{ route('admin.tailoring.index', ['locale' => app()->getLocale()]) }}" class="mb-6 inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">← Back to Tailoring</a>

    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Tailoring request</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $tailoring->user->email }}</p>
        </div>
        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold capitalize text-purple-700">{{ str($tailoring->status)->replace('_', ' ') }}</span>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <main class="space-y-6 xl:col-span-2">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Request</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach([
                        ['Reference', $tailoring->uuid],
                        ['Profile', $tailoring->orderItem?->measurement_profile_name ?? $tailoring->measurementProfile?->name ?? '—'],
                        ['Order', $order?->number ?? '—'],
                        ['Payment', $order?->payment_status ?? 'not ordered'],
                    ] as [$label, $value])
                        <div class="rounded-lg bg-gray-50 p-4"><span class="text-xs text-gray-500">{{ $label }}</span><strong class="mt-1 block break-words text-sm text-gray-900">{{ $value }}</strong></div>
                    @endforeach
                </div>
                @if($tailoring->orderItem?->tailoring_notes ?? $tailoring->customer_notes)
                    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4"><p class="text-xs font-semibold text-amber-800">Customer notes</p><p class="mt-2 text-sm leading-6 text-amber-700">{{ $tailoring->orderItem?->tailoring_notes ?? $tailoring->customer_notes }}</p></div>
                @endif
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900">Measurements</h2>
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-[#8B1538]/10 text-[#8B1538]"><x-icon name="ruler" class="h-5 w-5" /></span>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @if($tailoring->orderItem)
                        @foreach($tailoring->orderItem->measurements as $measurement)
                            <div class="rounded-lg bg-gray-50 p-4"><span class="block text-xs text-gray-500">{{ $measurement->definition_name }}</span><strong class="mt-1 block text-sm text-gray-900">{{ $measurement->value_cm }} cm</strong></div>
                        @endforeach
                    @elseif($tailoring->measurementProfile)
                        @foreach($tailoring->measurementProfile->values as $value)
                            <div class="rounded-lg bg-gray-50 p-4"><span class="block text-xs text-gray-500">{{ $value->definition->translation()?->name }}</span><strong class="mt-1 block text-sm text-gray-900">{{ $value->value_cm }} cm</strong></div>
                        @endforeach
                    @endif
                </div>
                @if($tailoring->orderItem)
                    <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ __('tailor.immutable_snapshot_notice') }}</div>
                @endif
            </section>

            @if($tailoring->assignment)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('tailor.activity') }}</h2>
                    <div class="relative mt-5 space-y-5 before:absolute before:bottom-2 before:start-[11px] before:top-2 before:w-px before:bg-gray-200">
                        @foreach($tailoring->assignment->events as $event)
                            <div class="relative flex gap-4">
                                <span class="relative z-10 mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-[#8B1538] text-[10px] text-white">✓</span>
                                <div><p class="font-medium text-gray-900">{{ __('tailor.event_'.$event->type) }}</p><p class="mt-1 text-xs text-gray-400">{{ $event->actor?->name ?? __('tailor.system') }} · {{ $event->created_at?->translatedFormat('M j, Y · H:i') }}</p></div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>

        <aside class="space-y-6">
            @if($tailoring->status === 'ordered')
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('tailor.assignment_to') }}</h2>
                    @if($tailoring->assignment)
                        <div class="mt-4 rounded-lg bg-gray-50 p-4"><p class="text-xs text-gray-500">{{ __('tailor.current_tailor') }}</p><strong class="mt-1 block text-gray-900">{{ $tailoring->assignment->tailor->name }}</strong><span class="mt-2 inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ __('tailor.status_'.$tailoring->assignment->status) }}</span></div>
                    @endif
                    @if($tailors->isNotEmpty())
                        <form method="POST" action="{{ route('admin.tailoring.assign', ['locale' => app()->getLocale(), 'tailoring' => $tailoring]) }}" class="mt-5">
                            @csrf
                            <label class="grid gap-1.5"><span class="text-sm font-medium">{{ __('tailor.select_tailor') }}</span><select name="tailor_uuid" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach($tailors as $tailor)<option value="{{ $tailor->uuid }}" @selected($tailoring->assignment?->tailor?->uuid === $tailor->uuid)>{{ $tailor->name }} · {{ $tailor->email }}</option>@endforeach</select></label>
                            <button type="submit" class="mt-4 w-full rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">{{ __('tailor.save_assignment') }}</button>
                        </form>
                    @else
                        <p class="mt-4 text-sm text-gray-500">{{ __('tailor.no_tailors_available') }}</p>
                    @endif
                </section>
            @endif

            @if($order)
                <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="flex w-full items-center justify-center rounded-md border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Open order</a>
            @endif

            @if($tailoring->status === 'ready')
                <form method="POST" action="{{ route('admin.tailoring.cancel', ['locale' => app()->getLocale(), 'tailoring' => $tailoring]) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-50">Cancel un-ordered request</button>
                </form>
            @endif
        </aside>
    </div>
</div>
@endsection
