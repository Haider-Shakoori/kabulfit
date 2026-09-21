<?php

namespace App\Http\Resources;

use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $translation = $this->translation($locale);
        $media = $this->primaryMedia();

        return [
            'slug' => $translation?->slug,
            'sku' => $this->sku,
            'name' => $translation?->name,
            'short_description' => $translation?->short_description,
            'url' => route('products.show', ['locale' => $locale, 'slug' => $translation?->slug]),
            'category' => [
                'slug' => $this->category?->translation($locale)?->slug,
                'name' => $this->category?->translation($locale)?->name,
            ],
            'price' => [
                'minor' => $this->currentPriceMinor(),
                'currency' => $this->currency,
                'formatted' => $this->formattedPrice(),
                'on_sale' => $this->sale_price_minor !== null,
            ],
            'availability' => [
                'in_stock' => $this->availableStock() > 0,
                'quantity' => $this->availableStock(),
            ],
            'primary_media' => $media ? [
                'url' => $media->url(),
                'alt' => $media->translation($locale)?->alt_text,
                'width' => $media->width,
                'height' => $media->height,
            ] : null,
            'options' => $this->optionSummary($locale),
            'featured' => $this->is_featured,
        ];
    }

    private function optionSummary(string $locale): array
    {
        if (! $this->relationLoaded('variants')) {
            return [];
        }

        return $this->variants
            ->flatMap(fn ($variant) => $variant->optionValues)
            ->unique('id')
            ->groupBy(fn (ProductOptionValue $value) => $value->option->code)
            ->map(fn ($values) => $values
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ProductOptionValue $value) => [
                    'code' => $value->code,
                    'name' => $value->translation($locale)?->name,
                ])
                ->all())
            ->all();
    }
}
