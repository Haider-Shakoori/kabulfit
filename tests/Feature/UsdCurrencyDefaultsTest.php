<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsdCurrencyDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_rows_default_to_usd_without_overwriting_historical_values(): void
    {
        $this->seed();
        $category = Category::query()->firstOrFail();

        DB::table('products')->insert([
            'category_id' => $category->id,
            'sku' => 'KF-USD-SCHEMA-TEST',
            'price_minor' => 1234,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('carts')->insert([
            'uuid' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shipping_methods')->insert([
            'code' => 'schema-usd-test',
            'name' => 'USD Schema Test',
            'price_minor' => 300,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'KF-USD-SCHEMA-TEST', 'currency' => 'USD']);
        $this->assertDatabaseHas('shipping_methods', ['code' => 'schema-usd-test', 'currency' => 'USD']);
        $this->assertSame('USD', DB::table('carts')->orderByDesc('id')->value('currency'));
    }
}
