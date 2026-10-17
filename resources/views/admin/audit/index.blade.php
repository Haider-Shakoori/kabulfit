@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Audit Log</h1>
        <p class="mt-1 text-sm text-gray-500">Immutable administrative activity trail.</p>
    </div>

    <div class="space-y-4">
        @foreach($logs as $log)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <h2 class="truncate font-semibold text-gray-900">{{ str($log->action)->replace('_', ' ')->title() }}</h2>
                        <p class="mt-1 text-xs text-gray-400">{{ $log->created_at?->translatedFormat('M j, Y · H:i') }} · {{ $log->actor?->email ?? 'system' }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-500 transition group-open:rotate-180">⌄</span>
                </summary>
                <div class="border-t border-gray-100 p-5">
                    <p class="break-all font-mono text-xs text-gray-400">{{ $log->uuid }}</p>
                    @if($log->metadata)
                        <pre class="mt-4 max-h-80 overflow-auto rounded-lg bg-gray-950 p-4 text-xs text-gray-200">{{ json_encode($log->metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                    @endif
                </div>
            </details>
        @endforeach
    </div>

    <div class="mt-8">{{ $logs->links() }}</div>
</div>
@endsection
