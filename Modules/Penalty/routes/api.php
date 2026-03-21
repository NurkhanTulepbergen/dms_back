<?php

use Illuminate\Support\Facades\Route;
use Modules\Penalty\Http\Controllers\PenaltyController;
use Modules\Penalty\Http\Controllers\RedemptionController;

Route::prefix('v1/penalties')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::middleware('role:manager,admin,dorm-admin')->group(function () {
            Route::get('/manage', [PenaltyController::class, 'manageIndex']);
            Route::get('/rules', [PenaltyController::class, 'rules']);
            Route::get('/targets', [PenaltyController::class, 'targets']);
            Route::get('/rooms', [PenaltyController::class, 'rooms']);
        });

        Route::middleware('role:student')->group(function () {
            Route::get('/', [PenaltyController::class, 'index']);
            Route::get('/{id}', [PenaltyController::class, 'show']);
            Route::post('/{id}/redeem', [PenaltyController::class, 'redeem']);
        });

        Route::middleware('role:manager,admin,dorm-admin')->group(function () {
            Route::post('/', [PenaltyController::class, 'store']);
            Route::post('/{id}/cancel', [PenaltyController::class, 'cancel']);
            Route::post('/redemptions/{id}/approve', [RedemptionController::class, 'approve']);
            Route::post('/redemptions/{id}/reject', [RedemptionController::class, 'reject']);
        });
    });
