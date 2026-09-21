<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentlyViewedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_product_views_keep_a_small_session_based_recent_history(): void
    {
        $products = Product::query()
            ->with('translations')
            ->whereIn('sku', ['KF-K-VS-001', 'KF-W-DR-001'])
            ->get()
            ->keyBy('sku');

        $kids = $products->get('KF-K-VS-001')->translation('en');
        $dress = $products->get('KF-W-DR-001')->translation('en');

        $this->get('/en/products/'.$kids->slug)->assertOk();

        $this->get('/en/products/'.$dress->slug)
            ->assertOk()
            ->assertSee('Recently viewed')
            ->assertSee($kids->name);
    }
}
