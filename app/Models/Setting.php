<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['uuid', 'group', 'key', 'value'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
