<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('collection_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->unique(['collection_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::create('collection_product', function (Blueprint $table): void {
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['collection_id', 'product_id']);
            $table->index(['collection_id', 'sort_order']);
        });

        Schema::create('sizes', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 24)->unique();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('colors', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('hex_value', 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('color_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->unique(['color_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('color_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('option_key', 100);
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->unsignedBigInteger('price_minor')->nullable();
            $table->unsignedBigInteger('sale_price_minor')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'option_key']);
            $table->index(['product_id', 'is_active', 'sort_order']);
        });

        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_variant_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->unsignedInteger('quantity_reserved')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(2);
            $table->timestamps();
        });

        Schema::create('product_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();
            $table->unique(['product_id', 'path']);
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_media_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_media_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('alt_text');
            $table->unique(['product_media_id', 'locale']);
        });

        Schema::create('related_products', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('related_product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['product_id', 'related_product_id']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('related_products');
        Schema::dropIfExists('product_media_translations');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('color_translations');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collection_translations');
        Schema::dropIfExists('collections');
    }
};
