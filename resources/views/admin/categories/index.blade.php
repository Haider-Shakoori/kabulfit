@extends('layouts.admin')

@section('content')
@php
    $locale = app()->getLocale();
    $categoryData = $categories->map(function ($category) {
        $translations = $category->translations->keyBy('locale');

        return [
            'id' => $category->id,
            'name' => $translations->get('en')?->name ?? '',
            'name_pashto' => $translations->get('ps')?->name ?? '',
            'name_dari' => $translations->get('fa')?->name ?? '',
            'slug' => $translations->get('en')?->slug ?? '',
            'slug_pashto' => $translations->get('ps')?->slug ?? '',
            'slug_dari' => $translations->get('fa')?->slug ?? '',
            'description' => $translations->get('en')?->description ?? '',
            'description_pashto' => $translations->get('ps')?->description ?? '',
            'description_dari' => $translations->get('fa')?->description ?? '',
            'image_url' => $category->image_url ?? '',
            'parent_id' => $category->parent_id,
            'sort_order' => $category->sort_order,
            'is_active' => $category->is_active,
            'products_count' => $category->products_count,
        ];
    })->values()->all();
@endphp

<div
    class="p-4 sm:p-6 lg:p-8"
    x-data="{
        open: false,
        editing: false,
        form: {
            id: null,
            name: '',
            name_pashto: '',
            name_dari: '',
            slug: '',
            slug_pashto: '',
            slug_dari: '',
            description: '',
            description_pashto: '',
            description_dari: '',
            image_url: '',
            parent_id: '',
            sort_order: 0,
            is_active: true
        },
        categories: @js($categoryData),
        reset() {
            this.editing = false;
            this.form = {
                id: null,
                name: '',
                name_pashto: '',
                name_dari: '',
                slug: '',
                slug_pashto: '',
                slug_dari: '',
                description: '',
                description_pashto: '',
                description_dari: '',
                image_url: '',
                parent_id: '',
                sort_order: 0,
                is_active: true
            };
        },
        add() {
            this.reset();
            this.open = true;
        },
        edit(category) {
            this.editing = true;
            this.form = { ...category, parent_id: category.parent_id ?? '' };
            this.open = true;
        },
        action() {
            return this.editing
                ? @js(url('/'.$locale.'/admin/categories')).replace(/\/$/, '') + '/' + this.form.id
                : @js(route('admin.categories.store', ['locale' => $locale]));
        }
    }"
    @keydown.escape.window="open = false"
>
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $categories->count() }} total categories</p>
        </div>
        <button type="button" @click="add()" class="inline-flex items-center gap-2 rounded-md bg-[#8B1538] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6d102c]">
            <x-icon name="plus" class="h-4 w-4" />
            Add Category
        </button>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($categories as $category)
            @php
                $translation = $category->translation('en') ?? $category->translation();
                $parentTranslation = $category->parent?->translation('en') ?? $category->parent?->translation();
            @endphp
            <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                <div class="relative aspect-[16/9] bg-gray-100">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $translation?->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="grid h-full w-full place-items-center bg-gradient-to-br from-[#8B1538]/10 to-[#2A6867]/10">
                            <x-icon name="tag" class="h-12 w-12 text-[#8B1538]/50" />
                        </div>
                    @endif
                    <span @class([
                        'absolute end-3 top-3 rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-green-100 text-green-700' => $category->is_active,
                        'bg-gray-200 text-gray-600' => ! $category->is_active,
                    ])>{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-semibold text-gray-900">{{ $translation?->name }}</h2>
                            <p class="mt-1 truncate text-sm text-gray-500">/{{ $translation?->slug }}</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">#{{ $category->sort_order }}</span>
                    </div>

                    @if($translation?->description)
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-gray-600">{{ $translation->description }}</p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs text-purple-700">{{ $category->products_count }} products</span>
                        @if($parentTranslation)
                            <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs text-gray-600">Parent: {{ $parentTranslation->name }}</span>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4">
                        <button type="button" @click="edit(categories.find(item => item.id === {{ $category->id }}))" class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-[#8B1538]">
                            ✎ Edit
                        </button>
                        <form method="POST" action="{{ route('admin.categories.destroy', ['locale' => $locale, 'category' => $category]) }}" onsubmit="return confirm('Are you sure you want to delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="grid h-9 w-9 place-items-center rounded-md text-red-500 transition hover:bg-red-50 hover:text-red-700" aria-label="Delete category">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-gray-300 bg-white py-16 text-center text-gray-500">
                No categories yet.
            </div>
        @endforelse
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-[100] grid place-items-center p-4">
        <button type="button" class="absolute inset-0 bg-black/50" @click="open = false" aria-label="Close"></button>
        <div x-transition class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white p-5">
                <h2 class="text-xl font-semibold text-gray-900" x-text="editing ? 'Edit Category' : 'Add New Category'"></h2>
                <button type="button" @click="open = false" class="grid h-9 w-9 place-items-center rounded-lg bg-gray-100"><x-icon name="close" class="h-4 w-4" /></button>
            </div>

            <form method="POST" :action="action()" class="space-y-5 p-5 sm:p-6">
                @csrf
                <input x-show="editing" type="hidden" name="_method" value="PUT">

                <div>
                    <label class="mb-1.5 block text-sm font-medium">Name (English) *</label>
                    <input name="translations[en][name]" x-model="form.name" required class="w-full rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Name (Pashto) *</span><input name="translations[ps][name]" x-model="form.name_pashto" required dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Name (Dari) *</span><input name="translations[fa][name]" x-model="form.name_dari" required dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Slug (English) *</span><input name="translations[en][slug]" x-model="form.slug" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Slug (Pashto) *</span><input name="translations[ps][slug]" x-model="form.slug_pashto" required dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Slug (Dari) *</span><input name="translations[fa][slug]" x-model="form.slug_dari" required dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description (English)</span><textarea name="translations[en][description]" x-model="form.description" rows="3" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Pashto)</span><textarea name="translations[ps][description]" x-model="form.description_pashto" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Description (Dari)</span><textarea name="translations[fa][description]" x-model="form.description_dari" rows="3" dir="rtl" class="rounded-md border border-gray-200 px-3 py-2.5"></textarea></label>
                </div>

                <label class="grid gap-1.5"><span class="text-sm font-medium">Image URL</span><input type="url" name="image_url" x-model="form.image_url" placeholder="https://..." class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1.5">
                        <span class="text-sm font-medium">Parent Category</span>
                        <select name="parent_id" x-model="form.parent_id" class="rounded-md border border-gray-200 bg-white px-3 py-2.5">
                            <option value="">None</option>
                            @foreach($categories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->translation('en')?->name ?? $parent->translation()?->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid gap-1.5"><span class="text-sm font-medium">Display Order</span><input type="number" min="0" max="9999" name="sort_order" x-model.number="form.sort_order" required class="rounded-md border border-gray-200 px-3 py-2.5"></label>
                </div>

                <label class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <span><strong class="block text-sm text-gray-900">Active</strong><span class="text-xs text-gray-500">Show this category on the storefront.</span></span>
                    <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="h-5 w-5 rounded text-[#8B1538] focus:ring-[#8B1538]">
                </label>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" @click="open = false" class="rounded-md border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700">Cancel</button>
                    <button type="submit" class="rounded-md bg-[#8B1538] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6d102c]" x-text="editing ? 'Update Category' : 'Create Category'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
