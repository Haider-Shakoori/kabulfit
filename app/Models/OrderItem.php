<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_variant_id', 'tailoring_request_uuid',
        'is_custom_tailored', 'measurement_profile_name', 'tailoring_notes',
        'sku', 'name', 'variant_label',
        'unit_price_minor', 'quantity', 'line_total_minor',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_minor' => 'integer',
            'quantity' => 'integer',
            'line_total_minor' => 'integer',
            'is_custom_tailored' => 'boolean',
        ];
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(OrderItemMeasurement::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(OrderItemMeasurement::class);
    }
}
