<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translation();
        $categoryTranslation = $this->category?->translation();
        $media = $this->primaryMedia;
        $mediaTranslation = $media?->translation();

        return [
            'slug' => $translation?->slug,
            'sku' => $this->sku,
            'name' => $translation?->name,
            'short_description' => $translation?->short_description,
            'category' => $categoryTranslation ? [
                'slug' => $categoryTranslation->slug,
                'name' => $categoryTranslation->name,
            ] : null,
            'price' => [
                'minor' => $this->currentPriceMinor(),
                'currency' => $this->currency,
                'formatted' => $this->formattedPrice(),
            ],
            'stock' => [
                'available' => $this->availableStock(),
                'in_stock' => $this->isInStock(),
            ],
            'primary_media' => $media ? [
                'url' => $media->url(),
                'alt' => $mediaTranslation?->alt_text,
                'width' => $media->width,
                'height' => $media->height,
                'sources' => $media->responsiveSources(),
            ] : null,
            'featured' => $this->is_featured,
            'tailoring_enabled' => $this->tailoring_enabled,
        ];
    }
}
