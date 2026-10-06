@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <a href="{{ route('admin.customers.index', ['locale' => app()->getLocale()]) }}" class="mb-6 inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">← Back to Customers</a>

    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8 flex items-center gap-4">
        <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full bg-[#8B1538] text-2xl font-bold text-white">{{ mb_strtoupper(mb_substr($customer->name ?: $customer->email, 0, 1)) }}</span>
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
            <p class="mt-1 truncate text-sm text-gray-500">{{ $customer->email }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <main class="space-y-6 xl:col-span-2">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Account State</h2>
                <form method="POST" action="{{ route('admin.customers.update', ['locale' => app()->getLocale(), 'customer' => $customer->uuid]) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" @checked($customer->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active account</span></label>
                    <label class="grid gap-1.5 max-w-xs"><span class="text-sm font-medium">Preferred locale</span><select name="preferred_locale" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">@foreach(['en','fa','ps'] as $loc)<option value="{{ $loc }}" @selected($customer->preferred_locale === $loc)>{{ strtoupper($loc) }}</option>@endforeach</select></label>
                    <button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save account</button>
                </form>
            </section>

            @can('manageRoles', $customer)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Roles</h2>
                    <form method="POST" action="{{ route('admin.customers.roles', ['locale' => app()->getLocale(), 'customer' => $customer->uuid]) }}" class="mt-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="roles[]" value="{{ $role->slug }}" @checked($customer->roles->contains('id', $role->id)) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">{{ $role->name }}</span></label>
                            @endforeach
                        </div>
                        <button type="submit" class="mt-5 rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save roles</button>
                    </form>
                </section>
            @endcan

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Orders</h2>
                <div class="mt-4 divide-y divide-gray-100">
                    @forelse($customer->orders as $order)
                        <a href="{{ route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}" class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div><p class="font-semibold text-gray-900">{{ $order->number }}</p><p class="mt-1 text-xs text-gray-400">{{ $order->created_at?->translatedFormat('M j, Y') }}</p></div>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs capitalize text-gray-600">{{ str($order->status)->replace('_', ' ') }}</span>
                        </a>
                    @empty
                        <p class="py-4 text-sm text-gray-500">No orders.</p>
                    @endforelse
                </div>
            </section>
        </main>

        <aside class="space-y-6">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Customer Summary</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt><dd class="font-semibold {{ $customer->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Locale</dt><dd class="font-semibold">{{ strtoupper($customer->preferred_locale) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Addresses</dt><dd class="font-semibold">{{ $customer->addresses->count() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Orders</dt><dd class="font-semibold">{{ $customer->orders->count() }}</dd></div>
                </dl>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Addresses</h2>
                <div class="mt-4 space-y-3">
                    @forelse($customer->addresses as $address)
                        <div class="rounded-lg bg-gray-50 p-3 text-sm text-gray-600">
                            <p class="font-medium text-gray-900">{{ $address->label ?: 'Address' }}</p>
                            <p class="mt-1">{{ $address->address_line1 }}, {{ $address->city }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No saved addresses.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
