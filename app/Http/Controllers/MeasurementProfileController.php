<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeasurementProfileRequest;
use App\Models\MeasurementDefinition;
use App\Models\MeasurementProfile;
use App\Services\Measurements\MeasurementProfileService;
use App\Support\Measurements\MeasurementConverter;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeasurementProfileController extends Controller
{
    public function index(Request $request): View
    {
        $profiles = $request->user()
            ->measurementProfiles()
            ->with('values.definition.translations')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('measurements.index', [
            'profiles' => $profiles,
            'seo' => PrivatePageSeo::make(
                __('measurements.profiles'),
                route('measurements.index', ['locale' => app()->getLocale()]),
            ),
        ]);
    }

    public function create(Request $request): View
    {
        $garmentType = $request->string('garment_type')->toString() ?: 'perahan_tunban';
        $unit = $request->string('unit')->toString() ?: 'cm';

        abort_unless(in_array($garmentType, ['perahan_tunban', 'dress', 'waistcoat'], true), 404);
        abort_unless(in_array($unit, ['cm', 'in'], true), 404);

        return $this->formView(null, $garmentType, $unit);
    }

    public function store(MeasurementProfileRequest $request, MeasurementProfileService $service): RedirectResponse
    {
        $service->save($request->user(), $request->validated());

        return redirect()
            ->route('measurements.index', ['locale' => app()->getLocale()])
            ->with('status', __('measurements.saved'));
    }

    public function edit(Request $request, MeasurementProfile $profile): View
    {
        abort_unless($profile->user_id === $request->user()->id, 404);
        $profile->load('values.definition.translations');

        $unit = $request->string('unit')->toString() ?: $profile->display_unit;
        abort_unless(in_array($unit, ['cm', 'in'], true), 404);

        return $this->formView($profile, $profile->garment_type, $unit);
    }

    public function update(
        MeasurementProfileRequest $request,
        MeasurementProfile $profile,
        MeasurementProfileService $service,
    ): RedirectResponse {
        $service->save($request->user(), $request->validated(), $profile);

        return redirect()
            ->route('measurements.index', ['locale' => app()->getLocale()])
            ->with('status', __('measurements.saved'));
    }

    public function destroy(
        Request $request,
        MeasurementProfile $profile,
        MeasurementProfileService $service,
    ): RedirectResponse {
        $service->delete($request->user(), $profile);

        return redirect()
            ->route('measurements.index', ['locale' => app()->getLocale()])
            ->with('status', __('measurements.deleted'));
    }

    private function formView(?MeasurementProfile $profile, string $garmentType, string $unit): View
    {
        $definitions = MeasurementDefinition::query()
            ->where('garment_type', $garmentType)
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $values = $profile?->values
            ->mapWithKeys(fn ($value) => [
                $value->definition->code => MeasurementConverter::fromCm($value->value_cm, $unit),
            ])
            ->all() ?? [];

        return view('measurements.form', [
            'profile' => $profile,
            'definitions' => $definitions,
            'values' => $values,
            'garmentType' => $garmentType,
            'unit' => $unit,
            'seo' => PrivatePageSeo::make(
                $profile ? __('measurements.edit_profile') : __('measurements.new_profile'),
                $profile
                    ? route('measurements.edit', ['locale' => app()->getLocale(), 'profile' => $profile])
                    : route('measurements.create', ['locale' => app()->getLocale()]),
            ),
        ]);
    }
}
