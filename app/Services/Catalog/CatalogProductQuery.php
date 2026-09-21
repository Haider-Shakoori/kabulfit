<?php

namespace App\Services\Catalog;

use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Builder;

class CatalogProductQuery
{
    public function build(string $locale, array $filters = []): Builder
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with([
                'translations',
                'category.translations',
                'collections.translations',
                'media.translations',
                'variants' => fn ($variantQuery) => $variantQuery
                    ->where('is_active', true)
                    ->with([
                        'inventory',
                        'optionValues.translations',
                        'optionValues.option.translations',
                    ]),
            ]);

        $this->applySearch($query, $locale, $filters['q'] ?? null);
        $this->applyCategory($query, $locale, $filters['category'] ?? null);
        $this->applyCollection($query, $locale, $filters['collection'] ?? null);

        foreach (['size', 'color', 'embroidery'] as $optionCode) {
            $this->applyOption($query, $optionCode, $filters[$optionCode] ?? null);
        }

        if (($filters['in_stock'] ?? false) === true || ($filters['in_stock'] ?? null) === '1') {
            $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery
                ->where('is_active', true)
                ->whereHas('inventory', fn (Builder $stockQuery) => $stockQuery
                    ->whereColumn('inventory_stocks.quantity_on_hand', '>', 'inventory_stocks.reserved_quantity')));
        }

        if (isset($filters['price_min_minor'])) {
            $query->whereRaw('COALESCE(sale_price_minor, price_minor) >= ?', [(int) $filters['price_min_minor']]);
        }

        if (isset($filters['price_max_minor'])) {
            $query->whereRaw('COALESCE(sale_price_minor, price_minor) <= ?', [(int) $filters['price_max_minor']]);
        }

        return $this->applySort($query, $locale, $filters['sort'] ?? 'featured');
    }

    private function applySearch(Builder $query, string $locale, ?string $search): void
    {
        $search = trim((string) $search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $nested) use ($locale, $search): void {
            $nested
                ->where('sku', 'like', '%'.$search.'%')
                ->orWhereHas('translations', fn (Builder $translationQuery) => $translationQuery
                    ->where('locale', $locale)
                    ->where(function (Builder $textQuery) use ($search): void {
                        $textQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('short_description', 'like', '%'.$search.'%')
                            ->orWhere('description', 'like', '%'.$search.'%');
                    }));
        });
    }

    private function applyCategory(Builder $query, string $locale, ?string $slug): void
    {
        if (! $slug) {
            return;
        }

        $query->whereHas('category.translations', fn (Builder $categoryQuery) => $categoryQuery
            ->where('locale', $locale)
            ->where('slug', $slug));
    }

    private function applyCollection(Builder $query, string $locale, ?string $slug): void
    {
        if (! $slug) {
            return;
        }

        $query->whereHas('collections.translations', fn (Builder $collectionQuery) => $collectionQuery
            ->where('locale', $locale)
            ->where('slug', $slug));
    }

    private function applyOption(Builder $query, string $optionCode, ?string $valueCode): void
    {
        if (! $valueCode) {
            return;
        }

        $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery
            ->where('is_active', true)
            ->whereHas('optionValues', fn (Builder $valueQuery) => $valueQuery
                ->where('code', $valueCode)
                ->whereHas('option', fn (Builder $optionQuery) => $optionQuery->where('code', $optionCode))));
    }

    private function applySort(Builder $query, string $locale, string $sort): Builder
    {
        return match ($sort) {
            'newest' => $query->orderByDesc('created_at')->orderBy('sort_order'),
            'price_asc' => $query->orderByRaw('COALESCE(sale_price_minor, price_minor) asc')->orderBy('sort_order'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price_minor, price_minor) desc')->orderBy('sort_order'),
            'name' => $query->orderBy(
                ProductTranslation::query()
                    ->select('name')
                    ->whereColumn('product_translations.product_id', 'products.id')
                    ->where('locale', $locale)
                    ->limit(1),
            ),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id'),
        };
    }
}
