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

    public function test_homepage_uses_base44_reference_media_and_local_kabulfit_brand_assets(): void
    {
        $response = $this->get('/en')->assertOk();

        $response
            ->assertSee('images/kabulfit-optimized/logo-header.webp', false)
            ->assertSee('images/kabulfit-optimized/logo-footer.webp', false)
            ->assertSee('6b7f27f2f_H1.webp', false)
            ->assertSee('f2989fdbd_H2.webp', false)
            ->assertSee('b6b455e59_H3.webp', false)
            ->assertSee('images/kabulfit-optimized/story-bg.webp', false)
            ->assertSee('images/kabulfit-optimized/measurement.webp', false)
            ->assertSee('images/kabulfit-optimized/craftsmanship.webp', false)
            ->assertSee('Handcrafted')
            ->assertSee('Free Shipping', false);

        $html = $response->getContent();

        foreach (['6b7f27f2f_H1.webp', 'f2989fdbd_H2.webp', 'b6b455e59_H3.webp'] as $heroImage) {
            $path = public_path('images/kabulfit-base44/source/'.$heroImage);

            $this->assertFileExists($path);
            $this->assertGreaterThan(50_000, filesize($path), $heroImage.' is unexpectedly small.');
            $this->assertSame('image/webp', mime_content_type($path));
        }

        $this->assertLessThan(
            strpos($html, 'f2989fdbd_H2.webp'),
            strpos($html, '6b7f27f2f_H1.webp'),
            'Hero H1 must render before H2.',
        );
        $this->assertLessThan(
            strpos($html, 'b6b455e59_H3.webp'),
            strpos($html, 'f2989fdbd_H2.webp'),
            'Hero H2 must render before H3.',
        );

        $this->assertStringContainsString('Authentic Afghan Elegance', $html);
        $this->assertStringContainsString('Traditional Elegance', $html);
        $this->assertStringContainsString('Custom Fit Guarantee', $html);
        $this->assertStringContainsString('base44-hero-image', $html);
        $this->assertStringContainsString('data-section="hero-bottom-fade"', $html);
        $this->assertSame(1, substr_count($html, 'data-section="hero-bottom-fade"'));
        $this->assertStringContainsString('loading="eager"', $html);
        $this->assertStringContainsString('fetchpriority="high"', $html);
        $this->assertStringContainsString('hero-h1-mobile.webp', $html);
        $this->assertStringContainsString('/en/measurement-guide', $html);
        $this->assertStringContainsString('h-24 bg-gradient-to-t from-[#FDFBF7] to-transparent', $html);
        $this->assertStringNotContainsString('kabulfit-hero-textile.svg', $html);
        $this->assertStringNotContainsString('kabulfit-craftsmanship.svg', $html);
    }

    public function test_global_image_css_does_not_override_tailwind_height_utilities(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringNotContainsString('img { height: auto; }', $css);
        $this->assertStringContainsString('img, svg { display: block; max-width: 100%; }', $css);
    }

    public function test_hero_css_cannot_be_collapsed_by_generic_responsive_image_rules(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringNotContainsString('img { height: auto; }', $css);
        $this->assertStringContainsString('.base44-hero-image', $css);
        $this->assertStringContainsString('height: 100% !important;', $css);
        $this->assertStringContainsString('object-fit: cover !important;', $css);
        $this->assertStringContainsString('object-position: center 50% !important;', $css);
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
