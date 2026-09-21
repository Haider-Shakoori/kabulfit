<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'name', 'slug', 'short_description', 'description', 'seo_title', 'seo_description'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
