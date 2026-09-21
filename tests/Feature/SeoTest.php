<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_has_core_seo_metadata_and_single_h1(): void
    {
        $response = $this->get('/en')->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('<title>Authentic Afghan Clothes &amp; Custom Tailoring | KabulFit</title>', $html);
        $this->assertStringContainsString('<meta name="description"', $html);
        $this->assertStringContainsString('<link rel="canonical" href="http://localhost/en">', $html);
        $this->assertStringContainsString('hreflang="fa"', $html);
        $this->assertStringContainsString('hreflang="ps"', $html);
        $this->assertStringContainsString('hreflang="x-default"', $html);
        $this->assertStringContainsString('<meta property="og:title"', $html);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $html);
        $this->assertSame(1, preg_match_all('/<h1(?:\s[^>]*)?>/i', $html));
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_product_has_localized_canonical_hreflang_and_product_schema(): void
    {
        $product = Product::query()->with('translations')->where('sku', 'KF-M-PT-001')->firstOrFail();
        $slug = $product->translations->firstWhere('locale', 'en')->slug;

        $html = $this->get('/en/products/'.$slug)->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="http://localhost/en/products/'.$slug.'">', $html);
        $this->assertStringContainsString('hreflang="fa"', $html);
        $this->assertStringContainsString('"@type":"Product"', $html);
        $this->assertStringContainsString('"priceCurrency":"AFN"', $html);
        $this->assertStringContainsString('"price":"6500.00"', $html);
        $this->assertSame(1, preg_match_all('/<h1(?:\s[^>]*)?>/i', $html));
    }

    public function test_robots_blocks_private_transactional_areas_without_blocking_catalog(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Disallow: /en/cart', $robots);
        $this->assertStringContainsString('Disallow: /fa/checkout', $robots);
        $this->assertStringNotContainsString('Disallow: /en/products', $robots);
        $this->assertStringContainsString('Sitemap: http://localhost/sitemap.xml', $robots);
    }

    public function test_sitemap_contains_only_public_foundation_urls(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('http://localhost/en/shop', $xml);
        $this->assertStringContainsString('http://localhost/fa/categories/', $xml);
        $this->assertStringContainsString('http://localhost/ps/products/', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
        $this->assertStringNotContainsString('/checkout', $xml);
    }
}
