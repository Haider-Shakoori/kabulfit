<?php

use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CommerceController;
use App\Http\Controllers\Api\V1\MeasurementController;
use App\Http\Controllers\Api\V1\TailoringController;
use App\Http\Controllers\Api\V1\MobileAddressController;
use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\MobilePasswordController;
use App\Http\Controllers\Api\V1\MobileVerificationController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', StripeWebhookController::class)->middleware('throttle:120,1')->name('stripe.webhook');

Route::prefix('v1/{locale}')
    ->where(['locale' => 'en|fa|ps'])
    ->middleware('locale')
    ->group(function (): void {
        Route::middleware('throttle:catalog-api')->group(function (): void {
            Route::get('/catalog', [CatalogController::class, 'index'])->name('api.v1.catalog');
            Route::get('/products/{slug}', [CatalogController::class, 'product'])->name('api.v1.products.show');
            Route::get('/categories', [CatalogController::class, 'categories'])->name('api.v1.categories');
            Route::get('/collections', [CatalogController::class, 'collections'])->name('api.v1.collections');
            Route::get('/measurements/definitions', [MeasurementController::class, 'definitions'])->name('api.v1.measurements.definitions');
        });

        Route::prefix('auth')->middleware('throttle:mobile-auth')->group(function (): void {
            Route::post('/register', [MobileAuthController::class, 'register'])->name('api.v1.auth.register');
            Route::post('/login', [MobileAuthController::class, 'login'])->name('api.v1.auth.login');
        });

        Route::prefix('auth')->middleware('throttle:password-reset')->group(function (): void {
            Route::post('/forgot-password', [MobilePasswordController::class, 'forgot'])->name('api.v1.auth.password.forgot');
            Route::post('/reset-password', [MobilePasswordController::class, 'reset'])->name('api.v1.auth.password.reset');
        });

        Route::middleware(['auth:sanctum', 'active.user', 'throttle:account-api'])->group(function (): void {
            Route::get('/account', [MobileAuthController::class, 'me'])->name('api.v1.account');
            Route::post('/auth/logout', [MobileAuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::post('/auth/email/verification-notification', [MobileVerificationController::class, 'send'])
                ->middleware('throttle:6,1')
                ->name('api.v1.auth.verification.send');
            Route::delete('/auth/devices/{device:uuid}', [MobileAuthController::class, 'revokeDevice'])
                ->name('api.v1.auth.devices.destroy');

            Route::get('/account/addresses', [MobileAddressController::class, 'index'])->name('api.v1.addresses.index');
            Route::post('/account/addresses', [MobileAddressController::class, 'store'])->name('api.v1.addresses.store');
            Route::put('/account/addresses/{address:uuid}', [MobileAddressController::class, 'update'])->name('api.v1.addresses.update');
            Route::delete('/account/addresses/{address:uuid}', [MobileAddressController::class, 'destroy'])->name('api.v1.addresses.destroy');

            Route::get('/measurements/profiles', [MeasurementController::class, 'index'])->name('api.v1.measurements.index');
            Route::post('/measurements/profiles', [MeasurementController::class, 'store'])->name('api.v1.measurements.store');
            Route::put('/measurements/profiles/{profile:uuid}', [MeasurementController::class, 'update'])->name('api.v1.measurements.update');
            Route::delete('/measurements/profiles/{profile:uuid}', [MeasurementController::class, 'destroy'])->name('api.v1.measurements.destroy');
            Route::post('/tailoring/requests', [TailoringController::class, 'store'])->name('api.v1.tailoring.store');

            Route::get('/cart', [CommerceController::class, 'cart'])->name('api.v1.cart');
            Route::post('/cart/items', [CommerceController::class, 'add'])->name('api.v1.cart.items.store');
            Route::put('/cart/items/{item:uuid}', [CommerceController::class, 'update'])->name('api.v1.cart.items.update');
            Route::delete('/cart/items/{item:uuid}', [CommerceController::class, 'remove'])->name('api.v1.cart.items.destroy');
            Route::get('/wishlist', [CommerceController::class, 'wishlist'])->name('api.v1.wishlist');
            Route::post('/wishlist', [CommerceController::class, 'wishlistStore'])->name('api.v1.wishlist.store');
            Route::delete('/wishlist/{slug}', [CommerceController::class, 'wishlistDestroy'])->name('api.v1.wishlist.destroy');
            Route::post('/checkout', [CommerceController::class, 'checkout'])->middleware('throttle:20,1')->name('api.v1.checkout');
            Route::get('/orders/{order:uuid}', [CommerceController::class, 'order'])->name('api.v1.orders.show');
        });
    });
