<?php

namespace App\Services\Commerce;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function create(User $user, Cart $cart, Address $address, ShippingMethod $shipping, ?Coupon $coupon = null): Order
    {
        abort_unless($cart->user_id === $user->id && $address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $cart, $address, $shipping, $coupon) {
            $cart->load(['items.product.translations', 'items.variant.inventory', 'items.variant.size', 'items.variant.color.translations']);
            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => __('commerce.empty_cart')]);
            }
            $subtotal = 0;
            foreach ($cart->items as $item) {
                if ($item->variant) {
                    $inventory = InventoryItem::query()->where('product_variant_id', $item->variant->id)->lockForUpdate()->firstOrFail();
                    if ($inventory->availableQuantity() < $item->quantity) {
                        throw ValidationException::withMessages(['cart' => __('commerce.insufficient_stock')]);
                    }
                    $inventory->increment('quantity_reserved', $item->quantity);
                } elseif ($item->product->stock_quantity < $item->quantity) {
                    throw ValidationException::withMessages(['cart' => __('commerce.insufficient_stock')]);
                }
                $subtotal += $item->lineTotalMinor();
            }
            $discount = $coupon?->discountFor($subtotal) ?? 0;
            if ($coupon && $discount === 0) {
                throw ValidationException::withMessages(['coupon' => __('commerce.invalid_coupon')]);
            }
            $order = Order::create([
                'uuid' => (string) Str::uuid(),
                'number' => 'KF-'.now()->format('ymd').'-'.Str::upper(Str::random(8)),
                'user_id' => $user->id,
                'status' => 'pending_payment',
                'payment_status' => 'pending',
                'currency' => $cart->currency,
                'subtotal_minor' => $subtotal,
                'discount_minor' => $discount,
                'shipping_minor' => $shipping->price_minor,
                'total_minor' => $subtotal - $discount + $shipping->price_minor,
                'coupon_code' => $coupon?->code,
                'shipping_method_code' => $shipping->code,
                'reservation_expires_at' => now()->addMinutes((int) config('kabulfit.checkout.reservation_minutes', 30)),
                'shipping_address' => $address->only([
                    'recipient_name', 'phone', 'country_code', 'province', 'city',
                    'address_line1', 'address_line2', 'postal_code',
                ]),
            ]);
            foreach ($cart->items as $item) {
                $v = $item->variant;
                $order->items()->create(['product_id' => $item->product_id, 'product_variant_id' => $v?->id, 'sku' => $v?->sku ?? $item->product->sku, 'name' => $item->product->translation()?->name ?? $item->product->sku, 'variant_label' => $v?->option_key, 'unit_price_minor' => $item->unitPriceMinor(), 'quantity' => $item->quantity, 'line_total_minor' => $item->lineTotalMinor()]);
            }
            if ($coupon) {
                $coupon->increment('times_used');
            }

            $cart->items()->delete();

            return $order->load('items');
        }, 3);
    }

    public function releaseReservations(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->get() as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }$inv = InventoryItem::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->first();
                if ($inv) {
                    $inv->update(['quantity_reserved' => max(0, $inv->quantity_reserved - $item->quantity)]);
                }
            }
        });
    }

    public function captureReservations(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }$inv = InventoryItem::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->firstOrFail();
                $inv->update(['quantity_reserved' => max(0, $inv->quantity_reserved - $item->quantity), 'quantity_on_hand' => max(0, $inv->quantity_on_hand - $item->quantity)]);
            }
        });
    }
}
