<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Catalog\CatalogQuery;
use App\Services\Catalog\RecentlyViewedProducts;
use App\Support\Seo\CatalogSchema;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(
        string $locale,
        string $slug,
        RecentlyViewedProducts $recentlyViewedProducts,
    ): View {
        $product = Product::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with(CatalogQuery::detailEagerLoads())
            ->firstOrFail();

        $translation = $product->translation($locale);
        $categoryTranslation = $product->category->translation($locale);
        $canonical = route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]);

        $relatedProducts = $product->relatedProducts()
            ->where('is_active', true)
            ->with(CatalogQuery::cardEagerLoads())
            ->limit(4)
            ->get();

        $recentlyViewed = $recentlyViewedProducts->excluding($product);
        $recentlyViewedProducts->remember($product);

        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->short_description ?? ''),
            canonical: $canonical,
            alternates: $product->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('products.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: CatalogSchema::product(
                $product,
                $locale,
                $canonical,
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    [
                        'name' => $categoryTranslation?->name ?? __('site.shop'),
                        'url' => route('categories.show', [
                            'locale' => $locale,
                            'slug' => $categoryTranslation?->slug,
                        ]),
                    ],
                    ['name' => $translation?->name ?? $product->sku, 'url' => $canonical],
                ],
            ),
        );

        return view('catalog.product', compact('product', 'relatedProducts', 'recentlyViewed', 'seo'));
    }
}
