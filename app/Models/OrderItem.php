<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderItem extends Model {
 protected $fillable=['order_id','product_id','product_variant_id','sku','name','variant_label','unit_price_minor','quantity','line_total_minor'];
 protected function casts(): array{return ['unit_price_minor'=>'integer','quantity'=>'integer','line_total_minor'=>'integer'];}
 public function order(): BelongsTo{return $this->belongsTo(Order::class);}
}