<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderItemMeasurement extends Model {
 protected $fillable=['order_item_id','definition_code','definition_name','value_cm'];
 protected function casts(): array{return ['value_cm'=>'decimal:2'];}
 public function orderItem(): BelongsTo{return $this->belongsTo(OrderItem::class);}
}