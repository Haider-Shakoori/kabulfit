<?php

namespace App\Support\Seo;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CatalogSchema
{
    public function listing(string $name, string $url, Collection $products, array $breadcrumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    '@id' => $url.'#collection',
                    'name' => $name,
                    'url' => $url,
                    'mainEntity' => ['@id' => $url.'#items'],
                ],
                [
                    '@type' => 'ItemList',
                    '@id' => $url.'#items',
                    'numberOfItems' => $products->count(),
                    'itemListElement' => $products->values()->map(
                        fn (Product $product, int $index) => [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'name' => $product->translation()?->name,
                            'url' => route('products.show', [
                                'locale' => app()->getLocale(),
                                'slug' => $product->translation()?->slug,
                            ]),
                        ],
                    )->all(),
                ],
                $this->breadcrumbs($breadcrumbs),
            ],
        ];
    }

    public function product(Product $product, string $locale): array
    {
        $translation = $product->translation($locale);
        $url = route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]);
        $categoryTranslation = $product->category?->translation($locale);
        $offers = $product->variants
            ->where('is_active', true)
            ->values()
            ->map(fn (ProductVariant $variant) => [
                '@type' => 'Offer',
                'sku' => $variant->sku,
                'priceCurrency' => $variant->currency,
                'price' => Product::minorToDecimal($variant->effectivePriceMinor()),
                'availability' => $variant->availableStock() > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'url' => $url,
            ])
            ->all();

        if ($offers === []) {
            $offers[] = [
                '@type' => 'Offer',
                'sku' => $product->sku,
                'priceCurrency' => $product->currency,
                'price' => $product->decimalPrice(),
                'availability' => $product->availableStock() > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'url' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Product',
                    '@id' => $url.'#product',
                    'name' => $translation?->name,
                    'description' => $translation?->short_description,
                    'sku' => $product->sku,
                    'image' => $product->media->map(fn ($media) => $media->url())->values()->all(),
                    'brand' => ['@type' => 'Brand', 'name' => 'KabulFit'],
                    'category' => $categoryTranslation?->name,
                    'offers' => $offers,
                ],
                $this->breadcrumbs([
                    ['name' => __('site.home'), 'url' => route('home', ['locale' => $locale])],
                    ['name' => __('site.shop'), 'url' => route('shop', ['locale' => $locale])],
                    [
                        'name' => $categoryTranslation?->name,
                        'url' => route('categories.show', [
                            'locale' => $locale,
                            'slug' => $categoryTranslation?->slug,
                        ]),
                    ],
                    ['name' => $translation?->name, 'url' => $url],
                ]),
            ],
        ];
    }

    private function breadcrumbs(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(
                fn (array $item, int $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ],
            )->all(),
        ];
    }
}
