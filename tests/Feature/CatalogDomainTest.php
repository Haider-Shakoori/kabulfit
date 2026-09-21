<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Services\Catalog\CatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_seeded_catalog_has_relational_variants_inventory_media_and_collections(): void
    {
        $product = Product::query()
            ->where('sku', 'KF-M-PT-001')
            ->with(CatalogQuery::detailEagerLoads())
            ->firstOrFail();

        $this->assertCount(4, $product->variants);
        $this->assertSame(14, $product->availableStock());
        $this->assertNotNull($product->primaryMedia);
        $this->assertSame('image/svg+xml', $product->primaryMedia->mime_type);
        $this->assertGreaterThanOrEqual(1, $product->collections->count());
        $this->assertGreaterThanOrEqual(1, $product->relatedProducts()->count());
    }

    public function test_inventory_available_quantity_subtracts_reserved_units(): void
    {
        $product = Product::query()
            ->where('sku', 'KF-M-PT-001')
            ->with(CatalogQuery::detailEagerLoads())
            ->firstOrFail();

        $variant = $product->variants->firstWhere('sku', 'KF-M-PT-001-M-BLACK');

        $this->assertNotNull($variant);
        $this->assertSame(6, $variant->inventory->quantity_on_hand);
        $this->assertSame(1, $variant->inventory->quantity_reserved);
        $this->assertSame(5, $variant->availableQuantity());
    }

    public function test_collections_use_localized_clean_slugs(): void
    {
        $collection = Collection::query()
            ->with('translations')
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', 'en')
                ->where('slug', 'wedding-edit'))
            ->firstOrFail();

        $this->assertSame('Wedding Edit', $collection->translation('en')->name);
        $this->assertSame('انتخاب-عروسی', $collection->translation('fa')->slug);
    }
}
