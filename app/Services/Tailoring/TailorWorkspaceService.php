<?php

namespace App\Services\Tailoring;

use App\Models\TailorAssignment;
use App\Models\TailorAssignmentEvent;
use App\Models\TailorAssignmentNote;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Services\Admin\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TailorWorkspaceService
{
    private const TRANSITIONS = [
        'assigned' => ['accepted', 'cancelled'],
        'accepted' => ['in_progress', 'cancelled'],
        'in_progress' => ['fitting', 'completed', 'cancelled'],
        'fitting' => ['in_progress', 'completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function __construct(private readonly AuditService $audit) {}

    public function assign(User $actor, TailoringRequest $tailoring, User $tailor): TailorAssignment
    {
        abort_unless($actor->hasPermission('tailoring.manage'), 403);

        if (! $tailor->is_active || ! $tailor->hasPermission('tailoring.work')) {
            throw ValidationException::withMessages([
                'tailor_uuid' => __('tailor.invalid_tailor'),
            ]);
        }

        $tailoring->loadMissing(['orderItem.order', 'orderItem.measurements']);

        if (
            $tailoring->status !== 'ordered'
            || ! $tailoring->orderItem
            || ! $tailoring->orderItem->is_custom_tailored
            || ! $tailoring->orderItem->order
            || $tailoring->orderItem->order->payment_status !== 'succeeded'
        ) {
            throw ValidationException::withMessages([
                'tailoring' => __('tailor.assignment_requires_paid_order'),
            ]);
        }

        if ($tailoring->orderItem->measurements->isEmpty()) {
            throw ValidationException::withMessages([
                'tailoring' => __('tailor.assignment_requires_snapshot'),
            ]);
        }

        return DB::transaction(function () use ($actor, $tailoring, $tailor): TailorAssignment {
            $locked = TailoringRequest::query()
                ->whereKey($tailoring->id)
                ->lockForUpdate()
                ->firstOrFail();

            $assignment = TailorAssignment::query()
                ->where('tailoring_request_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($assignment && $assignment->status === 'completed') {
                throw ValidationException::withMessages([
                    'tailoring' => __('tailor.completed_assignment_cannot_reassign'),
                ]);
            }

            if ($assignment && $assignment->tailor_id === $tailor->id && $assignment->status !== 'cancelled') {
                return $assignment;
            }

            $now = now();

            if ($assignment) {
                $previousTailor = $assignment->tailor()->value('uuid');
                $previousStatus = $assignment->status;

                $assignment->update([
                    'tailor_id' => $tailor->id,
                    'assigned_by_id' => $actor->id,
                    'status' => 'assigned',
                    'assigned_at' => $now,
                    'accepted_at' => null,
                    'started_at' => null,
                    'fitting_at' => null,
                    'completed_at' => null,
                    'cancelled_at' => null,
                ]);

                $this->event($assignment, $actor, 'reassigned', $previousStatus, 'assigned', [
                    'previous_tailor_uuid' => $previousTailor,
                    'tailor_uuid' => $tailor->uuid,
                ]);

                $this->audit->record($actor, 'tailoring.assignment_reassigned', $assignment, [
                    'assignment_uuid' => $assignment->uuid,
                    'tailoring_uuid' => $locked->uuid,
                    'previous_tailor_uuid' => $previousTailor,
                    'tailor_uuid' => $tailor->uuid,
                ]);

                return $assignment->fresh();
            }

            $assignment = TailorAssignment::query()->create([
                'uuid' => (string) Str::uuid(),
                'tailoring_request_id' => $locked->id,
                'tailor_id' => $tailor->id,
                'assigned_by_id' => $actor->id,
                'status' => 'assigned',
                'assigned_at' => $now,
            ]);

            $this->event($assignment, $actor, 'assigned', null, 'assigned', [
                'tailor_uuid' => $tailor->uuid,
            ]);

            $this->audit->record($actor, 'tailoring.assignment_created', $assignment, [
                'assignment_uuid' => $assignment->uuid,
                'tailoring_uuid' => $locked->uuid,
                'tailor_uuid' => $tailor->uuid,
            ]);

            return $assignment;
        }, 3);
    }

    public function transition(
        User $actor,
        TailorAssignment $assignment,
        string $status,
        ?string $note = null,
    ): TailorAssignment {
        $this->authorizeActor($actor, $assignment);

        return DB::transaction(function () use ($actor, $assignment, $status, $note): TailorAssignment {
            $locked = TailorAssignment::query()->whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            $allowed = $this->nextStatuses($locked);

            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => __('tailor.invalid_status_transition'),
                ]);
            }

            $from = $locked->status;
            $timestamps = match ($status) {
                'accepted' => ['accepted_at' => now()],
                'in_progress' => ['started_at' => $locked->started_at ?? now()],
                'fitting' => ['fitting_at' => now()],
                'completed' => ['completed_at' => now()],
                'cancelled' => ['cancelled_at' => now()],
                default => [],
            };

            $locked->update(['status' => $status, ...$timestamps]);

            $this->event($locked, $actor, 'status_changed', $from, $status);

            if (filled($note)) {
                $this->createNote($actor, $locked, trim((string) $note), false);
            }

            $this->audit->record($actor, 'tailoring.assignment_status_changed', $locked, [
                'assignment_uuid' => $locked->uuid,
                'from_status' => $from,
                'to_status' => $status,
            ]);

            return $locked->fresh();
        }, 3);
    }

    public function addNote(User $actor, TailorAssignment $assignment, string $body): TailorAssignmentNote
    {
        $this->authorizeActor($actor, $assignment);

        return DB::transaction(function () use ($actor, $assignment, $body): TailorAssignmentNote {
            $locked = TailorAssignment::query()->whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            $note = $this->createNote($actor, $locked, trim($body), true);

            return $note;
        }, 3);
    }

    public function nextStatuses(TailorAssignment $assignment): array
    {
        return self::TRANSITIONS[$assignment->status] ?? [];
    }

    private function authorizeActor(User $actor, TailorAssignment $assignment): void
    {
        abort_unless(
            $actor->hasPermission('tailoring.manage')
                || ($actor->hasPermission('tailoring.work') && $assignment->tailor_id === $actor->id),
            403,
        );
    }

    private function createNote(
        User $actor,
        TailorAssignment $assignment,
        string $body,
        bool $audit,
    ): TailorAssignmentNote {
        if ($body === '') {
            throw ValidationException::withMessages(['body' => __('tailor.note_required')]);
        }

        $note = $assignment->notes()->create([
            'uuid' => (string) Str::uuid(),
            'author_id' => $actor->id,
            'body' => $body,
            'created_at' => now(),
        ]);

        $this->event($assignment, $actor, 'note_added', null, null, [
            'note_uuid' => $note->uuid,
        ]);

        if ($audit) {
            $this->audit->record($actor, 'tailoring.assignment_note_added', $assignment, [
                'assignment_uuid' => $assignment->uuid,
                'note_uuid' => $note->uuid,
            ]);
        }

        return $note;
    }

    private function event(
        TailorAssignment $assignment,
        User $actor,
        string $type,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        array $metadata = [],
    ): TailorAssignmentEvent {
        return $assignment->events()->create([
            'uuid' => (string) Str::uuid(),
            'actor_id' => $actor->id,
            'type' => $type,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }
}
