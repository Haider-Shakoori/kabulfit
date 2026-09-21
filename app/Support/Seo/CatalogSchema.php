<?php

namespace App\Support\Seo;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CatalogSchema
{
    public static function listing(
        LengthAwarePaginator $products,
        string $name,
        string $url,
        array $breadcrumbs,
        string $locale,
    ): array {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::breadcrumbList($breadcrumbs),
                [
                    '@type' => 'ItemList',
                    'name' => $name,
                    'url' => $url,
                    'itemListElement' => collect($products->items())
                        ->values()
                        ->map(function (Product $product, int $index) use ($locale, $products): array {
                            $translation = $product->translation($locale);

                            return [
                                '@type' => 'ListItem',
                                'position' => (($products->currentPage() - 1) * $products->perPage()) + $index + 1,
                                'url' => route('products.show', [
                                    'locale' => $locale,
                                    'slug' => $translation?->slug,
                                ]),
                                'name' => $translation?->name,
                            ];
                        })
                        ->all(),
                ],
            ],
        ];
    }

    public static function product(Product $product, string $locale, string $url, array $breadcrumbs): array
    {
        $translation = $product->translation($locale);
        $offers = $product->variants
            ->where('is_active', true)
            ->map(function ($variant) use ($product, $url): array {
                $priceMinor = $variant->currentPriceMinor();

                return [
                    '@type' => 'Offer',
                    'sku' => $variant->sku,
                    'priceCurrency' => $product->currency,
                    'price' => $product->decimalPrice($priceMinor),
                    'availability' => $variant->availableQuantity() > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'url' => $url,
                ];
            })
            ->values()
            ->all();

        if ($offers === []) {
            $offers[] = [
                '@type' => 'Offer',
                'sku' => $product->sku,
                'priceCurrency' => $product->currency,
                'price' => $product->decimalPrice(),
                'availability' => $product->isInStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'url' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::breadcrumbList($breadcrumbs),
                [
                    '@type' => 'Product',
                    'name' => $translation?->name,
                    'description' => $translation?->short_description,
                    'sku' => $product->sku,
                    'brand' => ['@type' => 'Brand', 'name' => 'KabulFit'],
                    'image' => $product->media
                        ->map(fn ($media) => $media->url())
                        ->values()
                        ->all(),
                    'offers' => $offers,
                ],
            ],
        ];
    }

    private static function breadcrumbList(array $breadcrumbs): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)
                ->values()
                ->map(fn (array $item, int $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ])
                ->all(),
        ];
    }
}
