<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\Catalog\CatalogQuery;
use App\Services\Settings\SiteSettings;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly SiteSettings $settings) {}

    public function __invoke(string $locale): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('sku', 'like', 'LIVE-F-%')
            ->with(CatalogQuery::cardEagerLoads())
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $bestSellerProducts = Product::query()
            ->where('is_active', true)
            ->where('sku', 'like', 'LIVE-B-%')
            ->with(CatalogQuery::cardEagerLoads())
            ->orderBy('sort_order')
            ->limit(16)
            ->get();

        $homeTitle = $this->settings->get('seo.home.title.'.$locale, __('site.home_title'));
        $homeDescription = $this->settings->get('seo.home.description.'.$locale, __('site.home_description'));

        $seo = new SeoData(
            title: $homeTitle,
            description: $homeDescription,
            canonical: route('home', ['locale' => $locale]),
            alternates: $this->localeAlternates('home'),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'KabulFit',
                'url' => route('home', ['locale' => $locale]),
                'description' => $homeDescription,
            ],
        );

        return view('home', compact('categories', 'featuredProducts', 'bestSellerProducts', 'seo'));
    }

    private function localeAlternates(string $routeName, array $parameters = []): array
    {
        return collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route($routeName, ['locale' => $locale, ...$parameters])])
            ->all();
    }
}
