<?php

namespace App\Http\Controllers;

use App\Models\CollectionTranslation;
use App\Models\CategoryTranslation;
use App\Models\ProductTranslation;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /api/',
        ];

        foreach (config('kabulfit.supported_locales') as $locale) {
            foreach (['account', 'cart', 'checkout', 'wishlist', 'orders', 'measurements', 'search'] as $path) {
                $lines[] = "Disallow: /{$locale}/{$path}";
            }
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $urls = [];

        foreach (config('kabulfit.supported_locales') as $locale) {
            $urls[] = route('home', ['locale' => $locale]);
            $urls[] = route('shop', ['locale' => $locale]);
        }

        CategoryTranslation::query()->orderBy('category_id')->each(function (CategoryTranslation $translation) use (&$urls): void {
            $urls[] = route('categories.show', ['locale' => $translation->locale, 'slug' => $translation->slug]);
        });

        CollectionTranslation::query()->orderBy('collection_id')->each(function (CollectionTranslation $translation) use (&$urls): void {
            $urls[] = route('collections.show', ['locale' => $translation->locale, 'slug' => $translation->slug]);
        });

        ProductTranslation::query()->orderBy('product_id')->each(function (ProductTranslation $translation) use (&$urls): void {
            $urls[] = route('products.show', ['locale' => $translation->locale, 'slug' => $translation->slug]);
        });

        $xml = view('seo.sitemap', ['urls' => array_values(array_unique($urls))])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
