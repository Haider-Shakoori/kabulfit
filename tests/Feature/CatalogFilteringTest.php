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
        $this->get('/en/shop?q=shawl')
            ->assertOk()
            ->assertSee('Kuchi-Inspired Afghan Shawl')
            ->assertDontSee('Classic Afghan Perahan Tunban');

        $this->get('/en/shop?category=accessories')
            ->assertOk()
            ->assertSee('Kuchi-Inspired Afghan Shawl')
            ->assertDontSee('Kids Afghan Waistcoat Set');
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
        $html = $this->get('/en/shop?sort=price_asc&per_page=48')->assertOk()->getContent();

        $kids = strpos($html, 'Kids Afghan Waistcoat Set');
        $shawl = strpos($html, 'Kuchi-Inspired Afghan Shawl');
        $perahan = strpos($html, 'Classic Afghan Perahan Tunban');

        $this->assertIsInt($kids);
        $this->assertIsInt($shawl);
        $this->assertIsInt($perahan);
        $this->assertLessThan($shawl, $kids);
        $this->assertLessThan($perahan, $shawl);
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
