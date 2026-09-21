<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany,HasOne};
class Order extends Model {
 protected $fillable=['uuid','number','user_id','status','payment_status','currency','subtotal_minor','discount_minor','shipping_minor','total_minor','coupon_code','shipping_method_code','shipping_address','paid_at'];
 protected function casts(): array{return ['subtotal_minor'=>'integer','discount_minor'=>'integer','shipping_minor'=>'integer','total_minor'=>'integer','shipping_address'=>'array','paid_at'=>'datetime'];}
 public function user(): BelongsTo{return $this->belongsTo(User::class);}
 public function items(): HasMany{return $this->hasMany(OrderItem::class);}
 public function payment(): HasOne{return $this->hasOne(Payment::class);}
 public function getRouteKeyName(): string{return 'uuid';}
}