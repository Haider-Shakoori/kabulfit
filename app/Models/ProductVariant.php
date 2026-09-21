<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    protected $fillable = [
        'sku', 'price_minor', 'sale_price_minor', 'currency', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_variant_option_values');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(InventoryStock::class);
    }

    public function effectivePriceMinor(): int
    {
        return $this->sale_price_minor
            ?? $this->price_minor
            ?? $this->product->sale_price_minor
            ?? $this->product->price_minor;
    }

    public function availableStock(): int
    {
        if (! $this->inventory) {
            return 0;
        }

        return max(0, $this->inventory->quantity_on_hand - $this->inventory->reserved_quantity);
    }
}
