@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $translation = $tailoring->product->translation();
    $orderItem = $tailoring->orderItem;
    $profile = $tailoring->measurementProfile;
    $labels = [
        'back' => $locale === 'ps' ? 'بېرته تاریخ ته' : ($locale === 'fa' ? 'بازگشت به تاریخچه' : 'Back to History'),
        'details' => $locale === 'ps' ? 'د غوښتنې جزیات' : ($locale === 'fa' ? 'جزئیات درخواست' : 'Request Details'),
        'measurements' => $orderItem ? __('measurements.measurements_at_order') : __('measurements.current_measurements'),
        'notes' => __('measurements.notes'),
        'order' => __('measurements.order_number'),
    ];
    $statusClasses = [
        'ready' => 'bg-cyan-100 text-cyan-700',
        'ordered' => 'bg-purple-100 text-purple-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusClass = $statusClasses[$tailoring->status] ?? 'bg-gray-100 text-gray-700';
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('tailoring.index', ['locale' => $locale]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $labels['back'] }}
        </a>

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#2A6867]">{{ __('measurements.custom_tailoring') }}</p>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $translation?->name ?? $tailoring->product->sku }}</h1>
                <p class="mt-1 break-all text-xs text-gray-400">{{ $tailoring->uuid }}</p>
            </div>
            <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">{{ __('measurements.status_'.$tailoring->status) }}</span>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <main class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $labels['details'] }}</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach([
                            [__('measurements.request_reference'), $tailoring->uuid],
                            [__('measurements.status'), __('measurements.status_'.$tailoring->status)],
                            [__('measurements.product'), $translation?->name ?? $tailoring->product->sku],
                            [__('measurements.variant'), $tailoring->variant?->sku ?? '—'],
                            [__('measurements.measurement_profile'), $orderItem?->measurement_profile_name ?? $profile?->name ?? '—'],
                            [$labels['order'], $orderItem?->order?->number ?? '—'],
                        ] as [$label, $value])
                            <div class="rounded-xl bg-gray-50 p-4">
                                <span class="text-xs text-gray-500">{{ $label }}</span>
                                <strong class="mt-1 block break-words text-sm text-gray-900">{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>

                    @if($orderItem?->tailoring_notes ?? $tailoring->customer_notes)
                        <div class="mt-5 rounded-xl border border-amber-100 bg-amber-50 p-4">
                            <h3 class="text-sm font-semibold text-amber-800">{{ $labels['notes'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-amber-700">{{ $orderItem?->tailoring_notes ?? $tailoring->customer_notes }}</p>
                        </div>
                    @endif
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $labels['measurements'] }}</h2>
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-[#881C27]/10 text-[#881C27]"><x-icon name="ruler" class="h-5 w-5" /></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @if($orderItem)
                            @foreach($orderItem->measurements as $measurement)
                                <div class="rounded-xl bg-[#FDFBF7] p-4">
                                    <span class="block text-xs text-gray-500">{{ $measurement->definition_name }}</span>
                                    <strong class="mt-1 block text-base text-gray-900">{{ number_format((float) $measurement->value_cm, 2) }} cm</strong>
                                </div>
                            @endforeach
                        @elseif($profile)
                            @foreach($profile->values as $value)
                                <div class="rounded-xl bg-[#FDFBF7] p-4">
                                    <span class="block text-xs text-gray-500">{{ $value->definition->translation()?->name }}</span>
                                    <strong class="mt-1 block text-base text-gray-900">{{ \\App\\Support\\Measurements\\MeasurementConverter::fromCm($value->value_cm, $profile->display_unit) }} {{ $profile->display_unit }}</strong>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($orderItem)
                        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-700">{{ __('measurements.snapshot_notice') }}</div>
                    @endif
                </section>
            </main>

            <aside>
                <div class="sticky top-36 space-y-6">
                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-full bg-[#2A6867]/10 text-[#2A6867]"><x-icon name="ruler" class="h-6 w-6" /></span>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('measurements.garment_type') }}</p>
                                <strong class="text-gray-900">{{ __('measurements.garment_'.$tailoring->product->measurement_garment_type) }}</strong>
                            </div>
                        </div>
                        <div class="mt-5 border-t border-gray-100 pt-5">
                            <p class="text-xs text-gray-500">{{ __('measurements.created') }}</p>
                            <strong class="mt-1 block text-sm text-gray-900">{{ $tailoring->created_at?->translatedFormat('F j, Y · H:i') }}</strong>
                        </div>
                    </section>

                    <a href="{{ route('measurements.index', ['locale' => $locale]) }}" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                        <x-icon name="ruler" class="h-4 w-4" />
                        {{ __('measurements.manage_profiles') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
