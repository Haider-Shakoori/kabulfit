<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'price_minor', 'sale_price_minor', 'currency',
        'stock_quantity', 'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation(?string $locale = null): ?ProductTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function decimalPrice(): string
    {
        $major = intdiv($this->price_minor, 100);
        $minor = $this->price_minor % 100;

        return $major.'.'.str_pad((string) $minor, 2, '0', STR_PAD_LEFT);
    }

    public function formattedPrice(): string
    {
        [$major, $minor] = explode('.', $this->decimalPrice(), 2);

        return number_format((int) $major).'.'.$minor.' '.$this->currency;
    }
}
