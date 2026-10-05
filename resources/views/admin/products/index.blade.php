@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFBF7]">
    <section class="bg-gradient-to-r from-[#881C27] to-[#2A6867] py-10 text-white">
        <div class="mx-auto max-w-7xl px-4">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Catalog administration</p>
            <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Products</h1>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[260px_1fr]">
        @include('admin._nav')

        <main class="min-w-0">
            <x-form-errors />
            @if(session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
            @endif

            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage pricing, stock, visibility, tailoring, and localized content.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-gray-600 shadow-sm">{{ $products->total() }}</span>
            </div>

            <div class="space-y-4">
                @foreach($products as $product)
                    @php
                        $translations = $product->translations->keyBy('locale');
                        $display = $translations->get(app()->getLocale())?->name ?? $translations->get('en')?->name;
                    @endphp
                    <details class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-semibold text-[#2A6867]">{{ $product->sku }}</span>
                                    @if($product->is_featured)<span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700">Featured</span>@endif
                                    @if(!$product->is_active)<span class="rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-semibold text-red-700">Inactive</span>@endif
                                </div>
                                <h2 class="mt-2 truncate text-lg font-semibold text-gray-900">{{ $display }}</h2>
                                <p class="mt-1 text-sm text-gray-500">{{ $product->formattedPrice() }} · Stock {{ $product->availableStock() }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gray-100 text-xl text-gray-500 transition group-open:rotate-180">⌄</span>
                        </summary>

                        <form method="POST" action="{{ route('admin.products.update', ['locale' => app()->getLocale(), 'product' => $product->sku]) }}" class="border-t border-gray-100 p-5 sm:p-6">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <label class="grid gap-1.5"><span class="text-sm font-medium text-gray-700">Price minor units</span><input name="price_minor" type="number" min="0" value="{{ $product->price_minor }}" required class="rounded-xl border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27]"></label>
                                <label class="grid gap-1.5"><span class="text-sm font-medium text-gray-700">Sale price minor units</span><input name="sale_price_minor" type="number" min="0" value="{{ $product->sale_price_minor }}" class="rounded-xl border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27]"></label>
                                <label class="grid gap-1.5"><span class="text-sm font-medium text-gray-700">Fallback stock</span><input name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}" required class="rounded-xl border border-gray-200 px-3 py-2.5 outline-none focus:border-[#881C27]"></label>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                @foreach([
                                    ['is_active', 'Active', $product->is_active],
                                    ['is_featured', 'Featured', $product->is_featured],
                                    ['tailoring_enabled', 'Tailoring enabled', $product->tailoring_enabled],
                                ] as [$name, $label, $checked])
                                    <label class="flex items-center gap-3 rounded-xl bg-gray-50 px-4 py-3">
                                        <input name="{{ $name }}" type="checkbox" value="1" @checked($checked) class="h-4 w-4 rounded border-gray-300 text-[#881C27] focus:ring-[#881C27]">
                                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <label class="mt-4 grid gap-1.5 md:max-w-sm">
                                <span class="text-sm font-medium text-gray-700">Measurement garment type</span>
                                <select name="measurement_garment_type" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]">
                                    <option value="">None</option>
                                    @foreach(['perahan_tunban', 'dress', 'waistcoat'] as $type)
                                        <option value="{{ $type }}" @selected($product->measurement_garment_type === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <div class="mt-6 grid gap-5">
                                @foreach(config('kabulfit.supported_locales') as $locale)
                                    @php($t = $translations->get($locale))
                                    <fieldset class="rounded-2xl border border-gray-100 bg-[#FDFBF7] p-4">
                                        <legend class="px-2 text-xs font-bold uppercase tracking-[0.15em] text-[#881C27]">{{ strtoupper($locale) }}</legend>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium text-gray-700">Name</span><input name="translations[{{ $locale }}][name]" value="{{ $t?->name }}" required class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]"></label>
                                            <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium text-gray-700">Short description</span><textarea name="translations[{{ $locale }}][short_description]" rows="2" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]">{{ $t?->short_description }}</textarea></label>
                                            <label class="grid gap-1.5 md:col-span-2"><span class="text-sm font-medium text-gray-700">Description</span><textarea name="translations[{{ $locale }}][description]" rows="5" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]">{{ $t?->description }}</textarea></label>
                                            <label class="grid gap-1.5"><span class="text-sm font-medium text-gray-700">SEO title</span><input name="translations[{{ $locale }}][seo_title]" value="{{ $t?->seo_title }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]"></label>
                                            <label class="grid gap-1.5"><span class="text-sm font-medium text-gray-700">SEO description</span><textarea name="translations[{{ $locale }}][seo_description]" rows="2" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 outline-none focus:border-[#881C27]">{{ $t?->seo_description }}</textarea></label>
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-[#881C27] to-[#2A6867] px-6 py-3 text-sm font-semibold text-white">Save product</button>
                            </div>
                        </form>
                    </details>
                @endforeach
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        </main>
    </div>
</div>
@endsection
