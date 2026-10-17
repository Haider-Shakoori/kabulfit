<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->string('image_url', 2048)->nullable()->after('parent_id');
            $table->index(['parent_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['parent_id', 'is_active', 'sort_order']);
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn('image_url');
        });
    }
};
