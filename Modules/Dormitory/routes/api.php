<?php

use Illuminate\Support\Facades\Route;
use Modules\Dormitory\Http\Controllers\BuildingController;
use Modules\Dormitory\Http\Controllers\FloorController;
use Modules\Dormitory\Http\Controllers\RoomController;
use Modules\Dormitory\Http\Controllers\DormitoryController;

Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Buildings (CRUD)
        |--------------------------------------------------------------------------
        */
        Route::apiResource('buildings', BuildingController::class)
            ->only(['index', 'show']);

        Route::middleware('role:admin,manager')->group(function () {
            Route::apiResource('buildings', BuildingController::class)
                ->only(['store', 'update', 'destroy']);
        });

        /*
        |--------------------------------------------------------------------------
        | Floors (CRUD)
        |--------------------------------------------------------------------------
        */
        Route::apiResource('floors', FloorController::class)
            ->only(['index', 'show']);

        Route::middleware('role:admin,manager')->group(function () {
            Route::apiResource('floors', FloorController::class)
                ->only(['store', 'update', 'destroy']);
        });

        /*
        |--------------------------------------------------------------------------
        | Rooms (CRUD)
        |--------------------------------------------------------------------------
        */
        Route::apiResource('rooms', RoomController::class)
            ->only(['index', 'show']);

        Route::middleware('role:admin,manager')->group(function () {
            Route::apiResource('rooms', RoomController::class)
                ->only(['store', 'update', 'destroy']);
        });

        /*
        |--------------------------------------------------------------------------
        | Housing hierarchy (read-only)
        |--------------------------------------------------------------------------
        */
        Route::get('buildings/{building}/floors', [DormitoryController::class, 'getFloorsForBuilding']);
        Route::get('floors/{floor}/rooms', [DormitoryController::class, 'getRoomsForFloor']);
    });
