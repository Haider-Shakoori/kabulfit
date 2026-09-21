<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogFilterRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductSummaryResource;
use App\Models\CatalogCollection;
use App\Models\Category;
use App\Models\ProductOption;
use App\Services\Catalog\CatalogProductQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogApiController extends Controller
{
    public function index(CatalogFilterRequest $request, string $locale, CatalogProductQuery $catalog): AnonymousResourceCollection
    {
        $filters = $request->catalogFilters(api: true);
        $products = $catalog
            ->build($locale, $filters)
            ->paginate((int) ($filters['per_page'] ?? 12))
            ->withQueryString();

        return ProductSummaryResource::collection($products);
    }

    public function show(string $locale, string $slug, CatalogProductQuery $catalog): JsonResponse
    {
        $product = $catalog
            ->build($locale)
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->firstOrFail();

        $related = $catalog
            ->build($locale)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit(4)
            ->get();

        return response()->json([
            'data' => (new ProductDetailResource($product))->resolve(),
            'related' => ProductSummaryResource::collection($related)->resolve(),
        ]);
    }

    public function facets(string $locale): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('translations')
            ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category) => [
                'slug' => $category->translation($locale)?->slug,
                'name' => $category->translation($locale)?->name,
                'product_count' => $category->products_count,
            ]);

        $collections = CatalogCollection::query()
            ->where('is_active', true)
            ->with('translations')
            ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CatalogCollection $collection) => [
                'slug' => $collection->translation($locale)?->slug,
                'name' => $collection->translation($locale)?->name,
                'product_count' => $collection->products_count,
            ]);

        $options = ProductOption::query()
            ->where('is_filterable', true)
            ->with(['translations', 'values.translations'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductOption $option) => [
                'code' => $option->code,
                'name' => $option->translation($locale)?->name,
                'values' => $option->values
                    ->map(fn ($value) => [
                        'code' => $value->code,
                        'name' => $value->translation($locale)?->name,
                    ])
                    ->values(),
            ]);

        return response()->json([
            'data' => [
                'categories' => $categories->values(),
                'collections' => $collections->values(),
                'options' => $options->values(),
                'sorts' => ['featured', 'newest', 'price_asc', 'price_desc', 'name'],
            ],
        ]);
    }
}
