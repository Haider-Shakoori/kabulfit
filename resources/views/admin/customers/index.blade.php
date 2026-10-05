@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $customers->total() }} total users</p>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-3 grid place-items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
            <input type="search" placeholder="Search customers..." class="w-full rounded-md border border-gray-200 py-2.5 ps-10 pe-3 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($customers as $customer)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-start gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#8B1538] text-lg font-bold text-white">{{ mb_strtoupper(mb_substr($customer->name ?: $customer->email, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="truncate font-semibold text-gray-900">{{ $customer->name }}</h2>
                                <p class="mt-1 truncate text-sm text-gray-500">{{ $customer->email }}</p>
                            </div>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $customer->is_active,
                                'bg-red-100 text-red-700' => ! $customer->is_active,
                            ])>{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse($customer->roles as $role)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">{{ $role->name }}</span>
                            @empty
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">Customer</span>
                            @endforelse
                        </div>

                        <a href="{{ route('admin.customers.show', ['locale' => app()->getLocale(), 'customer' => $customer->uuid]) }}" class="mt-5 inline-flex items-center gap-2 rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Open <span>›</span></a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-xl border border-gray-200 bg-white py-14 text-center text-sm text-gray-500 shadow-sm">No customers found.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $customers->links() }}</div>
</div>
@endsection
