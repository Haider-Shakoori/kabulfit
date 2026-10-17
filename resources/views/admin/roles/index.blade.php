@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
        <p class="mt-1 text-sm text-gray-500">Control administrator and staff access across KabulFit.</p>
    </div>

    <div class="space-y-5">
        @foreach($roles as $role)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h2>
                        <p class="mt-1 font-mono text-xs text-gray-400">{{ $role->slug }}</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $role->permissions->count() }} permissions</span>
                </div>

                @if($role->slug === 'super-admin')
                    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">Protected role: always has every permission.</div>
                @else
                    <form method="POST" action="{{ route('admin.roles.update', ['locale' => app()->getLocale(), 'role' => $role]) }}" class="mt-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-start gap-3 rounded-lg bg-gray-50 px-4 py-3">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->slug }}" @checked($role->permissions->contains('id', $permission->id)) class="mt-0.5 h-4 w-4 rounded text-[#8B1538] focus:ring-[#8B1538]">
                                    <span><strong class="block text-sm text-gray-900">{{ $permission->name }}</strong><span class="mt-1 block font-mono text-[11px] text-gray-400">{{ $permission->slug }}</span></span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Save permissions</button></div>
                    </form>
                @endif
            </section>
        @endforeach
    </div>
</div>
@endsection
