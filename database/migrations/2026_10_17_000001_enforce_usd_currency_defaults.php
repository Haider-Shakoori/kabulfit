<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'carts', 'shipping_methods'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->char('currency', 3)->default('USD')->change();
            });
        }
    }

    public function down(): void
    {
        // Intentionally retain USD defaults rather than restoring legacy AFN defaults.
    }
};
