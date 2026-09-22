<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TailorAssignment;
use App\Services\Tailoring\TailorWorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TailorWorkspaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(TailorAssignment::STATUSES)],
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = TailorAssignment::query()
            ->where('tailor_id', $request->user()->id)
            ->with([
                'tailoringRequest.user',
                'tailoringRequest.product.translations',
                'tailoringRequest.variant',
                'tailoringRequest.orderItem.order',
            ])
            ->latest('assigned_at');

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        $assignments = $query->paginate((int) ($data['per_page'] ?? 20));

        return response()->json([
            'data' => $assignments->getCollection()
                ->map(fn (TailorAssignment $assignment) => $this->payload($assignment)),
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'per_page' => $assignments->perPage(),
                'total' => $assignments->total(),
            ],
        ]);
    }

    public function show(
        Request $request,
        string $locale,
        string $assignment,
        TailorWorkspaceService $service,
    ): JsonResponse {
        $owned = $this->ownedAssignment($request, $assignment);

        return response()->json([
            'data' => [
                ...$this->payload($owned, true),
                'allowed_statuses' => $service->nextStatuses($owned),
            ],
        ]);
    }

    public function transition(
        Request $request,
        string $locale,
        string $assignment,
        TailorWorkspaceService $service,
    ): JsonResponse {
        $owned = $this->ownedAssignment($request, $assignment);

        $data = $request->validate([
            'status' => ['required', Rule::in(TailorAssignment::STATUSES)],
            'note' => 'nullable|string|max:3000',
        ]);

        $updated = $service->transition(
            $request->user(),
            $owned,
            $data['status'],
            $data['note'] ?? null,
        );

        $fresh = $this->ownedAssignment($request, $updated->uuid);

        return response()->json([
            'data' => [
                ...$this->payload($fresh, true),
                'allowed_statuses' => $service->nextStatuses($fresh),
            ],
        ]);
    }

    public function note(
        Request $request,
        string $locale,
        string $assignment,
        TailorWorkspaceService $service,
    ): JsonResponse {
        $owned = $this->ownedAssignment($request, $assignment);

        $data = $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        $note = $service->addNote($request->user(), $owned, $data['body']);

        return response()->json([
            'data' => [
                'uuid' => $note->uuid,
                'body' => $note->body,
                'created_at' => $note->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    private function ownedAssignment(Request $request, string $uuid): TailorAssignment
    {
        return TailorAssignment::query()
            ->where('tailor_id', $request->user()->id)
            ->where('uuid', $uuid)
            ->with([
                'tailor',
                'assignedBy',
                'tailoringRequest.user',
                'tailoringRequest.product.translations',
                'tailoringRequest.variant',
                'tailoringRequest.orderItem.order',
                'tailoringRequest.orderItem.measurements',
                'notes.author',
                'events.actor',
            ])
            ->firstOrFail();
    }

    private function payload(TailorAssignment $assignment, bool $detailed = false): array
    {
        $assignment->loadMissing([
            'tailoringRequest.user',
            'tailoringRequest.product.translations',
            'tailoringRequest.variant',
            'tailoringRequest.orderItem.order',
            'tailoringRequest.orderItem.measurements',
            'notes.author',
            'events.actor',
        ]);

        $tailoring = $assignment->tailoringRequest;
        $orderItem = $tailoring->orderItem;

        return [
            'uuid' => $assignment->uuid,
            'status' => $assignment->status,
            'assigned_at' => $assignment->assigned_at?->toIso8601String(),
            'accepted_at' => $assignment->accepted_at?->toIso8601String(),
            'started_at' => $assignment->started_at?->toIso8601String(),
            'fitting_at' => $assignment->fitting_at?->toIso8601String(),
            'completed_at' => $assignment->completed_at?->toIso8601String(),
            'tailoring' => [
                'uuid' => $tailoring->uuid,
                'customer_notes' => $orderItem?->tailoring_notes ?? $tailoring->customer_notes,
                'product' => [
                    'sku' => $tailoring->product->sku,
                    'name' => $tailoring->product->translation()?->name,
                ],
                'variant_sku' => $tailoring->variant?->sku,
                'customer' => [
                    'uuid' => $tailoring->user->uuid,
                    'name' => $tailoring->user->name,
                ],
                'order' => $orderItem?->order ? [
                    'uuid' => $orderItem->order->uuid,
                    'number' => $orderItem->order->number,
                ] : null,
            ],
            'measurements' => $detailed
                ? ($orderItem?->measurements ?? collect())->map(fn ($measurement) => [
                    'code' => $measurement->definition_code,
                    'name' => $measurement->definition_name,
                    'value_cm' => (float) $measurement->value_cm,
                ])->values()
                : null,
            'notes' => $detailed
                ? $assignment->notes->map(fn ($note) => [
                    'uuid' => $note->uuid,
                    'body' => $note->body,
                    'author' => $note->author ? [
                        'uuid' => $note->author->uuid,
                        'name' => $note->author->name,
                    ] : null,
                    'created_at' => $note->created_at?->toIso8601String(),
                ])->values()
                : null,
            'events' => $detailed
                ? $assignment->events->map(fn ($event) => [
                    'uuid' => $event->uuid,
                    'type' => $event->type,
                    'from_status' => $event->from_status,
                    'to_status' => $event->to_status,
                    'actor' => $event->actor ? [
                        'uuid' => $event->actor->uuid,
                        'name' => $event->actor->name,
                    ] : null,
                    'metadata' => $event->metadata,
                    'created_at' => $event->created_at?->toIso8601String(),
                ])->values()
                : null,
        ];
    }
}
