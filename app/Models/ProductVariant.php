<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'size_id', 'color_id', 'option_key', 'sku', 'barcode',
        'price_minor', 'sale_price_minor', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function currentPriceMinor(): int
    {
        return $this->sale_price_minor
            ?? $this->price_minor
            ?? $this->product->currentPriceMinor();
    }

    public function availableQuantity(): int
    {
        return $this->inventory?->availableQuantity() ?? 0;
    }
}
