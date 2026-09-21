<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TailoringRequest extends Model {
 protected $fillable=['uuid','user_id','product_id','product_variant_id','measurement_profile_id','status','customer_notes'];
 public function user(): BelongsTo{return $this->belongsTo(User::class);}
 public function product(): BelongsTo{return $this->belongsTo(Product::class);}
 public function variant(): BelongsTo{return $this->belongsTo(ProductVariant::class,'product_variant_id');}
 public function measurementProfile(): BelongsTo{return $this->belongsTo(MeasurementProfile::class);}
 public function getRouteKeyName(): string{return 'uuid';}
}