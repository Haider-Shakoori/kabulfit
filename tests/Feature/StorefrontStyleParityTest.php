<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontStyleParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_tailwind_text_and_font_utilities_are_not_overridden_by_unlayered_element_resets(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('@layer base {', $css);
        $this->assertStringContainsString('a { color: inherit; text-decoration: none; }', $css);
        $this->assertStringContainsString('button, input, select, textarea { font: inherit; }', $css);

        $baseStart = strpos($css, '@layer base {');
        $anchorReset = strpos($css, 'a { color: inherit; text-decoration: none; }');

        $this->assertNotFalse($baseStart);
        $this->assertNotFalse($anchorReset);
        $this->assertGreaterThan($baseStart, $anchorReset);
    }

    public function test_base44_cta_color_contracts_match_the_reference_site(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('.base44-gradient-cta,', $css);
        $this->assertStringContainsString('background: linear-gradient(90deg, #881C27, #2A6867);', $css);
        $this->assertStringContainsString('color: #fff !important;', $css);

        $this->assertStringContainsString('.base44-outline-light,', $css);
        $this->assertStringContainsString('border-color: #fff !important;', $css);

        $this->assertStringContainsString('.base44-gold-cta,', $css);
        $this->assertStringContainsString('background: #D4AF37;', $css);
        $this->assertStringContainsString('color: #1f2937 !important;', $css);

        $this->assertStringContainsString('.base44-dark-cta,', $css);
        $this->assertStringContainsString('background: rgba(0, 0, 0, .90);', $css);
    }

    public function test_homepage_uses_white_nav_dark_controls_and_reference_cta_contracts(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString('class="relative inset-x-0 top-0 z-50 bg-white shadow-sm md:fixed"', $html);
        $this->assertStringContainsString('text-gray-800', $html);
        $this->assertStringContainsString('text-gray-700', $html);
        $this->assertStringContainsString('base44-gradient-cta', $html);
        $this->assertStringContainsString('base44-outline-light', $html);
    }

    public function test_public_reference_pages_keep_the_expected_cta_palette(): void
    {
        $this->get('/en/measurement-guide')
            ->assertOk()
            ->assertSee('base44-gold-cta', false)
            ->assertSee('base44-gradient-cta', false);

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('base44-gold-cta', false)
            ->assertSee('base44-gradient-cta', false);

        $this->get('/en/contact')
            ->assertOk()
            ->assertSee('base44-gradient-cta', false);

        $this->get('/en/faq')
            ->assertOk()
            ->assertSee('base44-gradient-cta', false);

        $this->get('/en/shop')
            ->assertOk()
            ->assertSee('bg-white', false);
    }


    public function test_post_bundle_parity_stylesheet_is_loaded_after_vite(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();
        $parityCss = file_get_contents(public_path('css/storefront-parity.css'));

        $this->assertStringContainsString('/css/storefront-parity.css', $html);
        $this->assertStringContainsString('.base44-gradient-cta,', $parityCss);
        $this->assertStringContainsString('color: #fff !important;', $parityCss);
        $this->assertStringContainsString('.font-semibold { font-weight: 600 !important; }', $parityCss);
    }

    public function test_product_card_quick_look_is_white_text_on_dark_background(): void
    {
        $blade = file_get_contents(resource_path('views/components/product-card.blade.php'));

        $this->assertStringContainsString('base44-dark-cta', $blade);
    }
}
