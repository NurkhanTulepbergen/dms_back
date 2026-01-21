<?php

use Illuminate\Support\Facades\Route;
use Modules\Dormitory\Http\Controllers\buildingController;
use Modules\Dormitory\Http\Controllers\DormitoryController;
use Modules\Dormitory\Http\Controllers\FloorController;
use Modules\Dormitory\Http\Controllers\RoomController;

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Buildings
    |--------------------------------------------------------------------------
    */
    Route::get('/buildings', [BuildingController::class, 'indexBuilding']);
    Route::get('/buildings/{id}', [BuildingController::class, 'showBuilding']);
    Route::post('/buildings', [BuildingController::class, 'storeBuilding']);
    Route::put('/buildings/{id}', [BuildingController::class, 'updateBuilding']);
    Route::delete('/buildings/{id}', [BuildingController::class, 'destroyBuilding']);

    /*
    |--------------------------------------------------------------------------
    | Floors
    |--------------------------------------------------------------------------
    */
    Route::get('/floors', [FloorController::class, 'indexFloor']);
    Route::get('/floors/{id}', [FloorController::class, 'showFloor']);
    Route::post('/floors', [FloorController::class, 'storeFloor']);
    Route::put('/floors/{id}', [FloorController::class, 'updateFloor']);
    Route::delete('/floors/{id}', [FloorController::class, 'destroyFloor']);

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    */
    Route::get('/rooms', [RoomController::class, 'indexRoom']);
    Route::get('/rooms/{id}', [RoomController::class, 'showRoom']);
    Route::post('/rooms', [RoomController::class, 'storeRoom']);
    Route::put('/rooms/{id}', [RoomController::class, 'updateRoom']);
    Route::delete('/rooms/{id}', [RoomController::class, 'destroyRoom']);

    /*
    |--------------------------------------------------------------------------
    | Housing
    |--------------------------------------------------------------------------
    */
    Route::get('/buildings', [BuildingController::class, 'indexBuilding']);
    Route::get('/building/{id}/floors', [DormitoryController::class, 'getFloorsForBuilding']);
    Route::get('/floor/{id}/rooms', [DormitoryController::class, 'getRoomsForFloor']);

});
