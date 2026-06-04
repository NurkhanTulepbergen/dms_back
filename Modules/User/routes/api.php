<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\NotificationController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Current user
        |--------------------------------------------------------------------------
        */
        Route::get('me', [UserController::class, 'me']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);

        /*
        |--------------------------------------------------------------------------
        | Users (read-only for now)
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:admin,manager,employee')->group(function () {
            Route::get('users', [UserController::class, 'index']);
        });

        Route::middleware('role:admin,manager')->group(function () {
            Route::get('notifications/broadcasts', [NotificationController::class, 'broadcasts']);
            Route::post('notifications/broadcasts', [NotificationController::class, 'store']);

            Route::apiResource('users', UserController::class)
                ->only(['show', 'store', 'update', 'destroy']);
        });

        /*
        |--------------------------------------------------------------------------
        | Debug / legacy (если реально нужен)
        |--------------------------------------------------------------------------
        */
        // Route::get('users-json', [UserController::class, 'json']);
    });
