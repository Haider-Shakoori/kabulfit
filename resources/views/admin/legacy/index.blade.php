@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Legacy URLs</h1>
        <p class="mt-1 text-sm text-gray-500">SEO migration inventory and redirect mappings.</p>
    </div>

    <div class="space-y-4">
        @foreach($entries as $entry)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate font-mono text-sm font-semibold text-gray-900">{{ $entry->legacy_path }}</h2>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($entry->disposition)->replace('_', ' ') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Verified: {{ $entry->verified_at?->translatedFormat('M j, Y · H:i') ?? 'not verified' }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-500 transition group-open:rotate-180">⌄</span>
                </summary>

                <form method="POST" action="{{ route('admin.legacy.update', ['locale' => app()->getLocale(), 'legacyUrl' => $entry]) }}" class="border-t border-gray-100 p-5">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Disposition</span><select name="disposition" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach(['redirect','manual_product','private','gone'] as $value)<option value="{{ $value }}" @selected($entry->disposition === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Target path</span><input name="target_path" value="{{ $entry->target_path }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3 md:col-span-2"><input name="is_active" type="checkbox" value="1" @checked($entry->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
                        <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Notes</span><textarea name="notes" rows="4" class="rounded-md border border-gray-200 px-3 py-2.5">{{ $entry->notes }}</textarea></label>
                    </div>
                    <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save mapping</button></div>
                </form>
            </details>
        @endforeach
    </div>

    <div class="mt-8">{{ $entries->links() }}</div>
</div>
@endsection
