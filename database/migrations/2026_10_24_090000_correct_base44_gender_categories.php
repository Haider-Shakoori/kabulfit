<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Correct imported catalog gender assignments using the original Base44
     * product families. The specialized categories remain available for future
     * nested navigation; product gender is sourced from the Laravel database.
     */
    public function up(): void
    {
        $ids = DB::table('category_translations')
            ->where('locale', 'en')
            ->whereIn('slug', [
                'men', 'women', 'boys', 'girls',
                'embroidery', 'gandafghanifl', 'gandafghanikl', 'kgandafghani',
            ])
            ->pluck('category_id', 'slug');

        foreach ([
            'embroidery' => 'men',
            'gandafghanifl' => 'women',
            'gandafghanikl' => 'women',
            'kgandafghani' => 'girls',
        ] as $source => $destination) {
            if (! isset($ids[$source], $ids[$destination])) {
                throw new RuntimeException("Missing catalog category: {$source} or {$destination}");
            }

            DB::table('products')
                ->where('category_id', $ids[$source])
                ->update(['category_id' => $ids[$destination]]);
        }
    }

    public function down(): void
    {
        // No destructive rollback: individual products may have been recategorized
        // by the store administrator after this migration.
    }
};
