<?php

use Illuminate\Support\Facades\Route;
use Modules\Gym\Http\Controllers\GymController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::prefix('gym')->group(function () {
        Route::get('plans', [GymController::class, 'plans']);
        Route::get('membership', [GymController::class, 'membership']);
        Route::post('checkout/{plan}', [GymController::class, 'createCheckout']);
        Route::post('check-in', [GymController::class, 'checkIn']);
        Route::post('check-out', [GymController::class, 'completeVisit']);
        Route::get('stats', [GymController::class, 'stats']);
    });
});
