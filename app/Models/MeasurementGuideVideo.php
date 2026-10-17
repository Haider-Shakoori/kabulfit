<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementGuideVideo extends Model
{
    protected $fillable = [
        'measurement_field',
        'category',
        'title',
        'video_url',
        'video_url_dari',
        'video_url_pashto',
        'description',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function localizedVideoUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return match ($locale) {
            'fa' => $this->video_url_dari ?: $this->video_url,
            'ps' => $this->video_url_pashto ?: $this->video_url,
            default => $this->video_url,
        };
    }
}
