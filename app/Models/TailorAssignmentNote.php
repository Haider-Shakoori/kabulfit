<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class TailorAssignmentNote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'tailor_assignment_id',
        'author_id',
        'body',
        'created_at',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Tailor assignment notes are immutable.'));
        static::deleting(fn () => throw new LogicException('Tailor assignment notes are immutable.'));
    }

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TailorAssignment::class, 'tailor_assignment_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
