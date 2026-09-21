<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(string $locale): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with([
                'translations',
                'category.translations',
                'media.translations',
                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with(['inventory', 'optionValues.translations', 'optionValues.option.translations']),
            ])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $seo = new SeoData(
            title: __('site.home_title'),
            description: __('site.home_description'),
            canonical: route('home', ['locale' => $locale]),
            alternates: $this->localeAlternates('home'),
            jsonLd: [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'KabulFit',
                'url' => route('home', ['locale' => $locale]),
                'description' => __('site.home_description'),
            ],
        );

        return view('home', compact('categories', 'featuredProducts', 'seo'));
    }

    private function localeAlternates(string $routeName, array $parameters = []): array
    {
        return collect(config('kabulfit.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => route($routeName, ['locale' => $locale, ...$parameters])])
            ->all();
    }
}
