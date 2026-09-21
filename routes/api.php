<?php

use App\Http\Controllers\Api\V1\CatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/{locale}')
    ->where(['locale' => 'en|fa|ps'])
    ->middleware(['locale', 'throttle:catalog-api'])
    ->group(function (): void {
        Route::get('/catalog', [CatalogController::class, 'index'])->name('api.v1.catalog');
        Route::get('/products/{slug}', [CatalogController::class, 'product'])->name('api.v1.products.show');
        Route::get('/categories', [CatalogController::class, 'categories'])->name('api.v1.categories');
        Route::get('/collections', [CatalogController::class, 'collections'])->name('api.v1.collections');
    });
