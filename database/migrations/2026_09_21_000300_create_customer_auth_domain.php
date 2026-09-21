<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('preferred_locale', 5)->default('en')->after('phone')->index();
            $table->boolean('is_active')->default(true)->after('preferred_locale')->index();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('user_devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->string('platform', 20);
            $table->string('app_version', 32)->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();
            $table->index(['user_id', 'revoked_at']);
        });

        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('label', 60)->nullable();
            $table->string('recipient_name');
            $table->string('phone', 32);
            $table->char('country_code', 2)->default('AF');
            $table->string('province', 120)->nullable();
            $table->string('city', 120);
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('personal_access_tokens');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'preferred_locale', 'is_active']);
        });
    }
};
