<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeasurementProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'garment_type' => ['required', Rule::in(['perahan_tunban', 'dress', 'waistcoat'])],
            'display_unit' => ['required', Rule::in(['cm', 'in'])],
            'is_default' => ['sometimes', 'boolean'],
            'measurements' => ['required', 'array'],
            'measurements.*.code' => ['required', 'string', 'max:80', 'distinct'],
            'measurements.*.value' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
