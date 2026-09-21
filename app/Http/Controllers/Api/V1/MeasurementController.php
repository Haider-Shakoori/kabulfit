<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeasurementProfileRequest;
use App\Models\MeasurementDefinition;
use App\Models\MeasurementProfile;
use App\Services\Measurements\MeasurementProfileService;
use App\Support\Measurements\MeasurementConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function definitions(Request $request): JsonResponse
    {
        $data = $request->validate(['garment_type' => 'required|in:perahan_tunban,dress,waistcoat', 'unit' => 'nullable|in:cm,in']);
        $unit = $data['unit'] ?? 'cm';
        $definitions = MeasurementDefinition::where('garment_type', $data['garment_type'])->where('is_active', true)->with('translations')->orderBy('sort_order')->get();

        return response()->json(['data' => $definitions->map(fn ($definition) => [
            'uuid' => $definition->uuid,
            'code' => $definition->code,
            'name' => $definition->translation()?->name,
            'instructions' => $definition->translation()?->instructions,
            'guide_image' => $definition->translation()?->guide_image_path,
            'required' => $definition->is_required,
            'min' => MeasurementConverter::fromCm($definition->min_cm, $unit),
            'max' => MeasurementConverter::fromCm($definition->max_cm, $unit),
            'step' => MeasurementConverter::fromCm($definition->step_cm, $unit),
            'unit' => $unit,
        ])->values()]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->measurementProfiles()->with('values.definition.translations')->get()->map(fn ($profile) => $this->profileData($profile))]);
    }

    public function store(MeasurementProfileRequest $request, MeasurementProfileService $service): JsonResponse
    {
        return response()->json(['data' => $this->profileData($service->save($request->user(), $request->validated()))], 201);
    }

    public function update(MeasurementProfileRequest $request, MeasurementProfile $profile, MeasurementProfileService $service): JsonResponse
    {
        return response()->json(['data' => $this->profileData($service->save($request->user(), $request->validated(), $profile))]);
    }

    public function destroy(Request $request, MeasurementProfile $profile): JsonResponse
    {
        abort_unless($profile->user_id === $request->user()->id, 404);
        $profile->delete();

        return response()->json([], 204);
    }

    private function profileData(MeasurementProfile $profile): array
    {
        $profile->loadMissing('values.definition.translations');

        return [
            'uuid' => $profile->uuid,
            'name' => $profile->name,
            'garment_type' => $profile->garment_type,
            'display_unit' => $profile->display_unit,
            'is_default' => $profile->is_default,
            'measurements' => $profile->values->map(fn ($value) => [
                'code' => $value->definition->code,
                'name' => $value->definition->translation()?->name,
                'value' => MeasurementConverter::fromCm($value->value_cm, $profile->display_unit),
                'unit' => $profile->display_unit,
            ])->values(),
        ];
    }
}
