<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogFilterRequest;
use App\Models\CatalogCollection;
use App\Models\Category;
use App\Models\ProductOption;
use App\Services\Catalog\CatalogProductQuery;
use App\Support\Seo\CatalogSchema;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(
        CatalogFilterRequest $request,
        string $locale,
        CatalogProductQuery $catalog,
        CatalogSchema $schema,
    ): View {
        $filters = $request->catalogFilters();
        $products = $catalog->build($locale, $filters)->paginate(12)->withQueryString();
        $facets = $this->facets($locale);

        $seo = new SeoData(
            title: __('site.shop_title'),
            description: __('site.shop_description'),
            canonical: route('shop', ['locale' => $locale]),
            alternates: $this->shopAlternates(),
            robots: $request->query() === [] ? 'index,follow' : 'noindex,follow',
            jsonLd: $schema->listing(
                __('site.shop_title_h1'),
                route('shop', ['locale' => $locale]),
                $products->getCollection(),
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                ],
            ),
        );

        return view('catalog.index', compact('products', 'seo', 'filters', 'facets'));
    }

    public function category(
        CatalogFilterRequest $request,
        string $locale,
        string $slug,
        CatalogProductQuery $catalog,
        CatalogSchema $schema,
    ): View {
        $category = Category::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with('translations')
            ->firstOrFail();

        $translation = $category->translation($locale);
        $filters = [...$request->catalogFilters(), 'category' => $slug];
        $products = $catalog->build($locale, $filters)->paginate(12)->withQueryString();
        $facets = $this->facets($locale);
        $canonical = route('categories.show', ['locale' => $locale, 'slug' => $translation?->slug]);

        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->description ?? __('site.shop_description')),
            canonical: $canonical,
            alternates: $category->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('categories.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            robots: $request->query() === [] ? 'index,follow' : 'noindex,follow',
            jsonLd: $schema->listing(
                $translation?->name ?? 'KabulFit',
                $canonical,
                $products->getCollection(),
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    ['name' => $translation?->name, 'url' => $canonical],
                ],
            ),
        );

        return view('catalog.category', compact('category', 'products', 'seo', 'filters', 'facets'));
    }

    public function collection(
        CatalogFilterRequest $request,
        string $locale,
        string $slug,
        CatalogProductQuery $catalog,
        CatalogSchema $schema,
    ): View {
        $collection = CatalogCollection::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with('translations')
            ->firstOrFail();

        $translation = $collection->translation($locale);
        $filters = [...$request->catalogFilters(), 'collection' => $slug];
        $products = $catalog->build($locale, $filters)->paginate(12)->withQueryString();
        $facets = $this->facets($locale);
        $canonical = route('collections.show', ['locale' => $locale, 'slug' => $translation?->slug]);

        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->description ?? __('site.shop_description')),
            canonical: $canonical,
            alternates: $collection->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('collections.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            robots: $request->query() === [] ? 'index,follow' : 'noindex,follow',
            jsonLd: $schema->listing(
                $translation?->name ?? 'KabulFit',
                $canonical,
                $products->getCollection(),
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    ['name' => $translation?->name, 'url' => $canonical],
                ],
            ),
        );

        return view('catalog.collection', compact('collection', 'products', 'seo', 'filters', 'facets'));
    }

    private function facets(string $locale): array
    {
        return [
            'categories' => Category::query()
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get(),
            'collections' => CatalogCollection::query()
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get(),
            'options' => ProductOption::query()
                ->where('is_filterable', true)
                ->with(['translations', 'values.translations'])
                ->orderBy('sort_order')
                ->get(),
            'locale' => $locale,
        ];
    }

    private function shopAlternates(): array
    {
        return collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route('shop', ['locale' => $locale])])
            ->all();
    }
}
