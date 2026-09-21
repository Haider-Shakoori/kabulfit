<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColorTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'name', 'slug'];

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }
}
