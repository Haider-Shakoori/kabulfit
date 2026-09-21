<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogFilterRequest;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Size;
use App\Services\Catalog\CatalogQuery;
use App\Support\Seo\CatalogSchema;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(
        CatalogFilterRequest $request,
        string $locale,
        CatalogQuery $catalog,
    ): View {
        $products = $catalog->paginate($request, $locale);
        $filterOptions = $this->filterOptions();
        $filters = $request->validated();

        $seo = new SeoData(
            title: __('site.shop_title'),
            description: __('site.shop_description'),
            canonical: route('shop', ['locale' => $locale]),
            alternates: $this->shopAlternates(),
            jsonLd: CatalogSchema::listing(
                $products,
                __('site.shop_title_h1'),
                route('shop', ['locale' => $locale]),
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                ],
                $locale,
            ),
        );

        return view('catalog.index', compact('products', 'filterOptions', 'filters', 'seo'));
    }

    public function category(
        CatalogFilterRequest $request,
        string $locale,
        string $slug,
        CatalogQuery $catalog,
    ): View {
        $category = Category::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with('translations')
            ->firstOrFail();

        $translation = $category->translation($locale);
        $products = $catalog->paginate($request, $locale, categorySlug: $slug);
        $filterOptions = $this->filterOptions();
        $filters = $request->validated();

        $canonical = route('categories.show', ['locale' => $locale, 'slug' => $translation?->slug]);
        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->description ?? __('site.shop_description')),
            canonical: $canonical,
            alternates: $category->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('categories.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: CatalogSchema::listing(
                $products,
                $translation?->name ?? __('site.shop'),
                $canonical,
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    ['name' => $translation?->name ?? __('site.shop'), 'url' => $canonical],
                ],
                $locale,
            ),
        );

        return view('catalog.category', compact('category', 'products', 'filterOptions', 'filters', 'seo'));
    }

    public function collection(
        CatalogFilterRequest $request,
        string $locale,
        string $slug,
        CatalogQuery $catalog,
    ): View {
        $collection = Collection::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with('translations')
            ->firstOrFail();

        $translation = $collection->translation($locale);
        $products = $catalog->paginate($request, $locale, collectionSlug: $slug);
        $filterOptions = $this->filterOptions();
        $filters = $request->validated();

        $canonical = route('collections.show', ['locale' => $locale, 'slug' => $translation?->slug]);
        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->description ?? __('site.shop_description')),
            canonical: $canonical,
            alternates: $collection->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('collections.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: CatalogSchema::listing(
                $products,
                $translation?->name ?? __('site.shop'),
                $canonical,
                [
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    ['name' => $translation?->name ?? __('site.shop'), 'url' => $canonical],
                ],
                $locale,
            ),
        );

        return view('catalog.collection', compact('collection', 'products', 'filterOptions', 'filters', 'seo'));
    }

    private function filterOptions(): array
    {
        return [
            'categories' => Category::query()
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get(),
            'collections' => Collection::query()
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get(),
            'sizes' => Size::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'colors' => Color::query()
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get(),
        ];
    }

    private function shopAlternates(): array
    {
        return collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route('shop', ['locale' => $locale])])
            ->all();
    }
}
