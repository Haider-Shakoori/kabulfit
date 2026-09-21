<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementDefinitionTranslation extends Model
{
    protected $fillable = [
        'measurement_definition_id',
        'locale',
        'name',
        'instructions',
        'guide_image_path',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(MeasurementDefinition::class, 'measurement_definition_id');
    }
}
