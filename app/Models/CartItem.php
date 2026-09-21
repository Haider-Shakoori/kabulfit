<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['uuid', 'cart_id', 'product_id', 'product_variant_id', 'quantity'];

    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function unitPriceMinor(): int
    {
        return $this->variant?->currentPriceMinor() ?? $this->product->currentPriceMinor();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function lineTotalMinor(): int
    {
        return $this->unitPriceMinor() * $this->quantity;
    }
}
