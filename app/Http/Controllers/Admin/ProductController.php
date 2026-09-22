<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        return view('admin.products.index', [
            'products' => Product::query()->with(['translations', 'category.translations'])->orderBy('sku')->paginate(30),
            'seo' => PrivatePageSeo::make('Admin Products', route('admin.products.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, string $locale, Product $product, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'price_minor' => 'required|integer|min:0',
            'sale_price_minor' => 'nullable|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'tailoring_enabled' => 'nullable|boolean',
            'measurement_garment_type' => 'nullable|in:perahan_tunban,dress,waistcoat',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.short_description' => 'nullable|string|max:500',
            'translations.*.description' => 'nullable|string|max:5000',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:500',
        ]);

        $before = $product->only(['price_minor', 'sale_price_minor', 'stock_quantity', 'is_active', 'is_featured', 'tailoring_enabled', 'measurement_garment_type']);

        DB::transaction(function () use ($product, $data): void {
            $product->update([
                'price_minor' => $data['price_minor'],
                'sale_price_minor' => $data['sale_price_minor'] ?? null,
                'stock_quantity' => $data['stock_quantity'],
                'is_active' => (bool) ($data['is_active'] ?? false),
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'tailoring_enabled' => (bool) ($data['tailoring_enabled'] ?? false),
                'measurement_garment_type' => $data['measurement_garment_type'] ?? null,
            ]);

            foreach (config('kabulfit.supported_locales') as $translationLocale) {
                if (! isset($data['translations'][$translationLocale])) {
                    continue;
                }

                $translation = $data['translations'][$translationLocale];
                $product->translations()->updateOrCreate(
                    ['locale' => $translationLocale],
                    [
                        'name' => $translation['name'],
                        'short_description' => $translation['short_description'] ?? null,
                        'description' => $translation['description'] ?? null,
                        'seo_title' => $translation['seo_title'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                    ],
                );
            }
        });

        $audit->record($request->user(), 'product.updated', $product, [
            'sku' => $product->sku,
            'before' => $before,
            'after' => $product->fresh()->only(array_keys($before)),
        ]);

        return back()->with('status', 'Product updated.');
    }
}
