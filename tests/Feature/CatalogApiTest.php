<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\CatalogSeeder;
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
        $response = $this->getJson('/api/v1/en/catalog?category=women&per_page=48')
            ->assertOk()
            ->assertJsonPath('filters.sorts.0', 'featured');

        $product = collect($response->json('data'))->firstWhere('sku', 'KF-W-DR-001');

        $this->assertNotNull($product);
        $this->assertSame('herat-embroidered-afghan-dress', $product['slug']);
        $this->assertSame(9800, $product['price']['minor']);
        $this->assertSame('USD', $product['price']['currency']);
        $this->assertTrue($product['stock']['in_stock']);
        $this->assertArrayNotHasKey('id', $product);
    }

    public function test_approved_product_prices_are_seeded_in_us_dollars(): void
    {
        foreach ([
            'KF-M-PT-001' => 6500,
            'KF-W-DR-001' => 9800,
            'KF-K-VS-001' => 4200,
            'KF-M-WC-001' => 7900,
            'KF-A-KS-001' => 4800,
        ] as $sku => $priceMinor) {
            $this->assertDatabaseHas('products', [
                'sku' => $sku,
                'price_minor' => $priceMinor,
                'currency' => 'USD',
            ]);
        }

        $this->assertSame('$65.00', Product::query()->where('sku', 'KF-M-PT-001')->firstOrFail()->formattedPrice());
    }

    public function test_reseeding_catalog_does_not_overwrite_website_managed_products_and_categories(): void
    {
        $product = Product::query()->where('sku', 'KF-M-PT-001')->firstOrFail();
        $product->update(['price_minor' => 7300]);
        $product->category->translations()->where('locale', 'en')->update(['name' => 'Website Edited Category']);

        $this->seed(CatalogSeeder::class);

        $this->assertDatabaseHas('products', [
            'sku' => 'KF-M-PT-001',
            'price_minor' => 7300,
            'currency' => 'USD',
        ]);
        $this->assertSame(
            'Website Edited Category',
            $product->category->fresh('translations')->translation('en')->name,
        );
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
