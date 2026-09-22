<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TailorAssignment extends Model
{
    public const STATUSES = [
        'assigned',
        'accepted',
        'in_progress',
        'fitting',
        'completed',
        'cancelled',
    ];

    protected $fillable = [
        'uuid',
        'tailoring_request_id',
        'tailor_id',
        'assigned_by_id',
        'status',
        'assigned_at',
        'accepted_at',
        'started_at',
        'fitting_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'fitting_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function tailoringRequest(): BelongsTo
    {
        return $this->belongsTo(TailoringRequest::class);
    }

    public function tailor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tailor_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TailorAssignmentNote::class)->orderBy('created_at');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TailorAssignmentEvent::class)->orderBy('created_at');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
