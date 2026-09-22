<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeasurementProfileRequest;
use App\Models\MeasurementDefinition;
use App\Models\MeasurementProfile;
use App\Services\Measurements\MeasurementProfileService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeasurementController extends Controller
{
    public function index(Request $request): View
    {
        return view('measurements.index', ['profiles' => $request->user()->measurementProfiles()->with('values.definition.translations')->get(), 'definitions' => MeasurementDefinition::where('is_active', true)->with('translations')->orderBy('garment_type')->orderBy('sort_order')->get(), 'seo' => PrivatePageSeo::make(__('measurements.profiles'), route('measurements.index', ['locale' => app()->getLocale()]))]);
    }

    public function store(MeasurementProfileRequest $request, MeasurementProfileService $service): RedirectResponse
    {
        $service->save($request->user(), $request->validated());

        return back()->with('status', __('measurements.saved'));
    }

    public function update(MeasurementProfileRequest $request, MeasurementProfile $profile, MeasurementProfileService $service): RedirectResponse
    {
        $service->save($request->user(), $request->validated(), $profile);

        return back()->with('status', __('measurements.saved'));
    }

    public function destroy(Request $request, MeasurementProfile $profile): RedirectResponse
    {
        abort_unless($profile->user_id === $request->user()->id, 404);
        $profile->delete();

        return back()->with('status',__('measurements.deleted'));
    }
}
