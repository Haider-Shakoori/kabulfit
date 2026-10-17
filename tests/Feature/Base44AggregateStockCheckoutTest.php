<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class Base44AggregateStockCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function importedProduct(int $quantity = 7): Product
    {
        $product = Product::query()->create([
            'category_id' => Category::query()->firstOrFail()->id,
            'sku' => 'B44-AGGREGATE-STOCK-TEST',
            'price_minor' => 5900,
            'currency' => 'USD',
            'stock_quantity' => $quantity,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1500,
        ]);

        $product->translations()->create([
            'locale' => 'en',
            'name' => 'Base44 Aggregate Stock Test',
            'slug' => 'base44-aggregate-stock-test',
        ]);

        return $product;
    }

    private function checkoutFor(Product $product, int $quantity): \App\Models\Order
    {
        $user = User::factory()->create();
        $address = $user->addresses()->create([
            'uuid' => (string) Str::uuid(),
            'label' => 'Home',
            'recipient_name' => 'Customer',
            'phone' => '+93700000000',
            'country_code' => 'AF',
            'province' => 'Kabul',
            'city' => 'Kabul',
            'address_line1' => 'Test address',
            'is_default' => true,
        ]);

        $carts = app(CartService::class);
        $cart = $carts->forUser($user);
        $carts->add($cart, $product->fresh(['translations', 'variants.inventory']), null, $quantity);

        return app(CheckoutService::class)->create(
            $user,
            $cart,
            $address,
            ShippingMethod::query()->where('code', 'standard-af')->firstOrFail(),
        );
    }

    public function test_variantless_original_stock_is_reported_to_storefront_and_mobile(): void
    {
        $product = $this->importedProduct();

        // CatalogQuery eager-loads an empty variants relation for imported Base44 items.
        $this->assertSame(7, $product->load('variants')->availableStock());

        $this->getJson('/api/v1/en/catalog?q=Base44%20Aggregate%20Stock%20Test')
            ->assertOk()
            ->assertJsonPath('data.0.sku', $product->sku)
            ->assertJsonPath('data.0.stock.available', 7)
            ->assertJsonPath('data.0.stock.in_stock', true);

        $this->getJson('/api/v1/en/products/base44-aggregate-stock-test')
            ->assertOk()
            ->assertJsonPath('data.stock.available', 7)
            ->assertJsonPath('data.price.currency', 'USD');
    }

    public function test_variantless_checkout_reserves_releases_and_captures_idempotently(): void
    {
        $product = $this->importedProduct();
        $service = app(CheckoutService::class);

        $cancelled = $this->checkoutFor($product, 2);
        $this->assertSame('reserved', $cancelled->fresh()->reservation_state);
        $this->assertSame(2, $product->fresh()->stock_reserved);
        $this->assertSame(5, $product->fresh()->availableStock());

        $service->releaseReservations($cancelled);
        $service->releaseReservations($cancelled);
        $this->assertSame('released', $cancelled->fresh()->reservation_state);
        $this->assertSame(0, $product->fresh()->stock_reserved);
        $this->assertSame(7, $product->fresh()->stock_quantity);

        // A late payment callback cannot capture stock released for a cancelled order.
        $service->captureReservations($cancelled);
        $this->assertSame(7, $product->fresh()->stock_quantity);

        $paid = $this->checkoutFor($product, 3);
        $this->assertSame(3, $product->fresh()->stock_reserved);
        $service->captureReservations($paid);
        $service->captureReservations($paid);
        $service->releaseReservations($paid);
        $this->assertSame('captured', $paid->fresh()->reservation_state);
        $this->assertSame(0, $product->fresh()->stock_reserved);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame(4, $product->fresh()->availableStock());
    }

    public function test_in_stock_catalog_filter_omits_fully_reserved_variantless_products(): void
    {
        $product = $this->importedProduct(2);
        $this->checkoutFor($product, 2);

        $this->assertSame(0, $product->fresh(['variants'])->availableStock());

        $this->getJson('/api/v1/en/catalog?in_stock=1&q=Base44%20Aggregate%20Stock%20Test')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
