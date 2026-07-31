<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', HealthController::class)->middleware('throttle:health');

    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('products/trash', [ProductController::class, 'trashed'])->middleware('throttle:api');
        Route::post('products/{product}/restock', [ProductController::class, 'restock'])->middleware('throttle:mutations');
        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->middleware('throttle:mutations');
        Route::delete('products/{product}/purge', [ProductController::class, 'purge'])->middleware('throttle:mutations');
        Route::apiResource('products', ProductController::class)->names('api.products')->middleware('throttle:api');
    });
});
