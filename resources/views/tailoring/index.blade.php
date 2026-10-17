@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $labels = [
        'intro' => $locale === 'ps' ? 'خپل شخصي ګنډل شوي فرمایشونه، حالت او تاریخ وګورئ.' : ($locale === 'fa' ? 'سفارش‌های خیاطی سفارشی، وضعیت و تاریخچه را مشاهده کنید.' : 'Review your custom-tailoring requests, statuses, and history.'),
        'empty' => $locale === 'ps' ? 'تر اوسه شخصي ګنډل شوی فرمایش نه لرئ.' : ($locale === 'fa' ? 'هنوز سفارش خیاطی سفارشی ندارید.' : 'You do not have any custom-tailoring requests yet.'),
        'shop' => $locale === 'ps' ? 'د شخصي ګنډلو محصولات وګورئ' : ($locale === 'fa' ? 'محصولات قابل خیاطی را ببینید' : 'Browse Tailorable Products'),
        'created' => __('measurements.created'),
        'order' => __('measurements.order_number'),
        'profile' => __('measurements.measurement_profile'),
    ];
    $statusClasses = [
        'ready' => 'bg-cyan-100 text-cyan-700',
        'ordered' => 'bg-purple-100 text-purple-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm backdrop-blur">
                <x-icon name="ruler" class="h-4 w-4" />
                {{ __('measurements.custom_tailoring') }}
            </span>
            <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ __('measurements.tailoring_history') }}</h1>
            <p class="mx-auto mt-3 max-w-2xl text-white/80">{{ $labels['intro'] }}</p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('measurements.tailoring_history') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('measurements.tailoring_history_intro') }}</p>
            </div>
            <a href="{{ route('measurements.index', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
                <x-icon name="ruler" class="h-4 w-4" />
                {{ __('measurements.manage_profiles') }}
            </a>
        </div>

        @forelse($requests as $tailoring)
            @php
                $translation = $tailoring->product->translation();
                $statusClass = $statusClasses[$tailoring->status] ?? 'bg-gray-100 text-gray-700';
            @endphp
            <article class="mb-4 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-lg">
                <div class="grid gap-0 md:grid-cols-[160px_1fr]">
                    <div class="min-h-40 bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10">
                        <div class="grid h-full min-h-40 place-items-center">
                            <span class="grid h-20 w-20 place-items-center rounded-full bg-white shadow-sm">
                                <x-icon name="ruler" class="h-9 w-9 text-[#881C27]" />
                            </span>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ __('measurements.status_'.$tailoring->status) }}</span>
                                    @if($tailoring->orderItem?->order)
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">{{ $labels['order'] }}: {{ $tailoring->orderItem->order->number }}</span>
                                    @endif
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $translation?->name ?? $tailoring->product->sku }}</h2>
                                <p class="mt-1 break-all text-xs text-gray-400">{{ $tailoring->uuid }}</p>
                            </div>
                            <a href="{{ route('tailoring.show', ['locale' => $locale, 'tailoring' => $tailoring->uuid]) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-4 py-2.5 text-sm font-semibold text-white">
                                {{ __('measurements.view_request') }}
                                <span class="rtl:rotate-180">›</span>
                            </a>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-gray-50 p-3">
                                <span class="text-xs text-gray-500">{{ $labels['created'] }}</span>
                                <strong class="mt-1 block text-sm text-gray-900">{{ $tailoring->created_at?->translatedFormat('F j, Y') }}</strong>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <span class="text-xs text-gray-500">{{ $labels['profile'] }}</span>
                                <strong class="mt-1 block text-sm text-gray-900">{{ $tailoring->measurementProfile?->name ?? '—' }}</strong>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <span class="text-xs text-gray-500">{{ __('measurements.variant') }}</span>
                                <strong class="mt-1 block text-sm text-gray-900">{{ $tailoring->variant?->sku ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
                <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10">
                    <x-icon name="ruler" class="h-9 w-9 text-[#881C27]" />
                </span>
                <h2 class="mt-6 text-2xl font-semibold text-gray-900">{{ __('measurements.no_tailoring_requests') }}</h2>
                <p class="mx-auto mt-2 max-w-lg text-gray-500">{{ $labels['empty'] }}</p>
                <a href="{{ route('shop', ['locale' => $locale]) }}" class="mt-7 inline-flex rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white">{{ $labels['shop'] }}</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
