<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = ['weight_ranges', 'effective_date', 'is_active', 'notes'];

    protected function casts(): array
    {
        return [
            'weight_ranges' => 'array',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
