<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeGuide extends Model
{
    protected $fillable = [
        'title',
        'title_pashto',
        'title_dari',
        'category',
        'subcategory',
        'image_url',
        'image_url_pashto',
        'image_url_dari',
        'description',
        'description_pashto',
        'description_dari',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return match ($locale) {
            'fa' => $this->title_dari ?: $this->title,
            'ps' => $this->title_pashto ?: $this->title,
            default => $this->title,
        };
    }

    public function localizedImageUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return match ($locale) {
            'fa' => $this->image_url_dari ?: $this->image_url,
            'ps' => $this->image_url_pashto ?: $this->image_url,
            default => $this->image_url,
        };
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return match ($locale) {
            'fa' => $this->description_dari ?: $this->description,
            'ps' => $this->description_pashto ?: $this->description,
            default => $this->description,
        };
    }
}
