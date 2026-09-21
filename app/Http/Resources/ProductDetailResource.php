<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;
        $locale = app()->getLocale();
        $translation = $product->translation($locale);

        return [
            'slug' => $translation?->slug,
            'sku' => $product->sku,
            'name' => $translation?->name,
            'short_description' => $translation?->short_description,
            'description' => $translation?->description,
            'url' => route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            'category' => [
                'slug' => $product->category?->translation($locale)?->slug,
                'name' => $product->category?->translation($locale)?->name,
            ],
            'collections' => $product->collections
                ->map(fn ($collection) => [
                    'slug' => $collection->translation($locale)?->slug,
                    'name' => $collection->translation($locale)?->name,
                ])
                ->values()
                ->all(),
            'price' => [
                'minor' => $product->currentPriceMinor(),
                'currency' => $product->currency,
                'formatted' => $product->formattedPrice(),
                'on_sale' => $product->sale_price_minor !== null,
            ],
            'availability' => [
                'in_stock' => $product->availableStock() > 0,
                'quantity' => $product->availableStock(),
            ],
            'media' => $product->media
                ->map(fn ($media) => [
                    'url' => $media->url(),
                    'alt' => $media->translation($locale)?->alt_text,
                    'width' => $media->width,
                    'height' => $media->height,
                    'primary' => $media->is_primary,
                ])
                ->values()
                ->all(),
            'variants' => $product->variants
                ->where('is_active', true)
                ->values()
                ->map(fn ($variant) => [
                    'sku' => $variant->sku,
                    'price' => [
                        'minor' => $variant->effectivePriceMinor(),
                        'currency' => $variant->currency,
                        'decimal' => Product::minorToDecimal($variant->effectivePriceMinor()),
                    ],
                    'availability' => [
                        'in_stock' => $variant->availableStock() > 0,
                        'quantity' => $variant->availableStock(),
                    ],
                    'options' => $variant->optionValues
                        ->sortBy(fn ($value) => $value->option->sort_order)
                        ->values()
                        ->map(fn ($value) => [
                            'option' => [
                                'code' => $value->option->code,
                                'name' => $value->option->translation($locale)?->name,
                            ],
                            'value' => [
                                'code' => $value->code,
                                'name' => $value->translation($locale)?->name,
                            ],
                        ])
                        ->all(),
                ])
                ->all(),
        ];
    }
}
