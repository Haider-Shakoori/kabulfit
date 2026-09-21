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
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
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
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['collection_id', 'product_id']);
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_media_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_media_id')->constrained('product_media')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('alt_text');
            $table->unique(['product_media_id', 'locale']);
        });

        Schema::create('product_options', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64)->unique();
            $table->boolean('is_filterable')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_option_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->unique(['product_option_id', 'locale']);
        });

        Schema::create('product_option_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->cascadeOnDelete();
            $table->string('code', 64);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_option_id', 'code']);
            $table->index(['product_option_id', 'sort_order']);
        });

        Schema::create('product_option_value_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_option_value_id');
            $table->foreign('product_option_value_id', 'pov_translation_value_fk')
                ->references('id')
                ->on('product_option_values')
                ->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->unique(['product_option_value_id', 'locale']);
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->unsignedBigInteger('price_minor')->nullable();
            $table->unsignedBigInteger('sale_price_minor')->nullable();
            $table->char('currency', 3)->default('AFN');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'is_active', 'sort_order']);
        });

        Schema::create('product_variant_option_values', function (Blueprint $table): void {
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('product_option_value_id')->constrained('product_option_values')->restrictOnDelete();
            $table->primary(['product_variant_id', 'product_option_value_id'], 'variant_option_value_primary');
            $table->index('product_option_value_id');
        });

        Schema::create('inventory_stocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_variant_id')->unique()->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(2);
            $table->timestamps();
            $table->index(['quantity_on_hand', 'reserved_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('product_variant_option_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_option_value_translations');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_option_translations');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_media_translations');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collection_translations');
        Schema::dropIfExists('collections');
    }
};
