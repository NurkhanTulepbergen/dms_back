<?php

use Illuminate\Support\Facades\Route;
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

        /*
        |--------------------------------------------------------------------------
        | Users (read-only for now)
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:admin,manager')->group(function () {
            Route::apiResource('users', UserController::class)
                ->only(['index', 'show', 'store', 'update', 'destroy']);
        });

        /*
        |--------------------------------------------------------------------------
        | Debug / legacy (если реально нужен)
        |--------------------------------------------------------------------------
        */
        // Route::get('users-json', [UserController::class, 'json']);
    });
