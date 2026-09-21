<?php
namespace App\Http\Controllers;
use App\Models\{Address,CartItem,Coupon,Product,ProductVariant,ShippingMethod};
use App\Services\Commerce\{CartService,CheckoutService};
use App\Services\Payments\PaymentService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\{RedirectResponse,Request};
use Illuminate\View\View;
class CommerceController extends Controller {
 public function __construct(private readonly CartService $carts){}
 public function cart(Request $r): View {$cart=$this->carts->load($this->carts->forUser($r->user()));return view('commerce.cart',['cart'=>$cart,'seo'=>PrivatePageSeo::make(__('commerce.cart'),route('cart',['locale'=>app()->getLocale()]))]);}
 public function add(Request $r): RedirectResponse {$d=$r->validate(['product_slug'=>'required|string','variant_sku'=>'nullable|string','quantity'=>'required|integer|min:1|max:99']);$p=Product::whereHas('translations',fn($q)=>$q->where('locale',app()->getLocale())->where('slug',$d['product_slug']))->with(['translations','variants.inventory','variants.product'])->firstOrFail();$v=isset($d['variant_sku'])?$p->variants->firstWhere('sku',$d['variant_sku']):null;$this->carts->add($this->carts->forUser($r->user()),$p,$v,$d['quantity']);return redirect()->route('cart',['locale'=>app()->getLocale()]);}
 public function update(Request $r,CartItem $item): RedirectResponse {$this->carts->update($this->carts->forUser($r->user()),$item,(int)$r->validate(['quantity'=>'required|integer|min:0|max:99'])['quantity']);return back();}
 public function remove(Request $r,CartItem $item): RedirectResponse {$this->carts->remove($this->carts->forUser($r->user()),$item);return back();}
 public function checkout(Request $r): View {$cart=$this->carts->load($this->carts->forUser($r->user()));return view('commerce.checkout',['cart'=>$cart,'addresses'=>$r->user()->addresses()->get(),'shippingMethods'=>ShippingMethod::where('is_active',true)->orderBy('sort_order')->get(),'seo'=>PrivatePageSeo::make(__('commerce.checkout'),route('checkout',['locale'=>app()->getLocale()]))]);}
 public function place(Request $r,CheckoutService $checkout,PaymentService $payments): View {$d=$r->validate(['address_uuid'=>'required|uuid','shipping_method'=>'required|string','coupon'=>'nullable|string']);$a=Address::where('user_id',$r->user()->id)->where('uuid',$d['address_uuid'])->firstOrFail();$s=ShippingMethod::where('code',$d['shipping_method'])->where('is_active',true)->firstOrFail();$coupon=!empty($d['coupon'])?Coupon::where('code',strtoupper($d['coupon']))->first():null;$order=$checkout->create($r->user(),$this->carts->forUser($r->user()),$a,$s,$coupon);$payment=$payments->initiate($order);return view('commerce.payment',['order'=>$order,'clientSecret'=>$payment['client_secret'],'stripeKey'=>config('services.stripe.key'),'seo'=>PrivatePageSeo::make(__('commerce.payment'),route('orders.payment',['locale'=>app()->getLocale(),'order'=>$order]))]);}
 public function payment(Request $r,\App\Models\Order $order): View {abort_unless($order->user_id===$r->user()->id,404);return view('commerce.payment',['order'=>$order,'clientSecret'=>null,'stripeKey'=>config('services.stripe.key'),'seo'=>PrivatePageSeo::make(__('commerce.payment'),route('orders.payment',['locale'=>app()->getLocale(),'order'=>$order]))]);}
}