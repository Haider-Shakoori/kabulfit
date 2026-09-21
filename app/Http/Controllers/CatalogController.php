<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['translations', 'category.translations'])
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        $seo = new SeoData(
            title: __('site.shop_title'),
            description: __('site.shop_description'),
            canonical: route('shop', ['locale' => $locale]),
            alternates: $this->shopAlternates(),
        );

        return view('catalog.index', compact('products', 'seo'));
    }

    public function category(string $locale, string $slug): View
    {
        $category = Category::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale)->where('slug', $slug))
            ->with([
                'translations',
                'products' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with(['translations', 'category.translations'])
                    ->orderBy('sort_order'),
            ])
            ->firstOrFail();

        $translation = $category->translation($locale);
        $seo = new SeoData(
            title: $translation?->seo_title ?: ($translation?->name.' | KabulFit'),
            description: $translation?->seo_description ?: ($translation?->description ?? __('site.shop_description')),
            canonical: route('categories.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            alternates: $category->translations->mapWithKeys(fn ($item) => [
                $item->locale => route('categories.show', ['locale' => $item->locale, 'slug' => $item->slug]),
            ])->all(),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $translation?->name,
                'url' => route('categories.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            ],
        );

        return view('catalog.category', compact('category', 'seo'));
    }

    private function shopAlternates(): array
    {
        return collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route('shop', ['locale' => $locale])])
            ->all();
    }
}
