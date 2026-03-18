<?php

use Illuminate\Support\Facades\Route;
use Modules\Settlement\Http\Controllers\SettlementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('settlements/is-living/{userId}', [SettlementController::class, 'isLiving']);
    Route::apiResource('settlements', SettlementController::class)->names('settlement');
    Route::get('showStatus/{userId}', [SettlementController::class, 'showStatus']);
});
