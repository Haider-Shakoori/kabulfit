<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductDetailResource extends ProductSummaryResource
{
    public function toArray(Request $request): array
    {
        $base = parent::toArray($request);
        $translation = $this->translation();

        return [
            ...$base,
            'description' => $translation?->description,
            'collections' => $this->collections
                ->map(function ($collection): array {
                    $translation = $collection->translation();

                    return [
                        'slug' => $translation?->slug,
                        'name' => $translation?->name,
                    ];
                })
                ->values()
                ->all(),
            'media' => $this->media
                ->map(fn ($media) => [
                    'url' => $media->url(),
                    'alt' => $media->translation()?->alt_text,
                    'width' => $media->width,
                    'height' => $media->height,
                    'primary' => $media->is_primary,
                ])
                ->values()
                ->all(),
            'variants' => $this->variants
                ->where('is_active', true)
                ->map(fn ($variant) => [
                    'sku' => $variant->sku,
                    'size' => $variant->size?->code,
                    'color' => $variant->color ? [
                        'code' => $variant->color->code,
                        'slug' => $variant->color->translation()?->slug,
                        'name' => $variant->color->translation()?->name,
                        'hex' => $variant->color->hex_value,
                    ] : null,
                    'price' => [
                        'minor' => $variant->currentPriceMinor(),
                        'currency' => $this->currency,
                        'formatted' => $this->formattedPrice($variant->currentPriceMinor()),
                    ],
                    'stock' => [
                        'available' => $variant->availableQuantity(),
                        'in_stock' => $variant->availableQuantity() > 0,
                    ],
                ])
                ->values()
                ->all(),
        ];
    }
}
