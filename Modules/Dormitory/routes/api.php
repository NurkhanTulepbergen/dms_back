<?php

use Illuminate\Support\Facades\Route;
use Modules\Dormitory\Http\Controllers\DormitoryController;

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Buildings
    |--------------------------------------------------------------------------
    */
    Route::get('/buildings', [DormitoryController::class, 'indexBuilding']);
    Route::get('/buildings/{id}', [DormitoryController::class, 'showBuilding']);
    Route::post('/buildings', [DormitoryController::class, 'storeBuilding']);
    Route::put('/buildings/{id}', [DormitoryController::class, 'updateBuilding']);
    Route::delete('/buildings/{id}', [DormitoryController::class, 'destroyBuilding']);

    /*
    |--------------------------------------------------------------------------
    | Floors
    |--------------------------------------------------------------------------
    */
    Route::get('/floors', [DormitoryController::class, 'indexFloor']);
    Route::get('/floors/{id}', [DormitoryController::class, 'showFloor']);
    Route::post('/floors', [DormitoryController::class, 'storeFloor']);
    Route::put('/floors/{id}', [DormitoryController::class, 'updateFloor']);
    Route::delete('/floors/{id}', [DormitoryController::class, 'destroyFloor']);

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    */
    Route::get('/rooms', [DormitoryController::class, 'indexRooms']);
    Route::get('/rooms/{id}', [DormitoryController::class, 'showRooms']);
    Route::post('/rooms', [DormitoryController::class, 'storeRooms']);
    Route::put('/rooms/{id}', [DormitoryController::class, 'updateRooms']);
    Route::delete('/rooms/{id}', [DormitoryController::class, 'destroyRooms']);

});
