<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductMediaDerivative extends Model
{
    protected $fillable = [
        'product_media_id',
        'disk',
        'path',
        'format',
        'width',
        'height',
        'byte_size',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'byte_size' => 'integer',
        ];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(ProductMedia::class, 'product_media_id');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
