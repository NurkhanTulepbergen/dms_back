<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\FinanceController;

Route::middleware(['auth:sanctum'])
    ->prefix('v1/finance')
    ->group(function () {
        Route::get('/charges', [FinanceController::class, 'charges']);
        Route::post('/checkout/{charge}', [FinanceController::class, 'checkout']);
    });

Route::post('/v1/finance/webhook', [FinanceController::class, 'webhook']);
