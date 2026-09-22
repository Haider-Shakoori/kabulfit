<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TailoringRequest;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TailoringController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TailoringRequest::class);

        return view('admin.tailoring.index', [
            'requests' => TailoringRequest::query()->with(['user', 'product.translations', 'measurementProfile'])->latest()->paginate(30),
            'seo' => PrivatePageSeo::make('Admin Tailoring', route('admin.tailoring.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(string $locale, TailoringRequest $tailoring): View
    {
        $this->authorize('view', $tailoring);

        return view('admin.tailoring.show', [
            'tailoring' => $tailoring->load(['user', 'product.translations', 'variant', 'measurementProfile.values.definition.translations', 'orderItem.order', 'orderItem.measurements']),
            'seo' => PrivatePageSeo::make('Admin Tailoring Request', route('admin.tailoring.show', ['locale' => app()->getLocale(), 'tailoring' => $tailoring])),
        ]);
    }

    public function cancel(Request $request, string $locale, TailoringRequest $tailoring, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $tailoring);

        if ($tailoring->status !== 'ready') {
            throw ValidationException::withMessages(['tailoring' => 'Only an un-ordered ready tailoring request can be cancelled here.']);
        }

        $tailoring->update(['status' => 'cancelled']);
        $audit->record($request->user(), 'tailoring.cancelled', $tailoring, ['tailoring_uuid' => $tailoring->uuid]);

        return back()->with('status', 'Tailoring request cancelled.');
    }
}
