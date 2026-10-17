@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Product Reviews</h1>
        <p class="mt-1 text-sm text-gray-500">Moderate customer ratings and review content.</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach(['all' => 'All', 'approved' => 'Approved', 'hidden' => 'Hidden'] as $key => $label)
            <a href="{{ route('admin.reviews.index', ['locale' => app()->getLocale(), 'status' => $key]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === $key ? 'bg-[#8B1538] text-white' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}">
                {{ $label }} ({{ $counts[$key] }})
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($reviews as $review)
            @php($translation = $review->product?->translation('en') ?? $review->product?->translation())
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[#D4AF37]">{{ str_repeat('★', $review->rating) }}<span class="text-gray-200">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                            @if($review->is_verified_purchase)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Verified purchase</span>
                            @endif
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $review->is_approved ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $review->is_approved ? 'Approved' : 'Hidden' }}</span>
                        </div>
                        <h2 class="mt-3 font-semibold text-gray-900">{{ $review->title ?: 'Untitled review' }}</h2>
                        @if($review->comment)<p class="mt-2 max-w-3xl whitespace-pre-line text-sm leading-6 text-gray-600">{{ $review->comment }}</p>@endif
                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400">
                            <span>{{ $review->user?->name ?: $review->user?->email }}</span>
                            <span>{{ $translation?->name ?: $review->product?->sku }}</span>
                            <span>{{ $review->created_at?->translatedFormat('M j, Y · H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <form method="POST" action="{{ route('admin.reviews.toggle', ['locale' => app()->getLocale(), 'review' => $review]) }}">
                            @csrf
                            <button type="submit" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ $review->is_approved ? 'Hide' : 'Approve' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', ['locale' => app()->getLocale(), 'review' => $review]) }}" onsubmit="return confirm('Delete this review permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center text-gray-500">No reviews found.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $reviews->links() }}</div>
</div>
@endsection
