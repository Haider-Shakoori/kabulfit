<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class ShipmentEvent extends Model
{
    protected $fillable = ['uuid', 'shipment_id', 'status', 'location', 'description', 'occurred_at'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Shipment events are immutable.'));
        static::deleting(fn () => throw new LogicException('Shipment events are immutable.'));
    }

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
