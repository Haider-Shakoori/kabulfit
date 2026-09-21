<?php

use App\Http\Controllers\Api\V1\CatalogApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/{locale}')
    ->where(['locale' => 'en|fa|ps'])
    ->middleware(['locale', 'throttle:60,1'])
    ->group(function (): void {
        Route::get('/catalog/products', [CatalogApiController::class, 'index'])->name('api.v1.catalog.products.index');
        Route::get('/catalog/products/{slug}', [CatalogApiController::class, 'show'])->name('api.v1.catalog.products.show');
        Route::get('/catalog/facets', [CatalogApiController::class, 'facets'])->name('api.v1.catalog.facets');
    });
