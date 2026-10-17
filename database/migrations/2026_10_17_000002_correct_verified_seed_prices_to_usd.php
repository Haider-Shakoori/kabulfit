<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            // Only the known original bootstrap fixtures, with exact old values.
            // Never apply currency conversion or alter customer orders/payments.
            foreach ([
                'KF-M-PT-001' => [650000, 6500],
                'KF-W-DR-001' => [980000, 9800],
                'KF-K-VS-001' => [420000, 4200],
                'KF-M-WC-001' => [790000, 7900],
                'KF-A-KS-001' => [480000, 4800],
            ] as $sku => [$oldMinor, $usdMinor]) {
                DB::table('products')
                    ->where('sku', $sku)
                    ->where('currency', 'AFN')
                    ->where('price_minor', $oldMinor)
                    ->update(['currency' => 'USD', 'price_minor' => $usdMinor]);
            }

            foreach ([
                'standard-af' => [25000, 250],
                'express-af' => [50000, 500],
            ] as $code => [$oldMinor, $usdMinor]) {
                DB::table('shipping_methods')
                    ->where('code', $code)
                    ->where('currency', 'AFN')
                    ->where('price_minor', $oldMinor)
                    ->update(['currency' => 'USD', 'price_minor' => $usdMinor]);
            }
        });
    }

    public function down(): void
    {
        // A rollback must never relabel verified USD monetary values as AFN.
    }
};
