<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class TailorAssignmentEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'tailor_assignment_id',
        'actor_id',
        'type',
        'from_status',
        'to_status',
        'metadata',
        'created_at',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Tailor assignment events are immutable.'));
        static::deleting(fn () => throw new LogicException('Tailor assignment events are immutable.'));
    }

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TailorAssignment::class, 'tailor_assignment_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
