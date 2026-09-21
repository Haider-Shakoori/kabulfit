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

    public function test_unknown_legacy_product_is_a_real_404(): void
    {
        $this->get('/ProductDetail?id=unknown')->assertNotFound();
    }
}
