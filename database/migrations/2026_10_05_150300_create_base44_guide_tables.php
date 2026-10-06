<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_guide_videos', function (Blueprint $table): void {
            $table->id();
            $table->string('measurement_field')->index();
            $table->string('category', 20)->index();
            $table->string('title')->nullable();
            $table->string('video_url', 2048);
            $table->string('video_url_dari', 2048)->nullable();
            $table->string('video_url_pashto', 2048)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->index(['category', 'is_active', 'display_order']);
        });

        Schema::create('size_guides', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('title_pashto')->nullable();
            $table->string('title_dari')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('subcategory')->nullable()->index();
            $table->string('image_url', 2048);
            $table->string('image_url_pashto', 2048)->nullable();
            $table->string('image_url_dari', 2048)->nullable();
            $table->text('description')->nullable();
            $table->text('description_pashto')->nullable();
            $table->text('description_dari')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->index(['category', 'subcategory', 'is_active', 'display_order'], 'size_guides_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_guides');
        Schema::dropIfExists('measurement_guide_videos');
    }
};
