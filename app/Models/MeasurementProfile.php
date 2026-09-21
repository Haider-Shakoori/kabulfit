<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementProfile extends Model
{
    protected $fillable = ['uuid', 'user_id', 'name', 'garment_type', 'display_unit', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(MeasurementValue::class);
    }

    public function tailoringRequests(): HasMany
    {
        return $this->hasMany(TailoringRequest::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
