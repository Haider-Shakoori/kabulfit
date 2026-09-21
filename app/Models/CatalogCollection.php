<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogCollection extends Model
{
    use HasFactory;

    protected $table = 'collections';

    protected $fillable = ['is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CollectionTranslation::class, 'collection_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_product', 'collection_id', 'product_id')
            ->withPivot('sort_order');
    }

    public function translation(?string $locale = null): ?CollectionTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }
}
