<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('status', 40)->default('pending_payment')->change();
        });

        Schema::create('order_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 40)->index();
            $table->string('source')->default('system');
            $table->text('note')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('carrier')->nullable();
            $table->string('service')->nullable();
            $table->string('tracking_number')->nullable()->unique();
            $table->string('tracking_url')->nullable();
            $table->enum('status', [
                'pending',
                'ready',
                'shipped',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'exception',
                'returned',
            ])->default('pending')->index();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('status')->index();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('customer_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->json('payload');
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->index(['user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_events');
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_status_histories');

        DB::table('orders')
            ->whereIn('status', ['processing', 'ready', 'shipped', 'delivered', 'returned'])
            ->update(['status' => 'paid']);

        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('status', [
                'pending_payment',
                'paid',
                'payment_failed',
                'cancelled',
                'refunded',
            ])->default('pending_payment')->change();
        });
    }
};
