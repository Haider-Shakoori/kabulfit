<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'name', 'slug', 'description', 'seo_title', 'seo_description'];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
