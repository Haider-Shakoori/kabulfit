<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'country_code' => strtoupper((string) $this->input('country_code', 'AF')),
        ]);
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:32'],
            'country_code' => ['required', 'string', 'size:2', 'alpha'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:32'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
