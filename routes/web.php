<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CommerceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\MeasurementProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TailoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/'.config('kabulfit.default_locale')));
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/ProductDetail', [LegacyRedirectController::class, 'product'])->name('legacy.product');

Route::prefix('{locale}')
    ->where(['locale' => 'en|fa|ps'])
    ->middleware('locale')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/shop', [CatalogController::class, 'index'])->name('shop');
        Route::get('/categories/{slug}', [CatalogController::class, 'category'])->name('categories.show');
        Route::get('/collections/{slug}', [CatalogController::class, 'collection'])->name('collections.show');
        Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

        Route::middleware('guest')->group(function (): void {
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

            Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
            Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

            Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('throttle:password-reset')
                ->name('password.email');

            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
            Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
        });

        Route::middleware(['auth', 'active.user'])->group(function (): void {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

            Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
            Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
            Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

            Route::get('/account', AccountController::class)->name('account');
            Route::post('/account/addresses', [AddressController::class, 'store'])->name('addresses.store');
            Route::put('/account/addresses/{address:uuid}', [AddressController::class, 'update'])->name('addresses.update');
            Route::delete('/account/addresses/{address:uuid}', [AddressController::class, 'destroy'])->name('addresses.destroy');

            Route::get('/measurements', [MeasurementProfileController::class, 'index'])->name('measurements.index');
            Route::get('/measurements/create', [MeasurementProfileController::class, 'create'])->name('measurements.create');
            Route::post('/measurements', [MeasurementProfileController::class, 'store'])->name('measurements.store');
            Route::get('/measurements/{profile:uuid}/edit', [MeasurementProfileController::class, 'edit'])->name('measurements.edit');
            Route::put('/measurements/{profile:uuid}', [MeasurementProfileController::class, 'update'])->name('measurements.update');
            Route::delete('/measurements/{profile:uuid}', [MeasurementProfileController::class, 'destroy'])->name('measurements.destroy');

            Route::get('/products/{slug}/tailor', [TailoringController::class, 'create'])->name('tailoring.create');
            Route::post('/products/{slug}/tailor', [TailoringController::class, 'store'])->name('tailoring.store');

            Route::get('/cart', [CommerceController::class, 'cart'])->name('cart');
            Route::post('/cart/items', [CommerceController::class, 'add'])->name('cart.items.store');
            Route::put('/cart/items/{item:uuid}', [CommerceController::class, 'update'])->name('cart.items.update');
            Route::delete('/cart/items/{item:uuid}', [CommerceController::class, 'remove'])->name('cart.items.destroy');
            Route::get('/wishlist', [CommerceController::class, 'wishlist'])->name('wishlist');
            Route::post('/wishlist', [CommerceController::class, 'wishlistStore'])->name('wishlist.store');
            Route::delete('/wishlist/{slug}', [CommerceController::class, 'wishlistDestroy'])->name('wishlist.destroy');
            Route::get('/checkout', [CommerceController::class, 'checkout'])->name('checkout');
            Route::post('/checkout', [CommerceController::class, 'place'])->middleware('throttle:20,1')->name('checkout.place');
            Route::get('/orders/{order:uuid}/payment', [CommerceController::class, 'payment'])->name('orders.payment');
        });
    });
