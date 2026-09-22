<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentPage extends Model
{
    protected $fillable = ['uuid', 'page_key', 'is_published', 'sort_order'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ContentPageTranslation::class);
    }

    public function translation(?string $locale = null): ?ContentPageTranslation
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
