<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSiteAssetAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_uses_local_live_kabulfit_brand_and_section_assets(): void
    {
        $response = $this->get('/en')->assertOk();

        $response
            ->assertSee('images/kabulfit-live/logo-header.png', false)
            ->assertSee('images/kabulfit-live/logo-footer.png', false)
            ->assertSee('images/kabulfit-live/hero-heritage.png', false)
            ->assertSee('images/kabulfit-live/craftsmanship.jpg', false)
            ->assertSee('images/kabulfit-live/measurement-guide.png', false)
            ->assertSee('Handcrafted')
            ->assertSee('Free Shipping', false);

        $html = $response->getContent();

        $this->assertStringNotContainsString('kabulfit-hero-textile.svg', $html);
        $this->assertStringNotContainsString('kabulfit-craftsmanship.svg', $html);
        $this->assertStringNotContainsString('supabase.co', $html);
        $this->assertStringNotContainsString('media.base44.com', $html);
    }

    public function test_seeded_catalog_uses_local_non_placeholder_media(): void
    {
        $products = Product::query()
            ->with('primaryMedia')
            ->whereIn('sku', ['KF-M-PT-001', 'KF-W-DR-001', 'KF-K-VS-001', 'KF-M-WC-001', 'KF-A-KS-001'])
            ->get();

        $this->assertCount(5, $products);

        foreach ($products as $product) {
            $this->assertNotNull($product->primaryMedia, $product->sku.' is missing primary media');
            $this->assertStringStartsWith('images/kabulfit-live/catalog/', $product->primaryMedia->path);
            $this->assertStringNotContainsString('placeholder', $product->primaryMedia->path);
            $this->assertNotSame('image/svg+xml', $product->primaryMedia->mime_type);
            $this->assertFileExists(public_path($product->primaryMedia->path));
        }
    }
}
