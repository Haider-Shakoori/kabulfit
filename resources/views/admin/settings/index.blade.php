@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage storefront contact details, SEO, and localized metadata.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update', ['locale' => app()->getLocale()]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-600"><x-icon name="settings" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">General</h2><p class="text-sm text-gray-500">Primary contact configuration</p></div>
            </div>
            <label class="grid gap-1.5 max-w-xl"><span class="text-sm font-medium">Contact email</span><input type="email" name="contact_email" value="{{ $values['contact_email'] }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
        </section>

        @foreach(config('kabulfit.supported_locales') as $loc)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center justify-between">
                    <div><h2 class="font-semibold text-gray-900">Homepage SEO</h2><p class="text-sm text-gray-500">{{ strtoupper($loc) }}</p></div>
                    <span class="rounded-full bg-[#8B1538]/10 px-3 py-1 text-xs font-semibold text-[#8B1538]">{{ strtoupper($loc) }}</span>
                </div>
                <div class="grid gap-4">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Title</span><input name="titles[{{ $loc }}]" value="{{ $values['titles'][$loc] }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description</span><textarea name="descriptions[{{ $loc }}]" rows="4" maxlength="500" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $values['descriptions'][$loc] }}</textarea></label>
                </div>
            </section>
        @endforeach

        <div class="flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save settings</button></div>
    </form>
</div>
@endsection
