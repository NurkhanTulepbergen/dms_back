<?php

use Illuminate\Support\Facades\Route;
use Modules\BuySell\Http\Controllers\BuySellController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::prefix('buy-sell')->group(function () {
            Route::get('meta', [BuySellController::class, 'meta']);
            Route::get('listings', [BuySellController::class, 'index']);
            Route::get('listings/mine', [BuySellController::class, 'mine']);
            Route::get('listings/{listing}', [BuySellController::class, 'show']);

            Route::middleware('role:student,manager,admin')->group(function () {
                Route::post('listings', [BuySellController::class, 'store']);
                Route::put('listings/{listing}', [BuySellController::class, 'update']);
                Route::delete('listings/{listing}', [BuySellController::class, 'destroy']);
            });
        });
    });
