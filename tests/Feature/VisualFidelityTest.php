<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualFidelityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_preserves_live_reference_content_hierarchy(): void
    {
        $response = $this->get('/en')->assertOk();

        $response
            ->assertSee('Authentic Afghan Elegance')
            ->assertSee('Perfect fit with our measurement system')
            ->assertSee('We ship to over 50 countries')
            ->assertSee('Premium fabrics and craftsmanship')
            ->assertSee('Handcrafted')
            ->assertSee('Shop by Category')
            ->assertSee('Afghan Culture')
            ->assertSee('Best Sellers')
            ->assertSee('Get Your Perfect Measurements')
            ->assertSee('Authentic Afghan Clothes &amp; Custom Tailoring', false)
            ->assertSee('What Our Customers Say')
            ->assertSee('Ahmad K.')
            ->assertSee('Sarah M.')
            ->assertSee('Farid A.');

        $html = $response->getContent();

        $positions = [
            'hero' => strpos($html, 'data-section="hero"'),
            'categories' => strpos($html, 'data-section="categories"'),
            'featured' => strpos($html, 'data-section="featured"'),
            'story' => strpos($html, 'data-section="story"'),
            'best-sellers' => strpos($html, 'data-section="best-sellers"'),
            'measurements' => strpos($html, 'data-section="measurements"'),
            'heritage-content' => strpos($html, 'data-section="heritage-content"'),
            'testimonials' => strpos($html, 'data-section="testimonials"'),
        ];

        foreach ($positions as $section => $position) {
            $this->assertNotFalse($position, $section.' section is missing');
        }

        $this->assertLessThan($positions['categories'], $positions['hero']);
        $this->assertLessThan($positions['featured'], $positions['categories']);
        $this->assertLessThan($positions['story'], $positions['featured']);
        $this->assertLessThan($positions['best-sellers'], $positions['story']);
        $this->assertLessThan($positions['measurements'], $positions['best-sellers']);
        $this->assertLessThan($positions['heritage-content'], $positions['measurements']);
        $this->assertLessThan($positions['testimonials'], $positions['heritage-content']);
    }

    public function test_homepage_uses_measured_live_layout_classes_instead_of_legacy_approximation(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString('relative h-screen overflow-hidden', $html);
        $this->assertStringContainsString('grid grid-cols-2 gap-8 md:grid-cols-4', $html);
        $this->assertStringContainsString('grid grid-cols-4 gap-4 sm:gap-6 md:gap-8', $html);
        $this->assertStringContainsString('h-[300px] overflow-hidden rounded-2xl sm:h-[400px] sm:rounded-[2.5rem] md:h-[500px] md:rounded-[3rem]', $html);
        $this->assertStringContainsString('grid grid-cols-2 gap-6 md:grid-cols-4', $html);
        $this->assertStringContainsString('bg-gradient-to-b from-[#FDF5E6] to-[#FDFBF7] py-20', $html);
        $this->assertStringContainsString('data-header-action="search"', $html);
        $this->assertStringContainsString('data-header-action="wishlist"', $html);
        $this->assertStringContainsString('data-header-action="cart"', $html);
        $this->assertStringContainsString('data-header-action="account"', $html);
        $this->assertStringContainsString('safe-bottom fixed inset-x-0 bottom-0', $html);

        $this->assertStringNotContainsString('live-category-card', $html);
        $this->assertStringNotContainsString('live-products-section', $html);
        $this->assertStringNotContainsString('live-story-grid', $html);
    }

    public function test_customer_homepage_contains_no_internal_implementation_placeholder_language(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringNotContainsString('scheduled for', $html);
        $this->assertStringNotContainsString('architecture ready', $html);
        $this->assertStringNotContainsString('workflow foundation', strtolower($html));
        $this->assertStringNotContainsString('Batch 3', $html);
    }

    public function test_design_tokens_and_responsive_guards_are_centralized(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        foreach ([
            '--brand-primary:',
            '--brand-secondary:',
            '--brand-accent:',
            '--brand-background:',
            '--brand-surface:',
            '--brand-text:',
            '--brand-muted:',
            '--brand-border:',
        ] as $token) {
            $this->assertStringContainsString($token, $css);
        }

        $this->assertStringContainsString('@media (max-width: 430px)', $css);
        $this->assertStringContainsString('@media (max-width: 768px)', $css);
        $this->assertStringContainsString('@media (max-width: 1024px)', $css);
        $this->assertStringContainsString('@media (min-width: 1600px)', $css);
        $this->assertStringContainsString('prefers-reduced-motion', $css);
    }

    public function test_accessibility_landmarks_and_live_mobile_navigation_are_present(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString('class="skip-link"', $html);
        $this->assertStringContainsString('aria-controls="mobile-navigation"', $html);
        $this->assertStringContainsString('aria-label="Primary navigation"', $html);
        $this->assertStringContainsString('id="mobile-navigation"', $html);
        $this->assertStringContainsString('id="main-content"', $html);
        $this->assertStringContainsString('aria-labelledby="testimonials-title"', $html);
    }
}
