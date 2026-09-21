<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
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
    });
