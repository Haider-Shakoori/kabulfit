<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifiedLegacyCurrencyCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_exact_original_seeded_afn_values_are_corrected_to_approved_usd_amounts(): void
    {
        $this->seed();

        Product::query()->where('sku', 'KF-M-PT-001')->update([
            'currency' => 'AFN', 'price_minor' => 650000,
        ]);
        Product::query()->where('sku', 'KF-W-DR-001')->update([
            'currency' => 'AFN', 'price_minor' => 123456,
        ]);
        ShippingMethod::query()->where('code', 'standard-af')->update([
            'currency' => 'AFN', 'price_minor' => 25000,
        ]);

        $migration = require database_path('migrations/2026_10_17_000002_correct_verified_seed_prices_to_usd.php');
        $migration->up();
        $migration->up();

        $this->assertDatabaseHas('products', [
            'sku' => 'KF-M-PT-001', 'currency' => 'USD', 'price_minor' => 6500,
        ]);
        $this->assertDatabaseHas('products', [
            'sku' => 'KF-W-DR-001', 'currency' => 'AFN', 'price_minor' => 123456,
        ]);
        $this->assertDatabaseHas('shipping_methods', [
            'code' => 'standard-af', 'currency' => 'USD', 'price_minor' => 250,
        ]);
        $this->assertDatabaseHas('shipping_methods', [
            'code' => 'express-af', 'currency' => 'USD', 'price_minor' => 500,
        ]);
    }
}
