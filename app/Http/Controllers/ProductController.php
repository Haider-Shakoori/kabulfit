<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Catalog\CatalogProductQuery;
use App\Support\Seo\CatalogSchema;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(
        string $locale,
        string $slug,
        CatalogProductQuery $catalog,
        CatalogSchema $schema,
    ): View {
        $product = $catalog
            ->build($locale)
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->firstOrFail();

        $translation = $product->translation($locale);

        $related = $catalog
            ->build($locale)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit(4)
            ->get();

        $recentIds = collect(session()->get('recently_viewed_products', []))
            ->reject(fn ($id) => (int) $id === $product->id)
            ->take(4)
            ->values();

        $recentlyViewed = $this->recentlyViewed($catalog, $locale, $recentIds);

        session()->put(
            'recently_viewed_products',
            collect([$product->id])
                ->merge(session()->get('recently_viewed_products', []))
                ->unique()
                ->take(8)
                ->values()
                ->all(),
        );

        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->short_description ?? ''),
            canonical: route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            alternates: $product->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('products.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: $schema->product($product, $locale),
        );

        return view('catalog.product', compact('product', 'related', 'recentlyViewed', 'seo'));
    }

    private function recentlyViewed(CatalogProductQuery $catalog, string $locale, Collection $recentIds): Collection
    {
        if ($recentIds->isEmpty()) {
            return collect();
        }

        $products = $catalog->build($locale)->whereIn('id', $recentIds)->get();

        return $products
            ->sortBy(fn (Product $product) => $recentIds->search($product->id))
            ->values();
    }
}
