<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('tailoring_enabled')->default(false)->index();
            $table->string('measurement_garment_type')->nullable()->index();
        });

        Schema::create('measurement_definitions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('garment_type')->index();
            $table->decimal('min_cm', 7, 2);
            $table->decimal('max_cm', 7, 2);
            $table->decimal('step_cm', 5, 2)->default(0.10);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['garment_type', 'code'], 'measurement_definition_type_code_unique');
        });

        Schema::create('measurement_definition_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('measurement_definition_id');
            $table->string('locale', 5);
            $table->string('name');
            $table->text('instructions')->nullable();
            $table->string('guide_image_path')->nullable();
            $table->timestamps();

            $table->foreign('measurement_definition_id', 'mdt_definition_fk')
                ->references('id')
                ->on('measurement_definitions')
                ->cascadeOnDelete();
            $table->unique(['measurement_definition_id', 'locale'], 'mdt_definition_locale_unique');
        });

        Schema::create('measurement_profiles', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('garment_type')->index();
            $table->enum('display_unit', ['cm', 'in'])->default('cm');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });

        Schema::create('measurement_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('measurement_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measurement_definition_id')->constrained()->restrictOnDelete();
            $table->decimal('value_cm', 7, 2);
            $table->timestamps();
            $table->unique(['measurement_profile_id', 'measurement_definition_id'], 'measurement_value_unique');
        });

        Schema::create('tailoring_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('measurement_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'ready', 'ordered', 'cancelled'])->default('draft')->index();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->index('cart_id', 'cart_items_cart_id_idx');
            $table->dropUnique(['cart_id', 'product_id', 'product_variant_id']);
            $table->foreignId('tailoring_request_id')->nullable()->after('product_variant_id')->constrained()->nullOnDelete();
            $table->string('line_key', 120)->nullable()->after('uuid');
        });

        DB::statement("UPDATE cart_items SET line_key = CONCAT('std:', product_id, ':', COALESCE(product_variant_id, 0)) WHERE line_key IS NULL");

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->unique(['cart_id', 'line_key'], 'cart_items_cart_line_key_unique');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->uuid('tailoring_request_uuid')->nullable()->after('product_variant_id')->index();
            $table->boolean('is_custom_tailored')->default(false)->after('tailoring_request_uuid');
            $table->string('measurement_profile_name')->nullable()->after('is_custom_tailored');
            $table->text('tailoring_notes')->nullable()->after('measurement_profile_name');
        });

        Schema::create('order_item_measurements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('definition_code');
            $table->string('definition_name');
            $table->decimal('value_cm', 7, 2);
            $table->timestamps();
            $table->index(['order_item_id', 'definition_code'], 'order_item_measurement_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_measurements');

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn([
                'tailoring_request_uuid',
                'is_custom_tailored',
                'measurement_profile_name',
                'tailoring_notes',
            ]);
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropUnique('cart_items_cart_line_key_unique');
            $table->dropConstrainedForeignId('tailoring_request_id');
            $table->dropColumn('line_key');
            $table->dropIndex('cart_items_cart_id_idx');
            $table->unique(['cart_id', 'product_id', 'product_variant_id']);
        });

        Schema::dropIfExists('tailoring_requests');
        Schema::dropIfExists('measurement_values');
        Schema::dropIfExists('measurement_profiles');
        Schema::dropIfExists('measurement_definition_translations');
        Schema::dropIfExists('measurement_definitions');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['tailoring_enabled', 'measurement_garment_type']);
        });
    }
};
