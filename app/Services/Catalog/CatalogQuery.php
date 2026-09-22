<?php

namespace App\Services\Catalog;

use App\Http\Requests\CatalogFilterRequest;
use App\Models\Product;
use App\Support\Money\MinorMoney;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CatalogQuery
{
    public const SORTS = ['featured', 'newest', 'price_asc', 'price_desc'];

    public function paginate(
        CatalogFilterRequest $request,
        string $locale,
        ?string $categorySlug = null,
        ?string $collectionSlug = null,
    ): LengthAwarePaginator {
        $filters = $request->validated();

        $query = Product::query()
            ->where('is_active', true)
            ->with(self::cardEagerLoads());

        $categorySlug ??= $filters['category'] ?? null;
        $collectionSlug ??= $filters['collection'] ?? null;

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->whereHas('translations', function (Builder $translationQuery) use ($locale, $search): void {
                $translationQuery
                    ->where('locale', $locale)
                    ->where(function (Builder $textQuery) use ($search): void {
                        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                        $textQuery
                            ->where('name', 'like', $like)
                            ->orWhere('short_description', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        if ($categorySlug) {
            $query->whereHas('category.translations', fn (Builder $categoryQuery) => $categoryQuery
                ->where('locale', $locale)
                ->where('slug', $categorySlug));
        }

        if ($collectionSlug) {
            $query->whereHas('collections.translations', fn (Builder $collectionQuery) => $collectionQuery
                ->where('locale', $locale)
                ->where('slug', $collectionSlug));
        }

        if ($size = $filters['size'] ?? null) {
            $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery
                ->where('is_active', true)
                ->whereHas('size', fn (Builder $sizeQuery) => $sizeQuery
                    ->where('is_active', true)
                    ->where('code', strtoupper($size))));
        }

        if ($color = $filters['color'] ?? null) {
            $query->whereHas('variants', fn (Builder $variantQuery) => $variantQuery
                ->where('is_active', true)
                ->whereHas('color', fn (Builder $colorQuery) => $colorQuery
                    ->where('is_active', true)
                    ->whereHas('translations', fn (Builder $translationQuery) => $translationQuery
                        ->where('locale', $locale)
                        ->where('slug', $color))));
        }

        if (isset($filters['min_price'])) {
            $query->whereRaw('COALESCE(sale_price_minor, price_minor) >= ?', [
                MinorMoney::fromDecimalString($filters['min_price']),
            ]);
        }

        if (isset($filters['max_price'])) {
            $query->whereRaw('COALESCE(sale_price_minor, price_minor) <= ?', [
                MinorMoney::fromDecimalString($filters['max_price']),
            ]);
        }

        if ($request->boolean('in_stock')) {
            $query->where(function (Builder $stockQuery): void {
                $stockQuery
                    ->whereHas('variants', fn (Builder $variantQuery) => $variantQuery
                        ->where('is_active', true)
                        ->whereHas('inventory', fn (Builder $inventoryQuery) => $inventoryQuery
                            ->whereColumn('quantity_on_hand', '>', 'quantity_reserved')))
                    ->orWhere(function (Builder $legacyQuery): void {
                        $legacyQuery
                            ->whereDoesntHave('variants')
                            ->where('stock_quantity', '>', 0);
                    });
            });
        }

        $this->applySort($query, $filters['sort'] ?? 'featured');

        return $query
            ->paginate((int) ($filters['per_page'] ?? config('kabulfit.catalog.per_page', 12)))
            ->withQueryString();
    }

    public static function cardEagerLoads(): array
    {
        return [
            'translations',
            'category.translations',
            'primaryMedia.translations',
            'primaryMedia.derivatives',
            'variants' => fn ($query) => $query
                ->where('is_active', true)
                ->with(['size', 'color.translations', 'inventory'])
                ->orderBy('sort_order'),
        ];
    }

    public static function detailEagerLoads(): array
    {
        return [
            ...self::cardEagerLoads(),
            'media.translations',
            'media.derivatives',
            'collections.translations',
        ];
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'newest' => $query->orderByDesc('id'),
            'price_asc' => $query->orderByRaw('COALESCE(sale_price_minor, price_minor) ASC')->orderBy('sort_order'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price_minor, price_minor) DESC')->orderBy('sort_order'),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id'),
        };
    }
}
