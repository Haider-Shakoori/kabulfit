@extends('layouts.admin')

@section('content')
@php
    $measurementFields = [
        'neck' => 'Neck',
        'chest' => 'Chest',
        'shoulder' => 'Shoulder',
        'arm_length' => 'Arm Length',
        'bicep' => 'Bicep',
        'sleeve_opening' => 'Wrist/Sleeve Opening',
        'wrist' => 'Back Neck Drop',
        'waist' => 'Waist',
        'hip' => 'Hip',
        'kameez_length' => 'Dress Top Length',
        'dress_hem_width' => 'Dress Hem Width',
        'tunban_length' => 'Tunban Length',
        'trouser_leg_opening' => 'Trouser Leg Opening',
        'inseam' => 'Inseam',
        'thigh' => 'Thigh',
        'height' => 'Height',
        'underarm' => 'Underarm',
        'armhole' => 'Armhole',
        'full_dress_length' => 'Full Dress Length',
        'skirt_length' => 'Skirt Length',
        'bust_point_to_bust_point' => 'Bust Point to Bust Point',
        'front_neck_drop' => 'Front Neck Drop',
    ];
@endphp

<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Measurement Guides</h1>
        <p class="mt-1 text-sm text-gray-500">Manage English, Dari, and Pashto guide videos for each measurement field.</p>
    </div>

    <details class="group mb-6 overflow-hidden rounded-xl border border-dashed border-gray-300 bg-white shadow-sm">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-[#8B1538] text-white"><x-icon name="plus" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Add Measurement Guide</h2><p class="text-sm text-gray-500">Use hosted or YouTube-compatible video URLs.</p></div>
            </div>
            <span class="transition group-open:rotate-180">⌄</span>
        </summary>

        <form method="POST" action="{{ route('admin.measurement-guides.store', ['locale' => app()->getLocale()]) }}" class="border-t border-gray-100 p-5 sm:p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium">Category *</span>
                    <select name="category" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5">
                        <option value="male">Men</option>
                        <option value="female">Women</option>
                        <option value="child">Kids</option>
                    </select>
                </label>
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium">Measurement field *</span>
                    <select name="measurement_field" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5">
                        @foreach($measurementFields as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </label>
                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Title</span><input name="title" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">English Video URL *</span><input type="url" name="video_url" required class="rounded-md border border-gray-200 px-3 py-2.5" placeholder="https://..."></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Dari Video URL</span><input type="url" name="video_url_dari" class="rounded-md border border-gray-200 px-3 py-2.5" placeholder="https://..."></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Pashto Video URL</span><input type="url" name="video_url_pashto" class="rounded-md border border-gray-200 px-3 py-2.5" placeholder="https://..."></label>
                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Description</span><textarea name="description" rows="4" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Display order</span><input type="number" name="display_order" value="0" min="0" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
            </div>
            <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Create guide</button></div>
        </form>
    </details>

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse($guides as $guide)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-start justify-between gap-4 p-5">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-[#8B1538]/10 px-2.5 py-1 text-xs font-semibold text-[#8B1538]">{{ $measurementFields[$guide->measurement_field] ?? str($guide->measurement_field)->replace('_', ' ')->title() }}</span>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">{{ str($guide->category)->title() }}</span>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $guide->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $guide->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <h2 class="mt-3 font-semibold text-gray-900">{{ $guide->title ?: ($measurementFields[$guide->measurement_field] ?? $guide->measurement_field) }}</h2>
                        <p class="mt-1 text-xs text-gray-400">Order {{ $guide->display_order }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 transition group-open:rotate-180">⌄</span>
                </summary>

                <div class="border-t border-gray-100 p-5">
                    <div class="mb-5 grid gap-3 sm:grid-cols-3">
                        @foreach([
                            ['English', $guide->video_url],
                            ['Dari', $guide->video_url_dari],
                            ['Pashto', $guide->video_url_pashto],
                        ] as [$language, $url])
                            <a href="{{ $url ?: '#' }}" target="_blank" rel="noopener noreferrer" @class([
                                'rounded-lg border p-3 text-center text-sm font-medium transition',
                                'border-gray-200 text-gray-700 hover:bg-gray-50' => $url,
                                'pointer-events-none border-dashed border-gray-200 text-gray-300' => ! $url,
                            ])>{{ $language }} video</a>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('admin.measurement-guides.update', ['locale' => app()->getLocale(), 'measurementGuide' => $guide]) }}" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Category</span><select name="category" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach(['male'=>'Men','female'=>'Women','child'=>'Kids'] as $value=>$label)<option value="{{ $value }}" @selected($guide->category === $value)>{{ $label }}</option>@endforeach</select></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Measurement field</span><select name="measurement_field" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach($measurementFields as $value=>$label)<option value="{{ $value }}" @selected($guide->measurement_field === $value)>{{ $label }}</option>@endforeach</select></label>
                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Title</span><input name="title" value="{{ $guide->title }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">English Video URL</span><input type="url" name="video_url" value="{{ $guide->video_url }}" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Dari Video URL</span><input type="url" name="video_url_dari" value="{{ $guide->video_url_dari }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Pashto Video URL</span><input type="url" name="video_url_pashto" value="{{ $guide->video_url_pashto }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Description</span><textarea name="description" rows="3" class="rounded-md border border-gray-200 px-3 py-2.5">{{ $guide->description }}</textarea></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Display order</span><input type="number" name="display_order" min="0" value="{{ $guide->display_order }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" @checked($guide->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
                        <div class="flex flex-wrap justify-between gap-3 md:col-span-2">
                            <button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save guide</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.measurement-guides.destroy', ['locale' => app()->getLocale(), 'measurementGuide' => $guide]) }}" class="mt-4 border-t border-gray-100 pt-4" onsubmit="return confirm('Delete this measurement guide?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4" />Delete</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="lg:col-span-2 rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center text-gray-500">No measurement guide videos yet.</div>
        @endforelse
    </div>
</div>
@endsection
