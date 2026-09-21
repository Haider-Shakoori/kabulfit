<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
            'sort_order' => 'integer',
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

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(CatalogCollection::class, 'collection_product', 'product_id', 'collection_id')
            ->withPivot('sort_order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function translation(?string $locale = null): ?ProductTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function primaryMedia(): ?ProductMedia
    {
        if (! $this->relationLoaded('media')) {
            return $this->media()->with('translations')->first();
        }

        return $this->media->firstWhere('is_primary', true) ?? $this->media->first();
    }

    public function currentPriceMinor(): int
    {
        return $this->sale_price_minor ?? $this->price_minor;
    }

    public function decimalPrice(): string
    {
        return self::minorToDecimal($this->currentPriceMinor());
    }

    public function formattedPrice(): string
    {
        [$major, $minor] = explode('.', $this->decimalPrice(), 2);

        return number_format((int) $major).'.'.$minor.' '.$this->currency;
    }

    public function availableStock(): int
    {
        if (! $this->relationLoaded('variants') || $this->variants->isEmpty()) {
            return $this->stock_quantity;
        }

        return $this->variants
            ->where('is_active', true)
            ->sum(fn (ProductVariant $variant): int => $variant->availableStock());
    }

    public static function minorToDecimal(int $amount): string
    {
        $major = intdiv($amount, 100);
        $minor = $amount % 100;

        return $major.'.'.str_pad((string) $minor, 2, '0', STR_PAD_LEFT);
    }
}
