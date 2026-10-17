<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Original Base44 catalog has aggregate product stock, not per-size stock.
            $table->unsignedInteger('stock_reserved')->default(0);
        });

        Schema::table('orders', function (Blueprint $table): void {
            // Makes capture/release safe against duplicate webhook events and expirations.
            $table->string('reservation_state', 16)->default('reserved');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('reservation_state'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('stock_reserved'));
    }
};
