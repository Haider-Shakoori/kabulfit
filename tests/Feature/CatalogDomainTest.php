<?php

namespace Tests\Feature;

use App\Models\CatalogCollection;
use App\Models\Product;
use App\Models\ProductOption;
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

    public function test_catalog_seed_contains_normalized_collections_options_variants_media_and_inventory(): void
    {
        $this->assertSame(3, CatalogCollection::query()->count());
        $this->assertSame(3, ProductOption::query()->count());

        $product = Product::query()
            ->where('sku', 'KF-W-DR-001')
            ->with([
                'collections.translations',
                'media.translations',
                'variants.inventory',
                'variants.optionValues.option',
            ])
            ->firstOrFail();

        $this->assertCount(3, $product->variants);
        $this->assertNotNull($product->primaryMedia());
        $this->assertSame(11, $product->availableStock());
        $this->assertTrue($product->collections->isNotEmpty());
        $this->assertTrue($product->variants->every(fn ($variant) => $variant->optionValues->count() === 3));
    }

    public function test_shop_search_filter_and_sort_use_server_side_catalog_query(): void
    {
        $this->get('/en/shop?q=embroidered&color=maroon&in_stock=1&sort=price_desc')
            ->assertOk()
            ->assertSee('Hand-Embroidered Afghan Dress')
            ->assertDontSee('Classic Afghan Perahan Tunban')
            ->assertSee('<meta name="robots" content="noindex,follow">', false);
    }

    public function test_collection_page_is_clean_indexable_and_has_item_list_schema(): void
    {
        $html = $this->get('/en/collections/wedding')
            ->assertOk()
            ->assertSee('Wedding Edit')
            ->getContent();

        $this->assertStringContainsString('<meta name="robots" content="index,follow">', $html);
        $this->assertStringContainsString('"@type":"ItemList"', $html);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
    }

    public function test_product_page_contains_variant_offers_breadcrumb_schema_and_media(): void
    {
        $html = $this->get('/en/products/hand-embroidered-afghan-dress')
            ->assertOk()
            ->assertSee('KF-W-DR-001-S-MAR-H')
            ->assertSee('Available variants')
            ->getContent();

        $this->assertStringContainsString('"@type":"Product"', $html);
        $this->assertStringContainsString('"@type":"Offer"', $html);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
        $this->assertStringContainsString('kabulfit-craftsmanship.svg', $html);
    }

    public function test_recently_viewed_products_are_session_based_and_do_not_expose_database_ids(): void
    {
        $this->get('/en/products/classic-afghan-perahan-tunban')->assertOk();

        $this->get('/en/products/hand-embroidered-afghan-dress')
            ->assertOk()
            ->assertSee('Recently viewed')
            ->assertSee('Classic Afghan Perahan Tunban');
    }

    public function test_catalog_api_returns_localized_slug_money_stock_media_and_options_without_internal_ids(): void
    {
        $response = $this->getJson('/api/v1/en/catalog/products?category=women&color=maroon');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'hand-embroidered-afghan-dress')
            ->assertJsonPath('data.0.price.minor', 980000)
            ->assertJsonPath('data.0.price.currency', 'AFN')
            ->assertJsonPath('data.0.availability.quantity', 11)
            ->assertJsonPath('data.0.options.color.0.code', 'maroon');

        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        $this->assertArrayNotHasKey('product_id', $response->json('data.0'));
    }

    public function test_catalog_api_facets_are_localized_and_machine_stable(): void
    {
        $response = $this->getJson('/api/v1/fa/catalog/facets');

        $response
            ->assertOk()
            ->assertJsonPath('data.categories.0.slug', 'مردانه')
            ->assertJsonPath('data.options.0.code', 'size')
            ->assertJsonPath('data.options.1.code', 'color')
            ->assertJsonPath('data.options.2.code', 'embroidery')
            ->assertJsonPath('data.sorts.0', 'featured');
    }

    public function test_catalog_api_detail_returns_variants_and_related_products(): void
    {
        $response = $this->getJson('/api/v1/en/catalog/products/hand-embroidered-afghan-dress');

        $response
            ->assertOk()
            ->assertJsonPath('data.sku', 'KF-W-DR-001')
            ->assertJsonCount(3, 'data.variants')
            ->assertJsonPath('data.variants.0.options.0.option.code', 'size')
            ->assertJsonStructure([
                'data' => ['media', 'variants', 'collections'],
                'related',
            ]);
    }

    public function test_sitemap_includes_collections_and_robots_excludes_api_crawling(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/en/collections/new-arrivals', false)
            ->assertSee('/fa/collections/', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /api/', false);
    }
}
