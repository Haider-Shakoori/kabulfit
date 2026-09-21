<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProductMedia extends Model
{
    protected $table = 'product_media';

    protected $fillable = [
        'disk', 'path', 'mime_type', 'width', 'height', 'is_primary', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductMediaTranslation::class);
    }

    public function translation(?string $locale = null): ?ProductMediaTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function url(): string
    {
        if ($this->disk === 'public') {
            return asset($this->path);
        }

        return Storage::disk($this->disk)->url($this->path);
    }
}
