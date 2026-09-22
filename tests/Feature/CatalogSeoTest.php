<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_catalog_listing_pages_emit_item_list_and_breadcrumb_schema(): void
    {
        $shop = $this->get('/en/shop')->assertOk()->getContent();
        $category = $this->get('/en/categories/men')->assertOk()->getContent();
        $collection = $this->get('/en/collections/wedding-edit')->assertOk()->getContent();

        foreach ([$shop, $category, $collection] as $html) {
            $this->assertStringContainsString('"@type":"ItemList"', $html);
            $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
        }
    }

    public function test_sitemap_includes_localized_collections_and_robots_blocks_api_crawling(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/sitemaps/catalog.xml', false);

        $this->get('/sitemaps/catalog.xml')
            ->assertOk()
            ->assertSee('/en/collections/wedding-edit', false)
            ->assertSee('/fa/collections/', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /api/', false);
    }
}
