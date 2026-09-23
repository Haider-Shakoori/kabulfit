<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'price_minor', 'sale_price_minor', 'currency',
        'stock_quantity', 'is_active', 'is_featured', 'sort_order',
        'tailoring_enabled', 'measurement_garment_type',
    ];

    protected function casts(): array
    {
        return [
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'tailoring_enabled' => 'boolean',
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
        return $this->belongsToMany(Collection::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    public function primaryMedia(): HasOne
    {
        return $this->hasOne(ProductMedia::class)
            ->where('is_primary', true)
            ->orderBy('sort_order');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'related_products',
            'product_id',
            'related_product_id',
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function translation(?string $locale = null): ?ProductTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('kabulfit.default_locale'));
    }

    public function currentPriceMinor(): int
    {
        return $this->sale_price_minor ?? $this->price_minor;
    }

    public function decimalPrice(?int $minorUnits = null): string
    {
        $minorUnits ??= $this->currentPriceMinor();
        $major = intdiv($minorUnits, 100);
        $minor = $minorUnits % 100;

        return $major.'.'.str_pad((string) $minor, 2, '0', STR_PAD_LEFT);
    }

    public function formattedPrice(?int $minorUnits = null): string
    {
        [$major, $minor] = explode('.', $this->decimalPrice($minorUnits), 2);

        $amount = number_format((int) $major).'.'.$minor;

        return $this->currency === 'USD' ? '$'.$amount : $amount.' '.$this->currency;
    }

    public function availableStock(): int
    {
        if ($this->relationLoaded('variants')) {
            return (int) $this->variants
                ->where('is_active', true)
                ->sum(fn (ProductVariant $variant): int => $variant->availableQuantity());
        }

        $variantCount = $this->variants()->where('is_active', true)->count();

        if ($variantCount === 0) {
            return $this->stock_quantity;
        }

        return (int) InventoryItem::query()
            ->whereHas('variant', fn ($query) => $query
                ->where('product_id', $this->id)
                ->where('is_active', true))
            ->get()
            ->sum(fn (InventoryItem $inventory): int => $inventory->availableQuantity());
    }

    public function isInStock(): bool
    {
        return $this->availableStock() > 0;
    }
}
