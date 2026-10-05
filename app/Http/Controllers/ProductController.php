<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SizeGuide;
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

        $product->loadMissing('category.parent.translations');

        $translation = $product->translation($locale);
        $categoryTranslation = $product->category->translation($locale);
        $categoryEnglish = $product->category->translation('en')?->name;
        $parentEnglish = $product->category->parent?->translation('en')?->name;
        $sizeGuideCategory = $parentEnglish ?: $categoryEnglish;
        $sizeGuideSubcategory = $parentEnglish ? $categoryEnglish : null;

        $sizeGuides = SizeGuide::query()
            ->where('is_active', true)
            ->orderByDesc('display_order')
            ->get()
            ->filter(function (SizeGuide $guide) use ($sizeGuideCategory, $sizeGuideSubcategory): bool {
                if ($guide->category && strcasecmp($guide->category, (string) $sizeGuideCategory) !== 0) {
                    return false;
                }

                if ($sizeGuideSubcategory) {
                    return $guide->subcategory && strcasecmp($guide->subcategory, $sizeGuideSubcategory) === 0;
                }

                return blank($guide->subcategory);
            })
            ->values();
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

        return view('catalog.product', compact('product', 'relatedProducts', 'recentlyViewed', 'sizeGuides', 'seo'));
    }
}
