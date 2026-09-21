<?php

namespace App\Services\Measurements;

use App\Models\MeasurementDefinition;
use App\Models\MeasurementProfile;
use App\Models\User;
use App\Support\Measurements\MeasurementConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MeasurementProfileService
{
    public function save(User $user, array $data, ?MeasurementProfile $profile = null): MeasurementProfile
    {
        return DB::transaction(function () use ($user, $data, $profile): MeasurementProfile {
            if ($profile && $profile->user_id !== $user->id) {
                abort(404);
            }

            $definitions = MeasurementDefinition::query()
                ->where('garment_type', $data['garment_type'])
                ->where('is_active', true)
                ->with('translations')
                ->orderBy('sort_order')
                ->get()
                ->keyBy('code');

            $values = collect($data['measurements'] ?? [])->keyBy('code');
            $errors = [];

            foreach ($definitions as $code => $definition) {
                $input = $values->get($code);
                if ($definition->is_required && ($input === null || ! isset($input['value']))) {
                    $errors["measurements.$code"] = __('measurements.required_value', ['name' => $definition->translation()?->name ?? $code]);

                    continue;
                }
                if ($input === null || ! isset($input['value'])) {
                    continue;
                }

                $cm = (float) MeasurementConverter::toCm($input['value'], $data['display_unit']);
                if ($cm < (float) $definition->min_cm || $cm > (float) $definition->max_cm) {
                    $errors["measurements.$code"] = __('measurements.out_of_range', [
                        'name' => $definition->translation()?->name ?? $code,
                        'min' => MeasurementConverter::fromCm($definition->min_cm, $data['display_unit']),
                        'max' => MeasurementConverter::fromCm($definition->max_cm, $data['display_unit']),
                        'unit' => $data['display_unit'],
                    ]);
                }
            }

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }

            $profile ??= new MeasurementProfile(['uuid' => (string) Str::uuid(), 'user_id' => $user->id]);
            $profile->fill([
                'name' => $data['name'],
                'garment_type' => $data['garment_type'],
                'display_unit' => $data['display_unit'],
                'is_default' => (bool) ($data['is_default'] ?? false),
            ])->save();

            if ($profile->is_default) {
                MeasurementProfile::where('user_id', $user->id)->whereKeyNot($profile->id)->update(['is_default' => false]);
            }

            $profile->values()->delete();
            foreach ($definitions as $code => $definition) {
                $input = $values->get($code);
                if ($input !== null && isset($input['value'])) {
                    $profile->values()->create([
                        'measurement_definition_id' => $definition->id,
                        'value_cm' => MeasurementConverter::toCm($input['value'], $data['display_unit']),
                    ]);
                }
            }

            return $profile->load('values.definition.translations');
        });
    }
}
