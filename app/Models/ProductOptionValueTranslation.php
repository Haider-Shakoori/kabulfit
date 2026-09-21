<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionValueTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'name'];

    public function value(): BelongsTo
    {
        return $this->belongsTo(ProductOptionValue::class, 'product_option_value_id');
    }
}
