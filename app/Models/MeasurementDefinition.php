<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementDefinition extends Model
{
    protected $fillable = [
        'uuid',
        'code',
        'garment_type',
        'min_cm',
        'max_cm',
        'step_cm',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_cm' => 'decimal:2',
            'max_cm' => 'decimal:2',
            'step_cm' => 'decimal:2',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MeasurementDefinitionTranslation::class);
    }

    public function translation(?string $locale = null): ?MeasurementDefinitionTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
