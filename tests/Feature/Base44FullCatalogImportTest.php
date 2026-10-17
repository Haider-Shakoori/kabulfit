<?php

namespace Tests\Feature;

use App\Models\LegacyProductRedirect;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Base44FullCatalogImportTest extends TestCase
{
    use RefreshDatabase;

    private string $snapshot;

    private string $image;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $url = 'https://base44.app/api/apps/example/files/public/import-test.png';
        $name = substr(hash('sha256', $url), 0, 20).'.png';
        $folder = public_path('images/kabulfit-base44/catalog');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $this->image = $folder.'/'.$name;
        file_put_contents($this->image, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/Jd8AAAAASUVORK5CYII='
        ));

        $this->snapshot = tempnam(sys_get_temp_dir(), 'base44-catalog-');
        file_put_contents($this->snapshot, json_encode([
            'categories' => [[
                'id' => '69477bc8405f92df484cb236',
                'name' => 'Men',
                'name_dari' => 'مردانه',
                'name_pashto' => 'نارینه',
                'slug' => 'men',
                'parent_category' => '',
                'is_active' => true,
            ]],
            'products' => [[
                'id' => 'abcdef1234567890abcdef01',
                'category_id' => '69477bc8405f92df484cb236',
                'subcategory' => '',
                'name' => 'Original Base44 Product',
                'name_dari' => 'Original Base44 Product',
                'name_pashto' => 'Original Base44 Product',
                'slug' => 'original-base44-product',
                'price' => 219,
                'sale_price' => null,
                'stock_quantity' => 7,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'is_active' => true,
                'is_featured' => true,
                'allows_custom_size' => false,
                'description' => 'Original catalog item',
                'description_dari' => '',
                'description_pashto' => '',
                'images' => [$url],
            ]],
        ], JSON_THROW_ON_ERROR));
    }

    protected function tearDown(): void
    {
        @unlink($this->snapshot);
        @unlink($this->image);
        parent::tearDown();
    }

    public function test_validation_is_read_only_and_import_preserves_original_dollar_price(): void
    {
        $args = [
            'snapshot' => $this->snapshot,
            '--expected-total' => '1',
            '--expected-active' => '1',
        ];

        $this->assertSame(0, Artisan::call('kabulfit:import-base44', $args));
        $this->assertDatabaseMissing('products', ['sku' => 'B44-ABCDEF1234567890ABCDEF01']);

        $this->assertSame(0, Artisan::call('kabulfit:import-base44', $args + ['--commit' => true]));

        $product = Product::query()->where('sku', 'B44-ABCDEF1234567890ABCDEF01')->firstOrFail();
        $this->assertSame(21900, $product->price_minor);
        $this->assertSame('USD', $product->currency);
        $this->assertTrue($product->is_active);
        $this->assertSame(7, $product->stock_quantity);
        $this->assertSame(['S', 'M', 'L', 'XL'], $product->size_options);
        $this->assertSame(1, $product->media()->count());
        $this->assertSame('Original Base44 Product', $product->translation('en')->name);
        $this->assertDatabaseHas('legacy_product_redirects', [
            'legacy_key' => 'abcdef1234567890abcdef01',
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('products', ['sku' => 'KF-M-PT-001', 'is_active' => false]);

        $this->assertSame(0, Artisan::call('kabulfit:import-base44', $args + ['--commit' => true]));
        $this->assertSame(1, Product::query()->where('sku', 'B44-ABCDEF1234567890ABCDEF01')->count());
        $this->assertSame(1, LegacyProductRedirect::query()->where('legacy_key', 'abcdef1234567890abcdef01')->count());
    }

    public function test_customers_choose_original_size_without_invented_variant_inventory(): void
    {
        $this->assertSame(0, Artisan::call('kabulfit:import-base44', [
            'snapshot' => $this->snapshot,
            '--expected-total' => '1',
            '--expected-active' => '1',
            '--commit' => true,
        ]));

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'product_slug' => 'original-base44-product',
            'quantity' => 2,
        ];
        $this->postJson('/api/v1/en/cart/items', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['size_code']);

        $this->postJson('/api/v1/en/cart/items', $payload + ['size_code' => 'XXL'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['size_code']);

        $this->postJson('/api/v1/en/cart/items', $payload + ['size_code' => 'M'])
            ->assertCreated()
            ->assertJsonPath('data.items.0.size_code', 'M');

        $this->postJson('/api/v1/en/cart/items', $payload + ['size_code' => 'L'])
            ->assertCreated()
            ->assertJsonCount(2, 'data.items');

        $this->getJson('/api/v1/en/products/original-base44-product')
            ->assertOk()
            ->assertJsonPath('data.size_options', ['S', 'M', 'L', 'XL'])
            ->assertJsonPath('data.stock.available', 7);

        $address = $user->addresses()->create([
            'uuid' => (string) Str::uuid(), 'label' => 'Home',
            'recipient_name' => 'Customer', 'phone' => '+93700000000',
            'country_code' => 'AF', 'province' => 'Kabul', 'city' => 'Kabul',
            'address_line1' => 'Test address', 'is_default' => true,
        ]);
        $order = app(CheckoutService::class)->create(
            $user,
            app(CartService::class)->forUser($user),
            $address,
            ShippingMethod::query()->where('code', 'standard-af')->firstOrFail(),
        );
        $this->assertSame(['Size L', 'Size M'], $order->items->pluck('variant_label')->sort()->values()->all());
        $this->assertSame(4, Product::query()->where('sku', 'B44-ABCDEF1234567890ABCDEF01')->firstOrFail()->stock_reserved);
    }

    public function test_size_only_backfill_preserves_custom_catalog_prices_and_stock(): void
    {
        $args = ['snapshot' => $this->snapshot, '--expected-total' => '1', '--expected-active' => '1'];
        $this->assertSame(0, Artisan::call('kabulfit:import-base44', $args + ['--commit' => true]));
        $product = Product::query()->where('sku', 'B44-ABCDEF1234567890ABCDEF01')->firstOrFail();
        $product->update(['price_minor' => 12345, 'stock_quantity' => 3, 'size_options' => null]);

        $this->assertSame(0, Artisan::call('kabulfit:sync-base44-sizes', $args));
        $this->assertNull($product->fresh()->size_options);
        $this->assertSame(0, Artisan::call('kabulfit:sync-base44-sizes', $args + ['--commit' => true]));
        $this->assertSame(['S', 'M', 'L', 'XL'], $product->fresh()->size_options);
        $this->assertSame(12345, $product->fresh()->price_minor);
        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_validation_refuses_incomplete_snapshot_without_database_changes(): void
    {
        $this->assertSame(1, Artisan::call('kabulfit:import-base44', [
            'snapshot' => $this->snapshot,
        ]));
        $this->assertDatabaseMissing('products', ['sku' => 'B44-ABCDEF1234567890ABCDEF01']);
    }
}
