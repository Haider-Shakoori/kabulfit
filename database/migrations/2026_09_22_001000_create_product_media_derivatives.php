<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media_derivatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_media_id')->constrained('product_media')->cascadeOnDelete();
            $table->string('disk', 80)->default('public');
            $table->string('path');
            $table->string('format', 16);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedBigInteger('byte_size')->default(0);
            $table->timestamps();

            $table->unique(
                ['product_media_id', 'format', 'width'],
                'product_media_derivative_unique'
            );
            $table->index(['product_media_id', 'format', 'width']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media_derivatives');
    }
};
