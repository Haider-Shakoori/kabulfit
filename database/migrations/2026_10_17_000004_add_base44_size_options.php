<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Base44 supplies size choices but only aggregate product stock.
            // Keep choices separate from inventory variants so stock is not invented.
            $table->json('size_options')->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->string('size_code', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', fn (Blueprint $table) => $table->dropColumn('size_code'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('size_options'));
    }
};
