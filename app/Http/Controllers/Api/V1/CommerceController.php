<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Wishlist;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommerceController extends Controller
{
    public function __construct(private readonly CartService $carts, private readonly CheckoutService $checkout, private readonly PaymentService $payments) {}

    public function cart(Request $r): JsonResponse
    {
        return response()->json($this->payload($this->carts->load($this->carts->forUser($r->user()))));
    }

    public function add(Request $r): JsonResponse
    {
        $d = $r->validate(['product_slug' => 'required|string', 'variant_sku' => 'nullable|string', 'quantity' => 'required|integer|min:1|max:99']);
        $p = Product::whereHas('translations', fn ($q) => $q->where('locale', app()->getLocale())->where('slug', $d['product_slug']))->with(['translations', 'variants.inventory', 'variants.product'])->firstOrFail();
        $v = isset($d['variant_sku']) ? $p->variants->firstWhere('sku', $d['variant_sku']) : null;

        return response()->json($this->payload($this->carts->add($this->carts->forUser($r->user()), $p, $v, $d['quantity'])), 201);
    }

    public function update(Request $r, CartItem $item): JsonResponse
    {
        $d = $r->validate(['quantity' => 'required|integer|min:0|max:99']);

        return response()->json($this->payload($this->carts->update($this->carts->forUser($r->user()), $item, $d['quantity'])));
    }

    public function remove(Request $r, CartItem $item): JsonResponse
    {
        return response()->json($this->payload($this->carts->remove($this->carts->forUser($r->user()), $item)));
    }

    public function wishlist(Request $r): JsonResponse
    {
        $items = Wishlist::where('user_id', $r->user()->id)->with('product.translations')->latest()->get();

        return response()->json(['data' => $items->map(fn ($w) => ['slug' => $w->product->translation()?->slug, 'sku' => $w->product->sku, 'name' => $w->product->translation()?->name])]);
    }

    public function wishlistStore(Request $r): JsonResponse
    {
        $d = $r->validate(['product_slug' => 'required|string']);
        $p = Product::whereHas('translations', fn ($q) => $q->where('locale', app()->getLocale())->where('slug', $d['product_slug']))->firstOrFail();
        Wishlist::firstOrCreate(['user_id' => $r->user()->id, 'product_id' => $p->id]);

        return response()->json(['success' => true], 201);
    }

    public function wishlistDestroy(Request $r, string $slug): JsonResponse
    {
        $p = Product::whereHas('translations', fn ($q) => $q->where('locale', app()->getLocale())->where('slug', $slug))->firstOrFail();
        Wishlist::where(['user_id' => $r->user()->id, 'product_id' => $p->id])->delete();

        return response()->json(['success' => true]);
    }

    public function checkout(Request $r): JsonResponse
    {
        $d = $r->validate(['address_uuid' => 'required|uuid', 'shipping_method' => 'required|string', 'coupon' => 'nullable|string']);
        $a = Address::where('user_id', $r->user()->id)->where('uuid', $d['address_uuid'])->firstOrFail();
        $s = ShippingMethod::where('code', $d['shipping_method'])->where('is_active', true)->firstOrFail();
        $coupon = isset($d['coupon']) ? Coupon::where('code', strtoupper($d['coupon']))->first() : null;
        $order = $this->checkout->create($r->user(), $this->carts->forUser($r->user()), $a, $s, $coupon);
        $payment = $this->payments->initiate($order);

        return response()->json(['data' => ['order_uuid' => $order->uuid, 'order_number' => $order->number, 'status' => $order->status, 'payment_status' => $order->payment_status, 'total_minor' => $order->total_minor, 'currency' => $order->currency, 'stripe' => ['payment_intent_id' => $payment['payment']->provider_payment_id, 'client_secret' => $payment['client_secret']]]], 201);
    }

    public function order(Request $r, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $r->user()->id, 404);

        return response()->json(['data' => ['uuid' => $order->uuid, 'number' => $order->number, 'status' => $order->status, 'payment_status' => $order->payment_status, 'total_minor' => $order->total_minor, 'currency' => $order->currency]]);
    }

    private function payload($cart): array
    {
        return ['data' => ['uuid' => $cart->uuid, 'currency' => $cart->currency, 'items' => $cart->items->map(fn ($i) => ['uuid' => $i->uuid, 'product_slug' => $i->product->translation()?->slug, 'sku' => $i->variant?->sku ?? $i->product->sku, 'name' => $i->product->translation()?->name, 'quantity' => $i->quantity, 'unit_price_minor' => $i->unitPriceMinor(), 'line_total_minor' => $i->lineTotalMinor(),
            'tailoring' => $i->tailoringRequest ? [
                'uuid' => $i->tailoringRequest->uuid,
                'status' => $i->tailoringRequest->status,
                'measurement_profile' => $i->tailoringRequest->measurementProfile?->name,
            ] : null,
        ]), 'subtotal_minor' => $cart->items->sum(fn ($i) => $i->lineTotalMinor())]];
    }
}
