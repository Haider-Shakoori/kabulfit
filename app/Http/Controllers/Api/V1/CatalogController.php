<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogFilterRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductSummaryResource;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Services\Catalog\CatalogQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function index(
        CatalogFilterRequest $request,
        string $locale,
        CatalogQuery $catalog,
    ): AnonymousResourceCollection {
        $products = $catalog->paginate($request, $locale);

        return ProductSummaryResource::collection($products)->additional([
            'filters' => $this->filterMetadata(),
        ]);
    }

    public function product(string $locale, string $slug): ProductDetailResource
    {
        $product = Product::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with(CatalogQuery::detailEagerLoads())
            ->firstOrFail();

        return new ProductDetailResource($product);
    }

    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function collections(): AnonymousResourceCollection
    {
        $collections = Collection::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return CollectionResource::collection($collections);
    }

    private function filterMetadata(): array
    {
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
                ->map(fn (Color $color) => [
                    'code' => $color->code,
                    'slug' => $color->translation()?->slug,
                    'name' => $color->translation()?->name,
                    'hex' => $color->hex_value,
                ])
                ->values()
                ->all(),
            'sorts' => CatalogQuery::SORTS,
        ];
    }
}
