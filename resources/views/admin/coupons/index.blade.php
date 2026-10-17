@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Discount Codes</h1>
        <p class="mt-1 text-sm text-gray-500">Manage percentage and fixed-value promotions used at checkout.</p>
    </div>

    <details class="group mb-6 overflow-hidden rounded-xl border border-dashed border-gray-300 bg-white shadow-sm">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-[#8B1538] text-white"><x-icon name="plus" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Add Discount Code</h2><p class="text-sm text-gray-500">Matches the Base44 DiscountCode capability.</p></div>
            </div>
            <span class="transition group-open:rotate-180">⌄</span>
        </summary>

        <form method="POST" action="{{ route('admin.coupons.store', ['locale' => app()->getLocale()]) }}" class="border-t border-gray-100 p-5 sm:p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <label class="grid gap-1.5"><span class="text-sm font-medium">Code *</span><input name="code" value="{{ old('code') }}" required placeholder="WELCOME10" class="rounded-md border border-gray-200 px-3 py-2.5 uppercase"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Type *</span><select name="type" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5"><option value="percent">Percentage</option><option value="fixed">Fixed amount</option></select></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Value *</span><input type="number" step="0.01" min="0.01" name="value" value="{{ old('value') }}" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Minimum order</span><input type="number" step="0.01" min="0" name="minimum_order" value="{{ old('minimum_order', 0) }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Maximum discount</span><input type="number" step="0.01" min="0" name="maximum_discount" value="{{ old('maximum_discount') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Usage limit</span><input type="number" min="1" name="usage_limit" value="{{ old('usage_limit') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Valid from</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Valid until</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
            </div>
            <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Create discount code</button></div>
        </form>
    </details>

    <div class="space-y-4">
        @forelse($coupons as $coupon)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-mono text-lg font-bold text-gray-900">{{ $coupon->code }}</h2>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                            <span class="rounded-full bg-[#8B1538]/10 px-2.5 py-1 text-xs font-semibold text-[#8B1538]">{{ $coupon->type === 'percent' ? $coupon->value.'%' : number_format($coupon->value / 100, 2).' fixed' }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Used {{ $coupon->times_used }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }} times</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 transition group-open:rotate-180">⌄</span>
                </summary>

                <div class="border-t border-gray-100 p-5">
                    <form method="POST" action="{{ route('admin.coupons.update', ['locale' => app()->getLocale(), 'coupon' => $coupon]) }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @csrf
                        @method('PUT')
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Code</span><input name="code" value="{{ $coupon->code }}" required class="rounded-md border border-gray-200 px-3 py-2.5 uppercase"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Type</span><select name="type" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"><option value="percent" @selected($coupon->type === 'percent')>Percentage</option><option value="fixed" @selected($coupon->type === 'fixed')>Fixed amount</option></select></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Value</span><input type="number" step="0.01" min="0.01" name="value" value="{{ $coupon->type === 'percent' ? $coupon->value : number_format($coupon->value / 100, 2, '.', '') }}" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Minimum order</span><input type="number" step="0.01" min="0" name="minimum_order" value="{{ number_format($coupon->minimum_subtotal_minor / 100, 2, '.', '') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Maximum discount</span><input type="number" step="0.01" min="0" name="maximum_discount" value="{{ $coupon->maximum_discount_minor !== null ? number_format($coupon->maximum_discount_minor / 100, 2, '.', '') : '' }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Usage limit</span><input type="number" min="1" name="usage_limit" value="{{ $coupon->usage_limit }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Valid from</span><input type="datetime-local" name="starts_at" value="{{ $coupon->starts_at?->format('Y-m-d\TH:i') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Valid until</span><input type="datetime-local" name="ends_at" value="{{ $coupon->ends_at?->format('Y-m-d\TH:i') }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                        <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" @checked($coupon->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
                        <div class="flex flex-wrap gap-3 md:col-span-2 xl:col-span-3">
                            <button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save changes</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.coupons.destroy', ['locale' => app()->getLocale(), 'coupon' => $coupon]) }}" class="mt-4 border-t border-gray-100 pt-4" onsubmit="return confirm('Delete this discount code?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4" />Delete</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center text-gray-500">No discount codes yet.</div>
        @endforelse
    </div>
</div>
@endsection
