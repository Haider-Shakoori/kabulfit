@extends('layouts.admin')

@section('content')
@php
    $mainCategories = $categories->filter(fn ($category) => is_null($category->parent_id));
    $allCategoryNames = $categories->map(fn ($category) => $category->translation('en')?->name ?? $category->translation()?->name)->filter()->values();
@endphp

<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Size Guides</h1>
        <p class="mt-1 text-sm text-gray-500">Manage localized size-guide images and descriptions by category.</p>
    </div>

    <details class="group mb-6 overflow-hidden rounded-xl border border-dashed border-gray-300 bg-white shadow-sm">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-[#8B1538] text-white"><x-icon name="plus" class="h-5 w-5" /></span>
                <div><h2 class="font-semibold text-gray-900">Add Size Guide</h2><p class="text-sm text-gray-500">English, Pashto, and Dari content from the Base44 structure.</p></div>
            </div>
            <span class="transition group-open:rotate-180">⌄</span>
        </summary>

        <form method="POST" action="{{ route('admin.size-guides.store', ['locale' => app()->getLocale()]) }}" class="border-t border-gray-100 p-5 sm:p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-1.5"><span class="text-sm font-medium">Title (English) *</span><input name="title" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Category</span><select name="category" class="rounded-md border border-gray-200 bg-white px-3 py-2.5"><option value="">All</option>@foreach($mainCategories as $category)<option value="{{ $category->translation('en')?->name ?? $category->translation()?->name }}">{{ $category->translation('en')?->name ?? $category->translation()?->name }}</option>@endforeach</select></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Title (Pashto)</span><input name="title_pashto" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Title (Dari)</span><input name="title_dari" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Subcategory (Optional)</span><input name="subcategory" list="size-guide-subcategories" class="rounded-md border border-gray-200 px-3 py-2.5" placeholder="e.g., Sharara"></label>
                <datalist id="size-guide-subcategories">@foreach($allCategoryNames as $name)<option value="{{ $name }}"></option>@endforeach</datalist>

                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Size Guide Image (English) *</span><input type="url" name="image_url" required placeholder="https://..." class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Size Guide Image (Pashto)</span><input type="url" name="image_url_pashto" placeholder="https://..." class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Size Guide Image (Dari)</span><input type="url" name="image_url_dari" placeholder="https://..." class="rounded-md border border-gray-200 px-3 py-2.5"></label>

                <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Description (English)</span><textarea name="description" rows="3" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Pashto)</span><textarea name="description_pashto" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Dari)</span><textarea name="description_dari" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>

                <label class="grid gap-1.5"><span class="text-sm font-medium">Display order</span><input type="number" name="display_order" value="0" min="0" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
            </div>
            <div class="mt-5 flex justify-end"><button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]">Create size guide</button></div>
        </form>
    </details>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($guides as $guide)
            <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none">
                    <div class="aspect-[16/10] overflow-hidden bg-gray-100"><img src="{{ $guide->image_url }}" alt="{{ $guide->title }}" class="h-full w-full object-contain"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0"><h2 class="truncate text-lg font-semibold text-gray-900">{{ $guide->title }}</h2><p class="mt-1 text-sm text-gray-500">{{ $guide->category ?: 'All categories' }}@if($guide->subcategory) · {{ $guide->subcategory }}@endif</p></div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $guide->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $guide->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        @if($guide->description)<p class="mt-3 line-clamp-2 text-sm leading-6 text-gray-600">{{ $guide->description }}</p>@endif
                        <div class="mt-4 flex items-center justify-between text-xs text-gray-400"><span>Order {{ $guide->display_order }}</span><span class="transition group-open:rotate-180">⌄</span></div>
                    </div>
                </summary>

                <div class="border-t border-gray-100 p-5">
                    <form method="POST" action="{{ route('admin.size-guides.update', ['locale' => app()->getLocale(), 'sizeGuide' => $guide]) }}" class="grid gap-4">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Title (English)</span><input name="title" value="{{ $guide->title }}" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Category</span><input name="category" value="{{ $guide->category }}" list="size-guide-main-categories" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Title (Pashto)</span><input name="title_pashto" value="{{ $guide->title_pashto }}" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Title (Dari)</span><input name="title_dari" value="{{ $guide->title_dari }}" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5 sm:col-span-2"><span class="text-sm font-medium">Subcategory</span><input name="subcategory" value="{{ $guide->subcategory }}" list="size-guide-subcategories" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5 sm:col-span-2"><span class="text-sm font-medium">English image URL</span><input type="url" name="image_url" value="{{ $guide->image_url }}" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Pashto image URL</span><input type="url" name="image_url_pashto" value="{{ $guide->image_url_pashto }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Dari image URL</span><input type="url" name="image_url_dari" value="{{ $guide->image_url_dari }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="grid gap-1.5 sm:col-span-2"><span class="text-sm font-medium">Description (English)</span><textarea name="description" rows="3" class="rounded-md border border-gray-200 px-3 py-2.5">{{ $guide->description }}</textarea></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Pashto)</span><textarea name="description_pashto" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5">{{ $guide->description_pashto }}</textarea></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Dari)</span><textarea name="description_dari" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5">{{ $guide->description_dari }}</textarea></label>
                            <label class="grid gap-1.5"><span class="text-sm font-medium">Display order</span><input type="number" name="display_order" min="0" value="{{ $guide->display_order }}" class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                            <label class="flex items-center gap-3 self-end rounded-lg bg-gray-50 px-4 py-3"><input type="checkbox" name="is_active" value="1" @checked($guide->is_active) class="h-4 w-4 rounded text-[#8B1538]"><span class="text-sm font-medium">Active</span></label>
                        </div>
                        <button type="submit" class="w-fit rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white">Save size guide</button>
                    </form>

                    <form method="POST" action="{{ route('admin.size-guides.destroy', ['locale' => app()->getLocale(), 'sizeGuide' => $guide]) }}" class="mt-4 border-t border-gray-100 pt-4" onsubmit="return confirm('Delete this size guide?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4" />Delete</button>
                    </form>
                </div>
            </details>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center text-gray-500">No size guides yet.</div>
        @endforelse
    </div>

    <datalist id="size-guide-main-categories">@foreach($mainCategories as $category)<option value="{{ $category->translation('en')?->name ?? $category->translation()?->name }}"></option>@endforeach</datalist>
</div>
@endsection
