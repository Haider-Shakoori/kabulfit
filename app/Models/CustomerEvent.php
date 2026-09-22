<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class CustomerEvent extends Model
{
    protected $fillable = ['uuid', 'user_id', 'order_id', 'type', 'payload', 'occurred_at'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Customer events are immutable.'));
        static::deleting(fn () => throw new LogicException('Customer events are immutable.'));
    }

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
