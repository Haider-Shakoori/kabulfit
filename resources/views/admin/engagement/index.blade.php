@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8" x-data="{ tab: 'messages' }">
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Engagement</h1>
        <p class="mt-1 text-sm text-gray-500">Contact messages and newsletter subscribers from the storefront.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <button type="button" @click="tab = 'messages'" class="rounded-xl border border-gray-200 bg-white p-5 text-start shadow-sm transition" :class="tab === 'messages' ? 'ring-2 ring-[#8B1538]/20 border-[#8B1538]' : ''">
            <span class="text-sm text-gray-500">New messages</span>
            <strong class="mt-2 block text-3xl text-gray-900">{{ number_format($counts['new_messages']) }}</strong>
        </button>
        <button type="button" @click="tab = 'subscribers'" class="rounded-xl border border-gray-200 bg-white p-5 text-start shadow-sm transition" :class="tab === 'subscribers' ? 'ring-2 ring-[#8B1538]/20 border-[#8B1538]' : ''">
            <span class="text-sm text-gray-500">Active newsletter subscribers</span>
            <strong class="mt-2 block text-3xl text-gray-900">{{ number_format($counts['active_subscribers']) }}</strong>
        </button>
    </div>

    <section x-show="tab === 'messages'" class="space-y-4">
        @forelse($messages as $message)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-start justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-semibold text-gray-900">{{ $message->subject ?: 'General Inquiry' }}</h2>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-amber-100 text-amber-700' => $message->status === 'new',
                                'bg-blue-100 text-blue-700' => $message->status === 'read',
                                'bg-gray-100 text-gray-600' => $message->status === 'closed',
                            ])>{{ str($message->status)->title() }}</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-gray-500">{{ $message->name }} · {{ $message->email }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $message->created_at?->translatedFormat('M j, Y · H:i') }}</p>
                    </div>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-gray-100 transition group-open:rotate-180">⌄</span>
                </summary>
                <div class="border-t border-gray-100 p-5">
                    <p class="whitespace-pre-line text-sm leading-7 text-gray-700">{{ $message->message }}</p>
                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                        <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: '.($message->subject ?: 'KabulFit inquiry')) }}" class="rounded-md bg-[#8B1538] px-4 py-2 text-sm font-semibold text-white">Reply by email</a>
                        <form method="POST" action="{{ route('admin.engagement.messages.status', ['locale' => app()->getLocale(), 'contactMessage' => $message]) }}" class="flex gap-2">
                            @csrf
                            <select name="status" class="rounded-md border border-gray-200 bg-white px-3 py-2 text-sm">
                                @foreach(['new','read','closed'] as $value)<option value="{{ $value }}" @selected($message->status === $value)>{{ str($value)->title() }}</option>@endforeach
                            </select>
                            <button type="submit" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Update</button>
                        </form>
                    </div>
                </div>
            </details>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center text-gray-500">No contact messages yet.</div>
        @endforelse

        @if($messages->hasPages())<div class="mt-6">{{ $messages->links() }}</div>@endif
    </section>

    <section x-show="tab === 'subscribers'" x-cloak>
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr><th class="px-5 py-3 text-start">Email</th><th class="px-5 py-3 text-start">Locale</th><th class="px-5 py-3 text-start">Subscribed</th><th class="px-5 py-3 text-start">Status</th><th class="px-5 py-3 text-end">Action</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $subscriber->email }}</td>
                                <td class="px-5 py-4 uppercase text-gray-500">{{ $subscriber->locale }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-gray-500">{{ $subscriber->subscribed_at?->translatedFormat('M j, Y') ?? '—' }}</td>
                                <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $subscriber->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $subscriber->is_active ? 'Active' : 'Unsubscribed' }}</span></td>
                                <td class="px-5 py-4 text-end">
                                    <form method="POST" action="{{ route('admin.engagement.subscribers.toggle', ['locale' => app()->getLocale(), 'subscriber' => $subscriber]) }}">
                                        @csrf
                                        <button type="submit" class="rounded-md border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ $subscriber->is_active ? 'Deactivate' : 'Reactivate' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No newsletter subscribers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($subscribers->hasPages())<div class="mt-6">{{ $subscribers->links() }}</div>@endif
    </section>
</div>
@endsection
