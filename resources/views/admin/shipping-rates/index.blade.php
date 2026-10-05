@extends('layouts.admin')

@section('content')
@php
    $initialRanges = old('ranges', [
        ['min_kg' => 0, 'max_kg' => 1, 'price' => ''],
        ['min_kg' => 1, 'max_kg' => 2, 'price' => ''],
        ['min_kg' => 2, 'max_kg' => 3, 'price' => ''],
        ['min_kg' => 3, 'max_kg' => 5, 'price' => ''],
    ]);
@endphp

<div
    class="p-4 sm:p-6 lg:p-8"
    x-data="{
        ranges: @js($initialRanges),
        addRange() {
            const last = this.ranges[this.ranges.length - 1] || { max_kg: 0 };
            const min = Number(last.max_kg || 0);
            this.ranges.push({ min_kg: min, max_kg: min + 1, price: '' });
        },
        removeRange(index) {
            if (this.ranges.length > 1) this.ranges.splice(index, 1);
        }
    }"
>
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Shipping Rates Management</h1>
        <p class="mt-1 text-sm text-gray-500">Manage shipping rates based on total order weight.</p>
    </div>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-5 sm:p-6">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                <x-icon name="truck" class="h-5 w-5 text-[#8B1538]" />
                Add New Shipping Rate
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.shipping-rates.store', ['locale' => app()->getLocale()]) }}" class="space-y-5 p-5 sm:p-6">
            @csrf

            <div>
                <p class="mb-3 text-sm font-semibold text-gray-900">Weight Ranges & Prices *</p>
                <div class="space-y-3">
                    <template x-for="(range, index) in ranges" :key="index">
                        <div class="flex items-end gap-3 rounded-lg bg-gray-50 p-3">
                            <div class="grid min-w-0 flex-1 grid-cols-1 gap-3 sm:grid-cols-3">
                                <label class="grid gap-1.5">
                                    <span class="text-xs font-medium text-gray-600">Min Weight (kg)</span>
                                    <input type="number" step="0.1" min="0" :name="'ranges[' + index + '][min_kg]'" x-model="range.min_kg" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
                                </label>
                                <label class="grid gap-1.5">
                                    <span class="text-xs font-medium text-gray-600">Max Weight (kg)</span>
                                    <input type="number" step="0.1" min="0.1" :name="'ranges[' + index + '][max_kg]'" x-model="range.max_kg" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
                                </label>
                                <label class="grid gap-1.5">
                                    <span class="text-xs font-medium text-gray-600">Price (USD)</span>
                                    <input type="number" step="0.01" min="0" :name="'ranges[' + index + '][price]'" x-model="range.price" required placeholder="30.00" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
                                </label>
                            </div>
                            <button type="button" @click="removeRange(index)" :disabled="ranges.length <= 1" class="grid h-10 w-10 shrink-0 place-items-center rounded-md text-red-500 transition hover:bg-red-50 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Remove range">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addRange()" class="mt-3 inline-flex items-center gap-2 rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <x-icon name="plus" class="h-4 w-4" />
                    Add Weight Range
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium text-gray-700">Effective Date</span>
                    <input type="date" name="effective_date" value="{{ old('effective_date') }}" class="rounded-md border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
                </label>
                <label class="grid gap-1.5">
                    <span class="text-sm font-medium text-gray-700">Notes (Optional)</span>
                    <input name="notes" value="{{ old('notes') }}" placeholder="e.g., Rate increase due to fuel costs" class="rounded-md border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
                </label>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6d102c]">
                <x-icon name="truck" class="h-4 w-4" />
                Add Shipping Rate
            </button>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-5 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900">Current Shipping Rates</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-start text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-start">Weight Ranges & Prices</th>
                        <th class="px-5 py-3 text-start">Effective Date</th>
                        <th class="px-5 py-3 text-start">Created Date</th>
                        <th class="px-5 py-3 text-start">Notes</th>
                        <th class="px-5 py-3 text-start">Status</th>
                        <th class="px-5 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($rates as $rate)
                        <tr class="align-top">
                            <td class="px-5 py-4">
                                <div class="space-y-1.5">
                                    @foreach($rate->weight_ranges ?? [] as $range)
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-md border border-gray-200 px-2 py-1 font-mono text-xs text-gray-700">{{ $range['min_kg'] }}-{{ $range['max_kg'] }} kg</span>
                                            <strong class="text-[#8B1538]">{{ number_format((float) $range['price'], 2) }} USD</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $rate->effective_date?->translatedFormat('M j, Y') ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $rate->created_at?->translatedFormat('M j, Y') }}</td>
                            <td class="max-w-xs px-5 py-4 text-gray-600"><p class="line-clamp-2">{{ $rate->notes ?: '—' }}</p></td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-green-100 text-green-700' => $rate->is_active,
                                    'bg-gray-100 text-gray-600' => ! $rate->is_active,
                                ])>{{ $rate->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-1">
                                    <form method="POST" action="{{ route('admin.shipping-rates.toggle', ['locale' => app()->getLocale(), 'shippingRate' => $rate]) }}">
                                        @csrf
                                        <button type="submit" class="rounded-md px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50">{{ $rate->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.shipping-rates.destroy', ['locale' => app()->getLocale(), 'shippingRate' => $rate]) }}" onsubmit="return confirm('Delete this shipping rate?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-md text-red-500 transition hover:bg-red-50 hover:text-red-700" aria-label="Delete shipping rate">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No shipping rates configured yet. Add one above to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rates->isNotEmpty())
            <div class="m-5 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 sm:m-6">
                <h3 class="font-semibold text-blue-900">How it works</h3>
                <ul class="mt-2 space-y-1">
                    <li>• The newest active configuration is the rate set used for orders.</li>
                    <li>• Afghanistan uses the platform’s configured domestic shipping rule.</li>
                    <li>• International shipping uses the total order weight and the matching range.</li>
                    <li>• Deactivate older rate sets instead of deleting them when you want to preserve history.</li>
                </ul>
            </div>
        @endif
    </section>
</div>
@endsection
