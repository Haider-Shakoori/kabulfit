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
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:180'],
            'collection' => ['nullable', 'string', 'max:180'],
            'size' => ['nullable', 'string', 'max:64'],
            'color' => ['nullable', 'string', 'max:64'],
            'embroidery' => ['nullable', 'string', 'max:64'],
            'in_stock' => ['nullable', 'boolean'],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0', 'gte:price_min'],
            'price_min_minor' => ['nullable', 'integer', 'min:0'],
            'price_max_minor' => ['nullable', 'integer', 'min:0', 'gte:price_min_minor'],
            'sort' => ['nullable', Rule::in(['featured', 'newest', 'price_asc', 'price_desc', 'name'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ];
    }

    public function catalogFilters(bool $api = false): array
    {
        $validated = $this->validated();

        if ($api) {
            $validated['price_min_minor'] = $validated['price_min_minor'] ?? null;
            $validated['price_max_minor'] = $validated['price_max_minor'] ?? null;
        } else {
            $validated['price_min_minor'] = isset($validated['price_min']) ? $validated['price_min'] * 100 : null;
            $validated['price_max_minor'] = isset($validated['price_max']) ? $validated['price_max'] * 100 : null;
        }

        unset($validated['price_min'], $validated['price_max']);

        return $validated;
    }
}
