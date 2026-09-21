<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_catalog_api_returns_localized_slug_sku_money_stock_and_filter_metadata(): void
    {
        $response = $this->getJson('/api/v1/en/catalog?category=women');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.sku', 'KF-W-DR-001')
            ->assertJsonPath('data.0.slug', 'herat-embroidered-afghan-dress')
            ->assertJsonPath('data.0.price.minor', 980000)
            ->assertJsonPath('data.0.price.currency', 'AFN')
            ->assertJsonPath('data.0.stock.in_stock', true)
            ->assertJsonPath('filters.sorts.0', 'featured');

        $this->assertArrayNotHasKey('id', $response->json('data.0'));
    }

    public function test_product_api_exposes_variant_skus_without_database_ids(): void
    {
        $slug = Product::query()
            ->with('translations')
            ->where('sku', 'KF-M-PT-001')
            ->firstOrFail()
            ->translation('en')
            ->slug;

        $response = $this->getJson('/api/v1/en/products/'.$slug)
            ->assertOk()
            ->assertJsonPath('data.sku', 'KF-M-PT-001')
            ->assertJsonPath('data.variants.0.sku', 'KF-M-PT-001-M-BLACK')
            ->assertJsonPath('data.variants.0.stock.available', 5);

        $this->assertArrayNotHasKey('id', $response->json('data'));
        $this->assertArrayNotHasKey('id', $response->json('data.variants.0'));
    }

    public function test_catalog_api_localizes_categories_and_collections(): void
    {
        $this->getJson('/api/v1/fa/categories')
            ->assertOk()
            ->assertJsonFragment(['name' => 'مردانه']);

        $this->getJson('/api/v1/ps/collections')
            ->assertOk()
            ->assertJsonFragment(['name' => 'نوي راغلي']);
    }

    public function test_catalog_api_rejects_invalid_filter_contract(): void
    {
        $this->getJson('/api/v1/en/catalog?min_price=not-money&per_page=99')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['min_price', 'per_page']);
    }
}
