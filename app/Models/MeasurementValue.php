<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementValue extends Model
{
    protected $fillable = ['measurement_profile_id', 'measurement_definition_id', 'value_cm'];

    protected function casts(): array
    {
        return ['value_cm' => 'decimal:2'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MeasurementProfile::class, 'measurement_profile_id');
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(MeasurementDefinition::class, 'measurement_definition_id');
    }
}
