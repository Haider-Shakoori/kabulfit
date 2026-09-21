<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Services\Commerce\CartService;
use App\Services\Commerce\CheckoutService;
use App\Services\Payments\PaymentService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommerceController extends Controller
{
    public function __construct(private readonly CartService $carts) {}

    public function cart(Request $request): View
    {
        $cart = $this->carts->load($this->carts->forUser($request->user()));

        return view('commerce.cart', [
            'cart' => $cart,
            'seo' => PrivatePageSeo::make(__('commerce.cart'), route('cart', ['locale' => app()->getLocale()])),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_slug' => 'required|string',
            'variant_sku' => 'nullable|string',
            'quantity' => 'required|integer|min:1|max:99',
        ]);
        $product = Product::whereHas(
            'translations',
            fn ($query) => $query->where('locale', app()->getLocale())->where('slug', $data['product_slug']),
        )->with(['translations', 'variants.inventory', 'variants.product'])->firstOrFail();
        $variant = isset($data['variant_sku']) ? $product->variants->firstWhere('sku', $data['variant_sku']) : null;
        $this->carts->add($this->carts->forUser($request->user()), $product, $variant, $data['quantity']);

        return redirect()->route('cart', ['locale' => app()->getLocale()]);
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        $this->carts->update($this->carts->forUser($request->user()), $item, (int) $data['quantity']);

        return back();
    }

    public function remove(Request $request, CartItem $item): RedirectResponse
    {
        $this->carts->remove($this->carts->forUser($request->user()), $item);

        return back();
    }

    public function checkout(Request $request): View
    {
        $cart = $this->carts->load($this->carts->forUser($request->user()));

        return view('commerce.checkout', [
            'cart' => $cart,
            'addresses' => $request->user()->addresses()->get(),
            'shippingMethods' => ShippingMethod::where('is_active', true)->orderBy('sort_order')->get(),
            'seo' => PrivatePageSeo::make(__('commerce.checkout'), route('checkout', ['locale' => app()->getLocale()])),
        ]);
    }

    public function place(Request $request, CheckoutService $checkout, PaymentService $payments): View
    {
        $data = $request->validate([
            'address_uuid' => 'required|uuid',
            'shipping_method' => 'required|string',
            'coupon' => 'nullable|string',
        ]);
        $address = Address::where('user_id', $request->user()->id)->where('uuid', $data['address_uuid'])->firstOrFail();
        $shipping = ShippingMethod::where('code', $data['shipping_method'])->where('is_active', true)->firstOrFail();
        $coupon = ! empty($data['coupon']) ? Coupon::where('code', strtoupper($data['coupon']))->first() : null;
        $order = $checkout->create($request->user(), $this->carts->forUser($request->user()), $address, $shipping, $coupon);
        $payment = $payments->initiate($order);

        return view('commerce.payment', [
            'order' => $order,
            'clientSecret' => $payment['client_secret'],
            'stripeKey' => config('services.stripe.key'),
            'seo' => PrivatePageSeo::make(__('commerce.payment'), route('orders.payment', ['locale' => app()->getLocale(), 'order' => $order])),
        ]);
    }

    public function payment(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('commerce.payment', [
            'order' => $order,
            'clientSecret' => null,
            'stripeKey' => config('services.stripe.key'),
            'seo' => PrivatePageSeo::make(__('commerce.payment'), route('orders.payment', ['locale' => app()->getLocale(), 'order' => $order])),
        ]);
    }
}
