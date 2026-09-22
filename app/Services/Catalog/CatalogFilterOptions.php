<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Cache;

class CatalogFilterOptions
{
    public function forWeb(string $locale): array
    {
        return Cache::remember(
            "catalog:filters:web:{$locale}:v1",
            now()->addMinutes((int) config('kabulfit.catalog.filter_cache_minutes', 10)),
            fn () => [
                'categories' => Category::query()
                    ->where('is_active', true)
                    ->with('translations')
                    ->orderBy('sort_order')
                    ->get(),
                'collections' => Collection::query()
                    ->where('is_active', true)
                    ->with('translations')
                    ->orderBy('sort_order')
                    ->get(),
                'sizes' => Size::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(),
                'colors' => Color::query()
                    ->where('is_active', true)
                    ->with('translations')
                    ->orderBy('sort_order')
                    ->get(),
            ],
        );
    }

    public function forApi(string $locale): array
    {
        return Cache::remember(
            "catalog:filters:api:{$locale}:v1",
            now()->addMinutes((int) config('kabulfit.catalog.filter_cache_minutes', 10)),
            function () use ($locale): array {
                return [
                    'sizes' => Size::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->pluck('code')
                        ->all(),
                    'colors' => Color::query()
                        ->where('is_active', true)
                        ->with('translations')
                        ->orderBy('sort_order')
                        ->get()
                        ->map(function (Color $color) use ($locale): array {
                            $translation = $color->translations->firstWhere('locale', $locale)
                                ?? $color->translations->firstWhere('locale', config('kabulfit.default_locale'));

                            return [
                                'code' => $color->code,
                                'slug' => $translation?->slug,
                                'name' => $translation?->name,
                                'hex' => $color->hex_value,
                            ];
                        })
                        ->values()
                        ->all(),
                    'sorts' => CatalogQuery::SORTS,
                ];
            },
        );
    }
}
