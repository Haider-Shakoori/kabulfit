<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Payment extends Model {
 protected $fillable=['uuid','order_id','provider','provider_payment_id','status','currency','amount_minor','idempotency_key','failure_message'];
 protected function casts(): array{return ['amount_minor'=>'integer'];}
 public function order(): BelongsTo{return $this->belongsTo(Order::class);}
 public function getRouteKeyName(): string{return 'uuid';}
}