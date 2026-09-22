<?php

namespace App\Services\Commerce;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TailoringRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function forUser(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['uuid' => (string) Str::uuid(), 'currency' => config('kabulfit.default_currency')],
        );
    }

    public function add(
        Cart $cart,
        Product $product,
        ?ProductVariant $variant,
        int $quantity,
        ?TailoringRequest $tailoringRequest = null,
    ): Cart {
        if ($quantity < 1 || ! $product->is_active || ($variant && (! $variant->is_active || $variant->product_id !== $product->id))) {
            throw ValidationException::withMessages(['quantity' => __('commerce.invalid_item')]);
        }

        if ($tailoringRequest) {
            if (
                $tailoringRequest->user_id !== $cart->user_id
                || $tailoringRequest->product_id !== $product->id
                || $tailoringRequest->product_variant_id !== $variant?->id
                || $tailoringRequest->status !== 'ready'
            ) {
                throw ValidationException::withMessages(['tailoring' => __('measurements.invalid_tailoring_request')]);
            }

            $quantity = 1;
        }

        $available = $variant?->availableQuantity() ?? $product->availableStock();
        if ($quantity > $available) {
            throw ValidationException::withMessages(['quantity' => __('commerce.insufficient_stock')]);
        }

        $lineKey = $tailoringRequest
            ? 'tailor:'.$tailoringRequest->uuid
            : 'std:'.$product->id.':'.($variant?->id ?? 0);

        $item = $cart->items()->firstOrNew(['line_key' => $lineKey]);

        if (! $item->exists) {
            $item->uuid = (string) Str::uuid();
            $item->product_id = $product->id;
            $item->product_variant_id = $variant?->id;
            $item->tailoring_request_id = $tailoringRequest?->id;
        }

        $wanted = $tailoringRequest ? 1 : (($item->exists ? $item->quantity : 0) + $quantity);
        if ($wanted > $available) {
            throw ValidationException::withMessages(['quantity' => __('commerce.insufficient_stock')]);
        }

        $item->quantity = $wanted;
        $item->save();

        return $this->load($cart);
    }

    public function update(Cart $cart, CartItem $item, int $quantity): Cart
    {
        abort_unless($item->cart_id === $cart->id, 404);

        if ($quantity === 0) {
            $item->tailoringRequest?->update(['status' => 'cancelled']);
            $item->delete();

            return $this->load($cart);
        }

        if ($item->tailoring_request_id && $quantity !== 1) {
            throw ValidationException::withMessages(['quantity' => __('measurements.tailored_quantity_one')]);
        }

        if ($quantity < 0 || $quantity > ($item->variant?->availableQuantity() ?? $item->product->availableStock())) {
            throw ValidationException::withMessages(['quantity' => __('commerce.insufficient_stock')]);
        }

        $item->update(['quantity' => $quantity]);

        return $this->load($cart);
    }

    public function remove(Cart $cart, CartItem $item): Cart
    {
        abort_unless($item->cart_id === $cart->id, 404);
        $item->tailoringRequest?->update(['status' => 'cancelled']);
        $item->delete();

        return $this->load($cart);
    }

    public function load(Cart $cart): Cart
    {
        return $cart->fresh([
            'items.product.translations',
            'items.variant.product',
            'items.variant.size',
            'items.variant.color.translations',
            'items.variant.inventory',
            'items.tailoringRequest.measurementProfile.values.definition.translations',
        ]);
    }
}
