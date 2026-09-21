<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class CommerceSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::updateOrCreate(['code' => 'standard-af'], ['name' => 'Standard Afghanistan Delivery', 'currency' => 'AFN', 'price_minor' => 25000, 'is_active' => true, 'sort_order' => 10]);
        ShippingMethod::updateOrCreate(['code' => 'express-af'], ['name' => 'Express Afghanistan Delivery', 'currency' => 'AFN', 'price_minor' => 50000, 'is_active' => true, 'sort_order' => 20]);
        Coupon::updateOrCreate(['code' => 'WELCOME10'], ['type' => 'percent', 'value' => 10, 'minimum_subtotal_minor' => 100000, 'maximum_discount_minor' => 100000, 'is_active' => true]);
    }
}
