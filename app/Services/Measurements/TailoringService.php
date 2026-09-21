<?php

namespace App\Services\Measurements;

use App\Models\Cart;
use App\Models\MeasurementProfile;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Services\Commerce\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TailoringService
{
    public function __construct(private readonly CartService $carts) {}

    public function addToCart(
        User $user,
        Product $product,
        ?ProductVariant $variant,
        MeasurementProfile $profile,
        ?string $notes = null,
    ): array {
        if (! $product->tailoring_enabled || $profile->user_id !== $user->id) {
            abort(404);
        }

        if ($profile->garment_type !== $product->measurement_garment_type) {
            throw ValidationException::withMessages([
                'measurement_profile_uuid' => __('measurements.profile_type_mismatch'),
            ]);
        }

        if ($variant && $variant->product_id !== $product->id) {
            throw ValidationException::withMessages(['variant_sku' => __('commerce.invalid_item')]);
        }

        return DB::transaction(function () use ($user, $product, $variant, $profile, $notes): array {
            $tailoring = TailoringRequest::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'measurement_profile_id' => $profile->id,
                'status' => 'ready',
                'customer_notes' => $notes,
            ]);

            $cart = $this->carts->add(
                $this->carts->forUser($user),
                $product,
                $variant,
                1,
                $tailoring,
            );

            return ['tailoring' => $tailoring, 'cart' => $cart];
        }, 3);
    }
}
