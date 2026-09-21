<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends Model
{
    protected $fillable = ['code', 'is_filterable', 'sort_order'];

    protected function casts(): array
    {
        return ['is_filterable' => 'boolean', 'sort_order' => 'integer'];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductOptionTranslation::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }

    public function translation(?string $locale = null): ?ProductOptionTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }
}
