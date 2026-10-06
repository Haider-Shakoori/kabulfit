<?php

namespace Tests\Feature;

use App\Models\LegacyProductRedirect;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_mapped_legacy_product_url_redirects_permanently_to_clean_slug(): void
    {
        $product = Product::query()->with('translations')->where('sku', 'KF-M-PT-001')->firstOrFail();
        LegacyProductRedirect::query()->create(['legacy_key' => 'legacy-demo-id', 'product_id' => $product->id]);

        $slug = $product->translations->firstWhere('locale', 'en')->slug;

        $this->get('/ProductDetail?id=legacy-demo-id')
            ->assertStatus(301)
            ->assertRedirect('/en/products/'.$slug);
    }

    public function test_legacy_base44_page_urls_redirect_to_canonical_laravel_urls(): void
    {
        $redirects = [
            '/Home' => '/en',
            '/About' => '/en/about',
            '/Contact' => '/en/contact',
            '/FAQ' => '/en/faq',
            '/MeasurementGuide' => '/en/measurement-guide',
            '/PrivacyPolicy' => '/en/privacy-policy',
            '/ReturnPolicy' => '/en/return-policy',
            '/ShippingPolicy' => '/en/shipping-policy',
            '/Shop' => '/en/shop',
            '/TermsConditions' => '/en/terms-and-conditions',
            '/Account' => '/en/account',
            '/Cart' => '/en/cart',
            '/Checkout' => '/en/checkout',
            '/Orders' => '/en/orders',
            '/Wishlist' => '/en/wishlist',
            '/MyMeasurements' => '/en/measurements',
            '/TailorDashboard' => '/en/tailor',
        ];

        foreach ($redirects as $legacy => $canonical) {
            $this->get($legacy)
                ->assertStatus(301)
                ->assertRedirect($canonical);
        }
    }

    public function test_primary_navigation_uses_dedicated_localized_contact_and_measurement_pages(): void
    {
        $targets = [
            'en' => ['/en/contact', '/en/measurement-guide'],
            'fa' => ['/fa/'.rawurlencode('تماس'), '/fa/'.rawurlencode('راهنمای-اندازه-گیری')],
            'ps' => ['/ps/'.rawurlencode('اړیکه'), '/ps/'.rawurlencode('د-اندازې-لارښود')],
        ];

        foreach ($targets as $locale => $hrefs) {
            $response = $this->get('/'.$locale)->assertOk();

            foreach ($hrefs as $href) {
                $response->assertSee($href, false);
            }
        }
    }

    public function test_unknown_legacy_product_is_a_real_404(): void
    {
        $this->get('/ProductDetail?id=unknown')->assertNotFound();
    }
}
