<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tailor_assignments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tailoring_request_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('tailor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_by_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', [
                'assigned',
                'accepted',
                'in_progress',
                'fitting',
                'completed',
                'cancelled',
            ])->default('assigned')->index();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('fitting_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['tailor_id', 'status', 'assigned_at'], 'tailor_assignment_queue_idx');
        });

        Schema::create('tailor_assignment_notes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tailor_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('tailor_assignment_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tailor_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->index();
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['tailor_assignment_id', 'created_at'], 'tailor_assignment_event_timeline_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tailor_assignment_events');
        Schema::dropIfExists('tailor_assignment_notes');
        Schema::dropIfExists('tailor_assignments');
    }
};
