<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    protected $fillable = ['quantity_on_hand', 'reserved_quantity', 'low_stock_threshold'];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'integer',
            'reserved_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function available(): int
    {
        return max(0, $this->quantity_on_hand - $this->reserved_quantity);
    }
}
