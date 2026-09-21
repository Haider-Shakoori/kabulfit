<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table): void {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token', 64)->nullable()->unique(); $table->char('currency', 3)->default('AFN'); $table->timestamps();
            $table->index(['user_id', 'updated_at']);
        });
        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id(); $table->foreignId('cart_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete(); $table->unsignedInteger('quantity');
            $table->timestamps(); $table->unique(['cart_id','product_id','product_variant_id']);
        });
        Schema::create('wishlists', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps(); $table->unique(['user_id','product_id']);
        });
        Schema::create('coupons', function (Blueprint $table): void {
            $table->id(); $table->string('code')->unique(); $table->enum('type',['fixed','percent']); $table->unsignedBigInteger('value');
            $table->unsignedBigInteger('minimum_subtotal_minor')->default(0); $table->unsignedBigInteger('maximum_discount_minor')->nullable();
            $table->unsignedInteger('usage_limit')->nullable(); $table->unsignedInteger('times_used')->default(0); $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('shipping_methods', function (Blueprint $table): void {
            $table->id(); $table->string('code')->unique(); $table->string('name'); $table->char('currency',3)->default('AFN');
            $table->unsignedBigInteger('price_minor'); $table->boolean('is_active')->default(true)->index(); $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table): void {
            $table->id(); $table->uuid('uuid')->unique(); $table->string('number')->unique(); $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('status',['pending_payment','paid','payment_failed','cancelled','refunded'])->default('pending_payment')->index();
            $table->enum('payment_status',['pending','processing','succeeded','failed','cancelled','refunded'])->default('pending')->index();
            $table->char('currency',3); $table->unsignedBigInteger('subtotal_minor'); $table->unsignedBigInteger('discount_minor')->default(0);
            $table->unsignedBigInteger('shipping_minor')->default(0); $table->unsignedBigInteger('total_minor'); $table->string('coupon_code')->nullable();
            $table->string('shipping_method_code'); $table->json('shipping_address'); $table->timestamp('paid_at')->nullable(); $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete(); $table->string('sku'); $table->string('name');
            $table->string('variant_label')->nullable(); $table->unsignedBigInteger('unit_price_minor'); $table->unsignedInteger('quantity'); $table->unsignedBigInteger('line_total_minor'); $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table): void {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->string('provider')->default('stripe');
            $table->string('provider_payment_id')->nullable()->unique(); $table->enum('status',['pending','processing','succeeded','failed','cancelled','refunded'])->default('pending')->index();
            $table->char('currency',3); $table->unsignedBigInteger('amount_minor'); $table->string('idempotency_key')->unique(); $table->text('failure_message')->nullable(); $table->timestamps();
        });
        Schema::create('payment_events', function (Blueprint $table): void {
            $table->id(); $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete(); $table->string('provider'); $table->string('provider_event_id')->unique();
            $table->string('type'); $table->json('payload'); $table->timestamp('processed_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_events'); Schema::dropIfExists('payments'); Schema::dropIfExists('order_items'); Schema::dropIfExists('orders');
        Schema::dropIfExists('shipping_methods'); Schema::dropIfExists('coupons'); Schema::dropIfExists('wishlists'); Schema::dropIfExists('cart_items'); Schema::dropIfExists('carts');
    }
};