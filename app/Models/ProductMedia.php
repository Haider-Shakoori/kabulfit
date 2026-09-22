<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductMedia extends Model
{
    use HasFactory;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id', 'path', 'mime_type', 'width', 'height', 'sort_order', 'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'is_primary' => 'boolean',
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

    public function derivatives(): HasMany
    {
        return $this->hasMany(ProductMediaDerivative::class)
            ->orderBy('format')
            ->orderBy('width');
    }

    public function responsiveSources(): array
    {
        $derivatives = $this->relationLoaded('derivatives')
            ? $this->derivatives
            : $this->derivatives()->get();

        return [
            'avif' => $derivatives
                ->where('format', 'avif')
                ->map(fn (ProductMediaDerivative $item): string => $item->url().' '.$item->width.'w')
                ->implode(', '),
            'webp' => $derivatives
                ->where('format', 'webp')
                ->map(fn (ProductMediaDerivative $item): string => $item->url().' '.$item->width.'w')
                ->implode(', '),
        ];
    }

    public function translation(?string $locale = null): ?ProductMediaTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function url(): string
    {
        return asset($this->path);
    }
}
