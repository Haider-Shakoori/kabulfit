<?php

namespace App\Http\Controllers;

use App\Models\BlogPostTranslation;
use App\Models\CategoryTranslation;
use App\Models\CollectionTranslation;
use App\Models\ContentPageTranslation;
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
            foreach ([
                'admin', 'account', 'login', 'register', 'forgot-password',
                'reset-password', 'verify-email', 'cart', 'checkout', 'wishlist',
                'orders', 'measurements', 'tailoring', 'tailor', 'search',
            ] as $path) {
                $lines[] = "Disallow: /{$locale}/{$path}";
            }
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function sitemapIndex(): Response
    {
        $xml = view('seo.sitemap-index', [
            'sitemaps' => [
                route('sitemaps.catalog'),
                route('sitemaps.content'),
            ],
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function catalogSitemap(): Response
    {
        $urls = [];

        foreach (config('kabulfit.supported_locales') as $locale) {
            $urls[] = route('home', ['locale' => $locale]);
            $urls[] = route('shop', ['locale' => $locale]);
        }

        CategoryTranslation::query()
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->orderBy('category_id')
            ->each(function (CategoryTranslation $translation) use (&$urls): void {
                $urls[] = route('categories.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);
            });

        CollectionTranslation::query()
            ->whereHas('collection', fn ($query) => $query->where('is_active', true))
            ->orderBy('collection_id')
            ->each(function (CollectionTranslation $translation) use (&$urls): void {
                $urls[] = route('collections.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);
            });

        ProductTranslation::query()
            ->whereHas('product', fn ($query) => $query->where('is_active', true))
            ->orderBy('product_id')
            ->each(function (ProductTranslation $translation) use (&$urls): void {
                $urls[] = route('products.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);
            });

        return $this->urlset($urls);
    }

    public function contentSitemap(): Response
    {
        $urls = [];

        foreach (config('kabulfit.supported_locales') as $locale) {
            $urls[] = route('blog.index', ['locale' => $locale]);
        }

        ContentPageTranslation::query()
            ->whereHas('page', fn ($query) => $query->where('is_published', true))
            ->orderBy('content_page_id')
            ->each(function (ContentPageTranslation $translation) use (&$urls): void {
                $urls[] = route('content.page', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);
            });

        BlogPostTranslation::query()
            ->whereHas('post', fn ($query) => $query
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->orderBy('blog_post_id')
            ->each(function (BlogPostTranslation $translation) use (&$urls): void {
                $urls[] = route('blog.show', [
                    'locale' => $translation->locale,
                    'slug' => $translation->slug,
                ]);
            });

        return $this->urlset($urls);
    }

    private function urlset(array $urls): Response
    {
        $xml = view('seo.sitemap', [
            'urls' => array_values(array_unique($urls)),
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
