@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <x-form-errors />
    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $products->total() }} total products</p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-md bg-[#8B1538] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6d102c]" onclick="document.getElementById('first-product-editor')?.scrollIntoView({behavior:'smooth'})">
            <x-icon name="plus" class="h-4 w-4" />
            Add Product
        </button>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-3 grid place-items-center text-gray-400"><x-icon name="search" class="h-4 w-4" /></span>
            <input type="search" placeholder="Search products..." class="w-full rounded-md border border-gray-200 py-2.5 ps-10 pe-3 text-sm outline-none focus:border-[#8B1538] focus:ring-2 focus:ring-[#8B1538]/10">
        </div>
    </div>

    <div class="space-y-4">
        @foreach($products as $product)
            @php
                $translations = $product->translations->keyBy('locale');
                $display = $translations->get(app()->getLocale())?->name ?? $translations->get('en')?->name;
            @endphp
            <details id="{{ $loop->first ? 'first-product-editor' : null }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-[#8B1538]">{{ $product->sku }}</span>
                            @if($product->is_featured)<span class="rounded-full bg-purple-100 px-2.5 py-1 text-[10px] font-semibold text-purple-700">Featured</span>@endif
                            @if(!$product->is_active)<span class="rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-semibold text-red-700">Inactive</span>@endif
                        </div>
                        <h2 class="mt-2 truncate text-base font-semibold text-gray-900 sm:text-lg">{{ $display }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $product->formattedPrice() }} · Stock {{ $product->availableStock() }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-gray-100 text-gray-500 transition group-open:rotate-180">⌄</span>
                </summary>

                <form method="POST" action="{{ route('admin.products.update', ['locale' => app()->getLocale(), 'product' => $product->sku]) }}" class="border-t border-gray-100 p-5 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="mb-5 flex flex-wrap gap-2">
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">English</span>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">پښتو (Pashto)</span>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">دری (Dari)</span>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Price minor units</span><input name="price_minor" type="number" min="0" value="{{ $product->price_minor }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Sale price minor units</span><input name="sale_price_minor" type="number" min="0" value="{{ $product->sale_price_minor }}" class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                        <label class="grid gap-1.5"><span class="text-sm font-medium">Fallback stock</span><input name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}" required class="rounded-md border border-gray-200 px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        @foreach([
                            ['is_active', 'Active', $product->is_active],
                            ['is_featured', 'Featured', $product->is_featured],
                            ['tailoring_enabled', 'Tailoring enabled', $product->tailoring_enabled],
                        ] as [$name, $label, $checked])
                            <label class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-3">
                                <input name="{{ $name }}" type="checkbox" value="1" @checked($checked) class="h-4 w-4 rounded border-gray-300 text-[#8B1538] focus:ring-[#8B1538]">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <label class="mt-4 grid gap-1.5 md:max-w-sm">
                        <span class="text-sm font-medium">Measurement garment type</span>
                        <select name="measurement_garment_type" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]">
                            <option value="">None</option>
                            @foreach(['perahan_tunban', 'dress', 'waistcoat'] as $type)
                                <option value="{{ $type }}" @selected($product->measurement_garment_type === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                    </label>

                    <div class="mt-6 grid gap-5">
                        @foreach(config('kabulfit.supported_locales') as $locale)
                            @php($t = $translations->get($locale))
                            <fieldset class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                                <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#8B1538]">{{ strtoupper($locale) }}</legend>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Name</span><input name="translations[{{ $locale }}][name]" value="{{ $t?->name }}" required class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                                    <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Short description</span><textarea name="translations[{{ $locale }}][short_description]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $t?->short_description }}</textarea></label>
                                    <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium">Description</span><textarea name="translations[{{ $locale }}][description]" rows="4" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $t?->description }}</textarea></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">SEO title</span><input name="translations[{{ $locale }}][seo_title]" value="{{ $t?->seo_title }}" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]"></label>
                                    <label class="grid gap-1.5"><span class="text-sm font-medium">SEO description</span><textarea name="translations[{{ $locale }}][seo_description]" rows="2" class="rounded-md border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#8B1538]">{{ $t?->seo_description }}</textarea></label>
                                </div>
                            </fieldset>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="rounded-md bg-[#8B1538] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6d102c]">Save product</button>
                    </div>
                </form>
            </details>
        @endforeach
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
</div>
@endsection
