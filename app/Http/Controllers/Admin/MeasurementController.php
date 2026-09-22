<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeasurementDefinition;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeasurementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MeasurementDefinition::class);

        return view('admin.measurements.index', [
            'definitions' => MeasurementDefinition::query()->with('translations')->orderBy('garment_type')->orderBy('sort_order')->get(),
            'seo' => PrivatePageSeo::make('Admin Measurements', route('admin.measurements.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, string $locale, MeasurementDefinition $definition, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $definition);
        $data = $request->validate([
            'min_cm' => 'required|numeric|min:0|max:500',
            'max_cm' => 'required|numeric|gt:min_cm|max:500',
            'step_cm' => 'required|numeric|min:0.01|max:10',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.instructions' => 'nullable|string|max:2000',
        ]);

        $before = $definition->only(['min_cm', 'max_cm', 'step_cm', 'is_required', 'is_active']);
        $definition->update([
            'min_cm' => $data['min_cm'],
            'max_cm' => $data['max_cm'],
            'step_cm' => $data['step_cm'],
            'is_required' => (bool) ($data['is_required'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        foreach (config('kabulfit.supported_locales') as $translationLocale) {
            if (! isset($data['translations'][$translationLocale])) {
                continue;
            }

            $definition->translations()->updateOrCreate(
                ['locale' => $translationLocale],
                [
                    'name' => $data['translations'][$translationLocale]['name'],
                    'instructions' => $data['translations'][$translationLocale]['instructions'] ?? null,
                ],
            );
        }

        $audit->record($request->user(), 'measurement.updated', $definition, ['code' => $definition->code, 'before' => $before, 'after' => $definition->fresh()->only(array_keys($before))]);

        return back()->with('status', 'Measurement definition updated.');
    }
}
