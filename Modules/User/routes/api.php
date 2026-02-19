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
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{user}', [UserController::class, 'show']);

        /*
        |--------------------------------------------------------------------------
        | Debug / legacy (если реально нужен)
        |--------------------------------------------------------------------------
        */
        // Route::get('users-json', [UserController::class, 'json']);
    });
