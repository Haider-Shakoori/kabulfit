@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $action = $profile
        ? route('measurements.update', ['locale' => $locale, 'profile' => $profile])
        : route('measurements.store', ['locale' => $locale]);
    $guide = $definitions->first()?->translation()?->guide_image_path;
    $title = $profile ? __('measurements.edit_profile') : __('measurements.new_profile');
    $labels = [
        'back' => $locale === 'ps' ? 'بېرته پروفایلونو ته' : ($locale === 'fa' ? 'بازگشت به پروفایل‌ها' : 'Back to Profiles'),
        'garment' => __('measurements.garment_type'),
        'unit' => __('measurements.unit'),
        'profile_details' => $locale === 'ps' ? 'د پروفایل معلومات' : ($locale === 'fa' ? 'اطلاعات پروفایل' : 'Profile Details'),
        'measurements' => $locale === 'ps' ? 'اندازې' : ($locale === 'fa' ? 'اندازه‌ها' : 'Measurements'),
        'range' => $locale === 'ps' ? 'محدوده' : ($locale === 'fa' ? 'محدوده' : 'Range'),
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-8">
        <a href="{{ route('measurements.index', ['locale' => $locale]) }}" class="mb-6 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">
            <span class="rtl:rotate-180">←</span>
            {{ $labels['back'] }}
        </a>

        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#2A6867]">{{ __('measurements.garment_'.$garmentType) }}</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">{{ $title }}</h1>
            <p class="mt-2 max-w-2xl text-gray-600">{{ __('measurements.form_intro') }}</p>
        </div>

        <x-form-errors />

        <div class="grid gap-8 lg:grid-cols-5">
            <aside class="lg:col-span-2">
                <div class="sticky top-36 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="aspect-[4/5] bg-gray-100">
                        @if($guide)
                            <img src="{{ asset($guide) }}" alt="{{ __('measurements.guide_alt', ['garment' => __('measurements.garment_'.$garmentType)]) }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" alt="{{ __('measurements.guide') }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="p-5">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900"><x-icon name="ruler" class="h-5 w-5 text-[#881C27]" />{{ __('measurements.guide') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ __('measurements.guide_text') }}</p>

                        <div class="mt-5 grid grid-cols-2 overflow-hidden rounded-xl border border-gray-200">
                            <a href="{{ request()->fullUrlWithQuery(['unit' => 'cm']) }}" @class([
                                'py-2.5 text-center text-sm font-semibold transition',
                                'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white' => $unit === 'cm',
                                'bg-white text-gray-600 hover:bg-gray-50' => $unit !== 'cm',
                            ])>CM</a>
                            <a href="{{ request()->fullUrlWithQuery(['unit' => 'in']) }}" @class([
                                'py-2.5 text-center text-sm font-semibold transition',
                                'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white' => $unit === 'in',
                                'bg-white text-gray-600 hover:bg-gray-50' => $unit !== 'in',
                            ])>IN</a>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="lg:col-span-3">
                <form method="POST" action="{{ $action }}" class="space-y-6">
                    @csrf
                    @if($profile) @method('PUT') @endif
                    <input type="hidden" name="garment_type" value="{{ $garmentType }}">
                    <input type="hidden" name="display_unit" value="{{ $unit }}">

                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $labels['profile_details'] }}</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <label class="grid gap-1.5">
                                <span class="text-sm font-medium text-gray-700">{{ __('measurements.profile_name') }}</span>
                                <input name="name" required maxlength="100" value="{{ old('name', $profile?->name) }}" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10">
                            </label>
                            <div class="rounded-xl bg-gray-50 p-4 text-sm">
                                <div class="flex justify-between gap-4"><span class="text-gray-500">{{ $labels['garment'] }}</span><strong>{{ __('measurements.garment_'.$garmentType) }}</strong></div>
                                <div class="mt-2 flex justify-between gap-4"><span class="text-gray-500">{{ $labels['unit'] }}</span><strong class="uppercase">{{ $unit }}</strong></div>
                            </div>
                        </div>
                        <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl bg-gray-50 px-4 py-3">
                            <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $profile?->is_default)) class="h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]">
                            <span class="text-sm font-medium text-gray-700">{{ __('measurements.make_default') }}</span>
                        </label>
                    </section>

                    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ $labels['measurements'] }}</h2>
                                <p class="mt-1 text-sm text-gray-500">{{ __('measurements.form_hint') }}</p>
                            </div>
                            <span class="rounded-full bg-[#2A6867]/10 px-3 py-1 text-xs font-semibold text-[#2A6867]">{{ $definitions->count() }}</span>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($definitions as $definition)
                                @php
                                    $name = $definition->translation()?->name ?? $definition->code;
                                    $step = AppSupportMeasurementsMeasurementConverter::fromCm($definition->step_cm, $unit);
                                    $min = AppSupportMeasurementsMeasurementConverter::fromCm($definition->min_cm, $unit);
                                    $max = AppSupportMeasurementsMeasurementConverter::fromCm($definition->max_cm, $unit);
                                @endphp
                                <label class="rounded-xl border border-gray-100 bg-[#FDFBF7] p-4">
                                    <span class="flex items-start justify-between gap-3">
                                        <span>
                                            <strong class="block text-sm text-gray-900">{{ $name }}</strong>
                                            @if($definition->translation()?->instructions)
                                                <small class="mt-1 block leading-5 text-gray-500">{{ $definition->translation()?->instructions }}</small>
                                            @endif
                                        </span>
                                        <span class="rounded-lg bg-white px-2 py-1 text-[10px] font-medium text-gray-500">{{ $unit }}</span>
                                    </span>
                                    <input type="hidden" name="measurements[{{ $loop->index }}][code]" value="{{ $definition->code }}">
                                    <input
                                        name="measurements[{{ $loop->index }}][value]"
                                        type="number"
                                        inputmode="decimal"
                                        step="{{ $step }}"
                                        min="{{ $min }}"
                                        max="{{ $max }}"
                                        value="{{ old('measurements.'.$loop->index.'.value', $values[$definition->code] ?? '') }}"
                                        required
                                        class="mt-3 w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-base font-semibold outline-none transition focus:border-[#881C27] focus:ring-2 focus:ring-[#881C27]/10"
                                    >
                                    <small class="mt-2 block text-xs text-gray-400">{{ $labels['range'] }}: {{ $min }}–{{ $max }} {{ $unit }}</small>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <div class="flex flex-wrap justify-end gap-3">
                        <a href="{{ route('measurements.index', ['locale' => $locale]) }}" class="rounded-xl border-2 border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">{{ $locale === 'ps' ? 'لغوه' : ($locale === 'fa' ? 'لغو' : 'Cancel') }}</a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white shadow-sm">
                            <x-icon name="ruler" class="h-4 w-4" />
                            {{ __('measurements.save_profile') }}
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>
@endsection
