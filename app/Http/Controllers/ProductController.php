<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        $product = Product::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale)->where('slug', $slug))
            ->with(['translations', 'category.translations'])
            ->firstOrFail();

        $translation = $product->translation($locale);
        $availability = $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->short_description ?? ''),
            canonical: route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            alternates: $product->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('products.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $translation?->name,
                'description' => $translation?->short_description,
                'sku' => $product->sku,
                'brand' => ['@type' => 'Brand', 'name' => 'KabulFit'],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => $product->currency,
                    'price' => $product->decimalPrice(),
                    'availability' => $availability,
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'url' => route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]),
                ],
            ],
        );

        return view('catalog.product', compact('product', 'seo'));
    }
}
