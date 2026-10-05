@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Accounts</p>
            <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Customers & users</h1>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[260px_1fr]">
        @include('admin._nav')

        <main class="min-w-0">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Customers & users</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage customer accounts, status, and access roles.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-gray-600 shadow-sm">{{ $customers->total() }}</span>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse($customers as $customer)
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#881C27] to-[#2A6867] text-lg font-bold text-white">{{ mb_strtoupper(mb_substr($customer->name ?: $customer->email, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
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

                                <a href="{{ route('admin.customers.show', ['locale' => app()->getLocale(), 'customer' => $customer->uuid]) }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-[#881C27] hover:text-[#881C27]">Open <span>›</span></a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="md:col-span-2 rounded-2xl bg-white py-14 text-center text-gray-500 shadow-sm">No customers found.</div>
                @endforelse
            </div>

            <div class="mt-8">{{ $customers->links() }}</div>
        </main>
    </div>
</div>
@endsection
