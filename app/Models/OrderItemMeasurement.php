<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class OrderItemMeasurement extends Model
{
    protected $fillable = ['order_item_id', 'definition_code', 'definition_name', 'value_cm'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Order item measurement snapshots are immutable.'));
        static::deleting(fn () => throw new LogicException('Order item measurement snapshots are immutable.'));
    }

    protected function casts(): array
    {
        return ['value_cm' => 'decimal:2'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
