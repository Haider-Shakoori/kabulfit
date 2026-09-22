<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LegacyUrlController as AdminLegacyUrlController;
use App\Http\Controllers\Admin\MeasurementController as AdminMeasurementController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TailoringController as AdminTailoringController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CommerceController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DefaultLocaleRedirectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyPageRedirectController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\MeasurementProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TailoringController;
use App\Http\Controllers\TailorWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', DefaultLocaleRedirectController::class)->name('locale.default');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemapIndex'])->name('sitemap');
Route::get('/sitemaps/catalog.xml', [SeoController::class, 'catalogSitemap'])->name('sitemaps.catalog');
Route::get('/sitemaps/content.xml', [SeoController::class, 'contentSitemap'])->name('sitemaps.content');
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
        Route::get('/blog', [ContentController::class, 'blog'])->name('blog.index');
        Route::get('/blog/{slug}', [ContentController::class, 'post'])->name('blog.show');

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

            Route::get('/tailoring', [TailoringController::class, 'index'])->name('tailoring.index');
            Route::get('/tailoring/{tailoring}', [TailoringController::class, 'show'])
                ->whereUuid('tailoring')
                ->name('tailoring.show');
            Route::get('/products/{slug}/tailor', [TailoringController::class, 'create'])->name('tailoring.create');
            Route::post('/products/{slug}/tailor', [TailoringController::class, 'store'])->name('tailoring.store');

            Route::prefix('tailor')
                ->name('tailor.')
                ->middleware('permission:tailoring.work')
                ->group(function (): void {
                    Route::get('/', [TailorWorkspaceController::class, 'index'])->name('index');
                    Route::get('/assignments/{assignment:uuid}', [TailorWorkspaceController::class, 'show'])->name('show');
                    Route::post('/assignments/{assignment:uuid}/status', [TailorWorkspaceController::class, 'transition'])->name('status');
                    Route::post('/assignments/{assignment:uuid}/notes', [TailorWorkspaceController::class, 'note'])->name('notes.store');
                });

            Route::get('/cart', [CommerceController::class, 'cart'])->name('cart');
            Route::post('/cart/items', [CommerceController::class, 'add'])->name('cart.items.store');
            Route::put('/cart/items/{item:uuid}', [CommerceController::class, 'update'])->name('cart.items.update');
            Route::delete('/cart/items/{item:uuid}', [CommerceController::class, 'remove'])->name('cart.items.destroy');
            Route::get('/wishlist', [CommerceController::class, 'wishlist'])->name('wishlist');
            Route::post('/wishlist', [CommerceController::class, 'wishlistStore'])->name('wishlist.store');
            Route::delete('/wishlist/{slug}', [CommerceController::class, 'wishlistDestroy'])->name('wishlist.destroy');
            Route::get('/checkout', [CommerceController::class, 'checkout'])->name('checkout');
            Route::post('/checkout', [CommerceController::class, 'place'])->middleware('throttle:20,1')->name('checkout.place');
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order:uuid}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('/orders/{order:uuid}/payment', [CommerceController::class, 'payment'])->name('orders.payment');

            Route::prefix('admin')
                ->name('admin.')
                ->middleware('permission:admin.access')
                ->group(function (): void {
                    Route::get('/', AdminDashboardController::class)->name('dashboard');

                    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
                    Route::put('/products/{product:sku}', [AdminProductController::class, 'update'])->name('products.update');

                    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
                    Route::get('/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
                    Route::post('/orders/{order:uuid}/status', [AdminOrderController::class, 'transition'])->name('orders.transition');
                    Route::post('/orders/{order:uuid}/shipments', [AdminOrderController::class, 'shipment'])->name('orders.shipments.store');
                    Route::post('/shipments/{shipment:uuid}/status', [AdminOrderController::class, 'shipmentStatus'])->name('shipments.status');

                    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
                    Route::get('/customers/{customer:uuid}', [AdminCustomerController::class, 'show'])->name('customers.show');
                    Route::put('/customers/{customer:uuid}', [AdminCustomerController::class, 'update'])->name('customers.update');
                    Route::put('/customers/{customer:uuid}/roles', [AdminCustomerController::class, 'roles'])->name('customers.roles');

                    Route::get('/measurements', [AdminMeasurementController::class, 'index'])->name('measurements.index');
                    Route::put('/measurements/{definition:uuid}', [AdminMeasurementController::class, 'update'])->name('measurements.update');

                    Route::get('/tailoring', [AdminTailoringController::class, 'index'])->name('tailoring.index');
                    Route::get('/tailoring/{tailoring:uuid}', [AdminTailoringController::class, 'show'])->name('tailoring.show');
                    Route::post('/tailoring/{tailoring:uuid}/assignment', [AdminTailoringController::class, 'assign'])->name('tailoring.assign');
                    Route::post('/tailoring/{tailoring:uuid}/cancel', [AdminTailoringController::class, 'cancel'])->name('tailoring.cancel');

                    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
                    Route::get('/payments/{payment:uuid}', [AdminPaymentController::class, 'show'])->name('payments.show');
                    Route::post('/payments/{payment:uuid}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');

                    Route::get('/content', [AdminContentController::class, 'index'])->name('content.index');
                    Route::put('/content/pages/{page:uuid}', [AdminContentController::class, 'updatePage'])->name('content.pages.update');
                    Route::post('/content/posts', [AdminContentController::class, 'storePost'])->name('content.posts.store');
                    Route::put('/content/posts/{post:uuid}', [AdminContentController::class, 'updatePost'])->name('content.posts.update');
                    Route::delete('/content/posts/{post:uuid}', [AdminContentController::class, 'destroyPost'])->name('content.posts.destroy');

                    Route::get('/legacy-urls', [AdminLegacyUrlController::class, 'index'])->name('legacy.index');
                    Route::put('/legacy-urls/{legacyUrl:uuid}', [AdminLegacyUrlController::class, 'update'])->name('legacy.update');

                    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
                    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

                    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
                    Route::put('/roles/{role:uuid}', [AdminRoleController::class, 'update'])->name('roles.update');

                    Route::get('/audit', AdminAuditController::class)->name('audit.index');
                });
        });

        Route::get('/{slug}', [ContentController::class, 'page'])
            ->where('slug', '[^/]+')
            ->name('content.page');
    });

Route::get('/{legacy}', LegacyPageRedirectController::class)
    ->where('legacy', 'About|Contact|FAQ|MeasurementGuide|PrivacyPolicy|ReturnPolicy|ShippingPolicy|Shop|TermsConditions|Account|Cart|Checkout|Orders|Wishlist|MyMeasurements|TailorDashboard')
    ->name('legacy.page');
