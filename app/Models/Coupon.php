<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Coupon extends Model {
 protected $fillable=['code','type','value','minimum_subtotal_minor','maximum_discount_minor','usage_limit','times_used','starts_at','ends_at','is_active'];
 protected function casts(): array{return ['value'=>'integer','minimum_subtotal_minor'=>'integer','maximum_discount_minor'=>'integer','usage_limit'=>'integer','times_used'=>'integer','starts_at'=>'datetime','ends_at'=>'datetime','is_active'=>'boolean'];}
 public function discountFor(int $subtotal): int {
  if(!$this->is_active || $subtotal<$this->minimum_subtotal_minor || ($this->starts_at && $this->starts_at->isFuture()) || ($this->ends_at && $this->ends_at->isPast()) || ($this->usage_limit!==null && $this->times_used >= $this->usage_limit)) return 0;
  $discount=$this->type==='percent'?intdiv($subtotal*$this->value,100):$this->value;
  if($this->maximum_discount_minor!==null)$discount=min($discount,$this->maximum_discount_minor);
  return min($discount,$subtotal);
 }
}