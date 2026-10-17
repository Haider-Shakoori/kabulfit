<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeasurementGuideVideo;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MeasurementGuideController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('measurements.manage'), 403);

        return view('admin.measurement-guides.index', [
            'guides' => MeasurementGuideVideo::query()
                ->orderBy('category')
                ->orderBy('display_order')
                ->orderBy('id')
                ->get(),
            'seo' => PrivatePageSeo::make('Admin Measurement Guides', route('admin.measurement-guides.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('measurements.manage'), 403);
        $guide = MeasurementGuideVideo::query()->create($this->validated($request));

        $audit->record($request->user(), 'measurement_guide.created', $guide, ['measurement_guide_id' => $guide->id]);

        return back()->with('status', 'Measurement guide created.');
    }

    public function update(Request $request, string $locale, MeasurementGuideVideo $measurementGuide, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('measurements.manage'), 403);
        $before = $measurementGuide->getAttributes();
        $measurementGuide->update($this->validated($request));

        $audit->record($request->user(), 'measurement_guide.updated', $measurementGuide, [
            'measurement_guide_id' => $measurementGuide->id,
            'before' => $before,
            'after' => $measurementGuide->fresh()->getAttributes(),
        ]);

        return back()->with('status', 'Measurement guide updated.');
    }

    public function destroy(Request $request, string $locale, MeasurementGuideVideo $measurementGuide, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('measurements.manage'), 403);
        $audit->record($request->user(), 'measurement_guide.deleted', $measurementGuide, ['measurement_guide_id' => $measurementGuide->id]);
        $measurementGuide->delete();

        return back()->with('status', 'Measurement guide deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'measurement_field' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(['male', 'female', 'child'])],
            'title' => ['nullable', 'string', 'max:255'],
            'video_url' => ['required', 'url', 'max:2048'],
            'video_url_dari' => ['nullable', 'url', 'max:2048'],
            'video_url_pashto' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:5000'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
