<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_shop_search_and_category_filters_are_server_rendered(): void
    {
        $this->get('/en/shop?q=perahan')
            ->assertOk()
            ->assertSee('Classic Afghan Perahan Tunban')
            ->assertDontSee('Herat Embroidered Afghan Dress');

        $this->get('/en/shop?category=men')
            ->assertOk()
            ->assertSee('Classic Afghan Perahan Tunban')
            ->assertDontSee('Herat Embroidered Afghan Dress');
    }

    public function test_collection_size_and_color_filters_work(): void
    {
        $this->get('/en/collections/wedding-edit')
            ->assertOk()
            ->assertSee('Herat Embroidered Afghan Dress')
            ->assertSee('Classic Afghan Perahan Tunban')
            ->assertDontSee('Kids Afghan Waistcoat Set');

        $this->get('/en/shop?size=L&color=royal-maroon')
            ->assertOk()
            ->assertSee('Traditional Afghan Waistcoat')
            ->assertDontSee('Kids Afghan Waistcoat Set');
    }

    public function test_price_sort_uses_integer_minor_unit_prices(): void
    {
        $html = $this->get('/en/shop?category=men&sort=price_asc')->assertOk()->getContent();

        $perahan = strpos($html, 'Classic Afghan Perahan Tunban');
        $waistcoat = strpos($html, 'Traditional Afghan Waistcoat');

        $this->assertIsInt($perahan);
        $this->assertIsInt($waistcoat);
        $this->assertLessThan($waistcoat, $perahan);
    }

    public function test_in_stock_filter_uses_variant_inventory(): void
    {
        $product = Product::query()->where('sku', 'KF-K-VS-001')->firstOrFail();

        $product->variants()->each(function ($variant): void {
            $variant->inventory()->update([
                'quantity_on_hand' => 0,
                'quantity_reserved' => 0,
            ]);
        });

        $this->get('/en/shop?in_stock=1')
            ->assertOk()
            ->assertDontSee('Kids Afghan Waistcoat Set')
            ->assertSee('Classic Afghan Perahan Tunban');
    }
}
