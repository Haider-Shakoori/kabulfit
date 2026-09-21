<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_definitions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('garment_type')->index();
            $table->decimal('min_cm', 7, 2);
            $table->decimal('max_cm', 7, 2);
            $table->decimal('step_cm', 5, 2)->default(0.10);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('measurement_definition_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('measurement_definition_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('instructions')->nullable();
            $table->string('guide_image_path')->nullable();
            $table->timestamps();
            $table->unique(['measurement_definition_id', 'locale']);
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
            $table->unique(['measurement_profile_id', 'measurement_definition_id']);
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

        Schema::create('order_item_measurements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('definition_code');
            $table->string('definition_name');
            $table->decimal('value_cm', 7, 2);
            $table->timestamps();
            $table->index(['order_item_id', 'definition_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_measurements');
        Schema::dropIfExists('tailoring_requests');
        Schema::dropIfExists('measurement_values');
        Schema::dropIfExists('measurement_profiles');
        Schema::dropIfExists('measurement_definition_translations');
        Schema::dropIfExists('measurement_definitions');
    }
};
