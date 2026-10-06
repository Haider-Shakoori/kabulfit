@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $labels = [
        'intro' => $locale === 'ps' ? 'خپل اندازه خوندي او بیا یې د راتلونکو فرمایشونو لپاره وکاروئ.' : ($locale === 'fa' ? 'اندازه‌های خود را ذخیره کنید و در سفارش‌های بعدی دوباره استفاده کنید.' : 'Save your measurements once and reuse them for future custom orders.'),
        'empty' => $locale === 'ps' ? 'تر اوسه مو د اندازه پروفایل نه دی جوړ کړی.' : ($locale === 'fa' ? 'هنوز پروفایل اندازه‌گیری ایجاد نکرده‌اید.' : 'You have not created a measurement profile yet.'),
        'start' => $locale === 'ps' ? 'لومړی پروفایل جوړ کړئ' : ($locale === 'fa' ? 'اولین پروفایل را بسازید' : 'Create Your First Profile'),
        'measurements' => $locale === 'ps' ? 'اندازې' : ($locale === 'fa' ? 'اندازه‌ها' : 'Measurements'),
        'unit' => __('measurements.unit'),
        'default' => __('measurements.default_profile'),
        'guide' => __('measurements.guide'),
    ];
@endphp

<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm backdrop-blur">
                <x-icon name="ruler" class="h-4 w-4" />
                {{ __('measurements.custom_tailoring') }}
            </span>
            <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ __('measurements.profiles') }}</h1>
            <p class="mx-auto mt-3 max-w-2xl text-white/80">{{ $labels['intro'] }}</p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8">
        <x-form-errors />
        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('measurements.profiles') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('measurements.profiles_intro') }}</p>
            </div>
            <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                <x-icon name="plus" class="h-4 w-4" />
                {{ __('measurements.new_profile') }}
            </a>
        </div>

        @if($profiles->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
                <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-[#881C27]/10 to-[#2A6867]/10">
                    <x-icon name="ruler" class="h-9 w-9 text-[#881C27]" />
                </span>
                <h3 class="mt-6 text-2xl font-semibold text-gray-900">{{ __('measurements.no_profiles') }}</h3>
                <p class="mx-auto mt-2 max-w-lg text-gray-500">{{ $labels['empty'] }}</p>
                <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 font-semibold text-white">
                    <x-icon name="plus" class="h-4 w-4" />
                    {{ $labels['start'] }}
                </a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($profiles as $profile)
                    <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                        <div class="border-b border-gray-100 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $profile->name }}</h3>
                                        @if($profile->is_default)
                                            <span class="rounded-full bg-[#881C27] px-2.5 py-1 text-[10px] font-semibold text-white">{{ $labels['default'] }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">{{ __('measurements.garment_'.$profile->garment_type) }}</p>
                                </div>
                                <span class="grid h-11 w-11 place-items-center rounded-full bg-[#2A6867]/10 text-[#2A6867]">
                                    <x-icon name="ruler" class="h-5 w-5" />
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="mb-4 flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $labels['measurements'] }}</span>
                                <strong class="text-gray-900">{{ $profile->values->count() }}</strong>
                            </div>
                            <div class="mb-5 flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $labels['unit'] }}</span>
                                <strong class="uppercase text-gray-900">{{ $profile->display_unit }}</strong>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach($profile->values->take(6) as $value)
                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <span class="block truncate text-[11px] text-gray-500">{{ $value->definition->translation()?->name }}</span>
                                        <strong class="mt-1 block text-sm text-gray-900">{{ \App\Support\Measurements\MeasurementConverter::fromCm($value->value_cm, $profile->display_unit) }} {{ $profile->display_unit }}</strong>
                                    </div>
                                @endforeach
                            </div>

                            @if($profile->values->count() > 6)
                                <p class="mt-3 text-center text-xs text-gray-400">+{{ $profile->values->count() - 6 }} {{ $labels['measurements'] }}</p>
                            @endif

                            <div class="mt-5 flex gap-2 border-t border-gray-100 pt-5">
                                <a href="{{ route('measurements.edit', ['locale' => $locale, 'profile' => $profile]) }}" class="flex flex-1 items-center justify-center rounded-xl border-2 border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">{{ __('measurements.edit_profile') }}</a>
                                <form method="POST" action="{{ route('measurements.destroy', ['locale' => $locale, 'profile' => $profile]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="grid h-11 w-11 place-items-center rounded-xl border border-red-200 text-red-500 transition hover:bg-red-50 hover:text-red-700" aria-label="{{ __('measurements.delete_profile') }}">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <section class="mt-10 overflow-hidden rounded-3xl bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white">
            <div class="grid items-center gap-0 md:grid-cols-2">
                <div class="p-8 md:p-10">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold"><x-icon name="ruler" class="h-4 w-4" />{{ $labels['guide'] }}</span>
                    <h2 class="mt-4 text-3xl font-bold">{{ __('site.measurements_title') }}</h2>
                    <p class="mt-3 leading-7 text-white/80">{{ __('measurements.guide_text') }}</p>
                    <a href="{{ route('measurements.create', ['locale' => $locale]) }}" class="mt-6 inline-flex rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#881C27]">{{ __('measurements.new_profile') }}</a>
                </div>
                <div class="min-h-64">
                    <img src="{{ asset('images/kabulfit-live/measurement-guide.png') }}" alt="{{ __('measurements.guide') }}" class="h-full w-full object-cover">
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
