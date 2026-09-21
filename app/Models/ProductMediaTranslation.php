<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMediaTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'alt_text'];

    public function media(): BelongsTo
    {
        return $this->belongsTo(ProductMedia::class, 'product_media_id');
    }
}
