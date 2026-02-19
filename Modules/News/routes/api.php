<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\NewsController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource('news', NewsController::class)
            ->only(['index', 'show']);

        Route::middleware('role:admin,manager')->group(function () {
            Route::apiResource('news', NewsController::class)
                ->only(['store', 'update', 'destroy']);
        });
    });
