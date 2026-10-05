@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tailoring</h1>
        <p class="mt-1 text-sm text-gray-500">Review custom tailoring requests and current assignments.</p>
    </div>

    <div class="space-y-4">
        @forelse($requests as $tailoring)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">{{ str($tailoring->status)->replace('_', ' ')->title() }}</span>
                            @if($tailoring->assignment)
                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ __('tailor.status_'.$tailoring->assignment->status) }}</span>
                            @endif
                        </div>
                        <h2 class="mt-3 text-lg font-semibold text-gray-900">{{ $tailoring->product->translation()?->name ?? $tailoring->product->sku }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $tailoring->user->email }}</p>
                        <p class="mt-1 break-all text-xs text-gray-400">{{ $tailoring->uuid }}</p>
                    </div>
                    <div class="text-end">
                        <p class="text-xs text-gray-500">Current tailor</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $tailoring->assignment?->tailor?->name ?? __('tailor.unassigned') }}</p>
                        <a href="{{ route('admin.tailoring.show', ['locale' => app()->getLocale(), 'tailoring' => $tailoring]) }}" class="mt-4 inline-flex rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Open</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white py-14 text-center text-sm text-gray-500 shadow-sm">No tailoring requests.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $requests->links() }}</div>
</div>
@endsection
