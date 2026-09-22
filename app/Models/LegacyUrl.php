<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyUrl extends Model
{
    protected $table = 'legacy_url_inventory';

    protected $fillable = [
        'uuid', 'legacy_path', 'query_key', 'query_value', 'disposition',
        'target_path', 'is_active', 'verified_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'verified_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
