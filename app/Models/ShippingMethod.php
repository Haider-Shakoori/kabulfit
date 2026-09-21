<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShippingMethod extends Model {
 protected $fillable=['code','name','currency','price_minor','is_active','sort_order'];
 protected function casts(): array{return ['price_minor'=>'integer','is_active'=>'boolean'];}
}