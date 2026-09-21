<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:190'],
            'collection' => ['nullable', 'string', 'max:190'],
            'size' => ['nullable', 'string', 'max:24'],
            'color' => ['nullable', 'string', 'max:190'],
            'min_price' => ['nullable', 'regex:/^\d{1,10}(?:\.\d{1,2})?$/'],
            'max_price' => ['nullable', 'regex:/^\d{1,10}(?:\.\d{1,2})?$/'],
            'in_stock' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(['featured', 'newest', 'price_asc', 'price_desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ];
    }
}
