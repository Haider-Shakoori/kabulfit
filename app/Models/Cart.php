<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Cart extends Model {
 protected $fillable=['uuid','user_id','guest_token','currency'];
 public function user(): BelongsTo{return $this->belongsTo(User::class);}
 public function items(): HasMany{return $this->hasMany(CartItem::class);}
 public function getRouteKeyName(): string{return 'uuid';}
}