<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_pages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('page_key')->unique();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('content_page_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->timestamps();
            $table->unique(['content_page_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('blog_post_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->timestamps();
            $table->unique(['blog_post_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::create('legacy_url_inventory', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('legacy_path');
            $table->string('query_key')->default('');
            $table->string('query_value')->default('');
            $table->enum('disposition', ['redirect', 'manual_product', 'private', 'gone'])->index();
            $table->string('target_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['legacy_path', 'query_key', 'query_value'], 'legacy_url_inventory_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_url_inventory');
        Schema::dropIfExists('blog_post_translations');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('content_page_translations');
        Schema::dropIfExists('content_pages');
    }
};
