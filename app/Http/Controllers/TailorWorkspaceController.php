<?php

namespace App\Http\Controllers;

use App\Models\TailorAssignment;
use App\Services\Tailoring\TailorWorkspaceService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TailorWorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TailorAssignment::class);

        $data = $request->validate([
            'status' => ['nullable', Rule::in(TailorAssignment::STATUSES)],
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

        return view('tailor.index', [
            'assignments' => $query->paginate(30)->withQueryString(),
            'selectedStatus' => $data['status'] ?? null,
            'statuses' => TailorAssignment::STATUSES,
            'seo' => PrivatePageSeo::make(
                __('tailor.dashboard'),
                route('tailor.index', ['locale' => app()->getLocale()]),
            ),
        ]);
    }

    public function show(
        string $locale,
        TailorAssignment $assignment,
        TailorWorkspaceService $service,
    ): View {
        $this->authorize('view', $assignment);

        $assignment->load([
            'tailor',
            'assignedBy',
            'tailoringRequest.user',
            'tailoringRequest.product.translations',
            'tailoringRequest.variant',
            'tailoringRequest.orderItem.order',
            'tailoringRequest.orderItem.measurements',
            'notes.author',
            'events.actor',
        ]);

        return view('tailor.show', [
            'assignment' => $assignment,
            'nextStatuses' => $service->nextStatuses($assignment),
            'seo' => PrivatePageSeo::make(
                __('tailor.assignment'),
                route('tailor.show', [
                    'locale' => app()->getLocale(),
                    'assignment' => $assignment,
                ]),
            ),
        ]);
    }

    public function transition(
        Request $request,
        string $locale,
        TailorAssignment $assignment,
        TailorWorkspaceService $service,
    ): RedirectResponse {
        $this->authorize('update', $assignment);

        $data = $request->validate([
            'status' => ['required', Rule::in(TailorAssignment::STATUSES)],
            'note' => 'nullable|string|max:3000',
        ]);

        $service->transition(
            $request->user(),
            $assignment,
            $data['status'],
            $data['note'] ?? null,
        );

        return back()->with('status', __('tailor.status_updated'));
    }

    public function note(
        Request $request,
        string $locale,
        TailorAssignment $assignment,
        TailorWorkspaceService $service,
    ): RedirectResponse {
        $this->authorize('update', $assignment);

        $data = $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        $service->addNote($request->user(), $assignment, $data['body']);

        return back()->with('status', __('tailor.note_added'));
    }
}
