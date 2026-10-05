@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Measurements</h1>
        <p class="mt-1 text-sm text-gray-500">Configure measurement definitions used by custom tailoring.</p>
    </div>

    <div class="space-y-4">
        @foreach($definitions as $definition)
            @php($translations=$definition->translations->keyBy('locale'))
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-[#8B1538]/10 px-2.5 py-1 text-xs font-semibold text-[#8B1538]">{{ str($definition->garment_type)->replace('_', ' ')->title() }}</span>
                            @if($definition->is_required)<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Required</span>@endif
                            @if(!$definition->is_active)<span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Inactive</span>@endif
                        </div>
                        <h2 class="mt-2 font-semibold text-gray-900">{{ $translations->get(app()->getLocale())?->name ?? $translations->get('en')?->name ?? $definition->code }}</h2>
                        <p class="mt-1 font-mono text-xs text-gray-400">{{ $definition->code }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-500 transition group-open:rotate-180">⌄</span>
                </summary>

                <form method="POST" action="{{ route('admin.measurements.update', ['locale' => app()->getLocale(), 'definition' => $definition]) }}" class="border-t border-gray-100 p-5 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Minimum cm</span><input type="number" step="0.01" name="min_cm" value="{{ $definition->min_cm }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Maximum cm</span><input type="number" step="0.01" name="max_cm" value="{{ $definition->max_cm }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Step cm</span><input type="number" step="0.01" name="step_cm" value="{{ $definition->step_cm }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_required" value="1" @checked($definition->is_required) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Required</span></label>
                        <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" @checked($definition->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @foreach(config('kabulfit.supported_locales') as $loc)
                            @php($t=$translations->get($loc))
                            <fieldset class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                                <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#8B1538]">{{ strtoupper($loc) }}</legend>
                                <div class="grid gap-4">
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">Name</span><input name="translations[{{ $loc }}][name]" value="{{ $t?->name }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">Instructions</span><textarea name="translations[{{ $loc }}][instructions]" rows="3" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $t?->instructions }}</textarea></label>
                                </div>
                            </fieldset>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save definition</button></div>
                </form>
            </details>
        @endforeach
    </div>
</div>
@endsection
