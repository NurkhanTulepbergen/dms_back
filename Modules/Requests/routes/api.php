<?php

use Illuminate\Support\Facades\Route;
use Modules\Requests\Http\Controllers\RequestLiveController;
use Modules\Requests\Http\Controllers\RequestChangeRoomController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:student')->group(function () {
            Route::post('requests/live', [RequestLiveController::class, 'store']);
            Route::post('requests/change-room', [RequestChangeRoomController::class, 'store']);
        });

        /*
        |--------------------------------------------------------------------------
        | MANAGER / ADMIN
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:manager,admin')->group(function () {

            // Request Live
            Route::get('requests/live', [RequestLiveController::class, 'index']);
            Route::post('requests/live/{requestLive}/approve', [RequestLiveController::class, 'approve']);
            Route::post('requests/live/{requestLive}/reject', [RequestLiveController::class, 'reject']);

            // Request Change Room
            Route::get('requests/change-room', [RequestChangeRoomController::class, 'index']);
            Route::post('requests/change-room/{requestChangeRoom}/approve', [RequestChangeRoomController::class, 'approve']);
            Route::post('requests/change-room/{requestChangeRoom}/reject', [RequestChangeRoomController::class, 'reject']);
        });
    });
