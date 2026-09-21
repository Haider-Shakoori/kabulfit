<?php

namespace App\Services\Catalog;

use App\Models\Product;
use Illuminate\Support\Collection;

class RecentlyViewedProducts
{
    private const SESSION_KEY = 'catalog.recently_viewed';

    public function remember(Product $product): void
    {
        $ids = collect(session()->get(self::SESSION_KEY, []))
            ->reject(fn ($id) => (int) $id === $product->id)
            ->prepend($product->id)
            ->take(10)
            ->values()
            ->all();

        session()->put(self::SESSION_KEY, $ids);
    }

    public function excluding(Product $product, int $limit = 4): Collection
    {
        $ids = collect(session()->get(self::SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn (int $id) => $id === $product->id)
            ->take($limit)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $order = $ids->flip();

        return Product::query()
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->with(CatalogQuery::cardEagerLoads())
            ->get()
            ->sortBy(fn (Product $item) => $order->get($item->id, PHP_INT_MAX))
            ->values();
    }
}
