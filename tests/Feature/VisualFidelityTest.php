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

    public function test_homepage_preserves_reference_content_hierarchy(): void
    {
        $response = $this->get('/en')->assertOk();

        $response
            ->assertSee('Authentic Afghan Elegance')
            ->assertSee('Perfect fit with our measurement system')
            ->assertSee('We ship to over 50 countries')
            ->assertSee('Premium fabrics and craftsmanship')
            ->assertSee('Shop by Category')
            ->assertSee('Afghan Culture')
            ->assertSee('Get Your Perfect Measurements')
            ->assertSee('Authentic Afghan Clothes &amp; Custom Tailoring', false)
            ->assertSee('What Our Customers Say')
            ->assertSee('Ahmad K.')
            ->assertSee('Sarah M.')
            ->assertSee('Farid A.');

        $html = $response->getContent();

        $this->assertLessThan(
            strpos($html, 'data-section="categories"'),
            strpos($html, 'data-section="hero"'),
        );
        $this->assertLessThan(
            strpos($html, 'data-section="story"'),
            strpos($html, 'data-section="featured"'),
        );
        $this->assertLessThan(
            strpos($html, 'data-section="testimonials"'),
            strpos($html, 'data-section="heritage-content"'),
        );
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

    public function test_accessibility_landmarks_and_mobile_navigation_are_present(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString('class="skip-link"', $html);
        $this->assertStringContainsString('aria-controls="primary-nav"', $html);
        $this->assertStringContainsString('aria-label="Primary navigation"', $html);
        $this->assertStringContainsString('id="main-content"', $html);
        $this->assertStringContainsString('aria-labelledby="testimonials-title"', $html);
    }
}
