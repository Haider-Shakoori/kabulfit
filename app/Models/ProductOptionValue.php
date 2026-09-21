<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOptionValue extends Model
{
    protected $fillable = ['product_option_id', 'code', 'sort_order'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductOptionValueTranslation::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_option_values');
    }

    public function translation(?string $locale = null): ?ProductOptionValueTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }
}
