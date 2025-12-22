<?php

use Illuminate\Support\Facades\Route;
use Modules\Requests\Http\Controllers\RequestLiveController;
use Modules\Requests\Http\Controllers\RequestChangeRoomController;

/*
|--------------------------------------------------------------------------
| STUDENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::post('/requests/live', [RequestLiveController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| MANAGER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    Route::get('/requests/live', [RequestLiveController::class, 'index']);
    Route::post('/requests/live/{id}/approve', [RequestLiveController::class, 'approve']);
    Route::post('/requests/live/{id}/reject', [RequestLiveController::class, 'reject']);
});


/*
|--------------------------------------------------------------------------
| STUDENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::post('/requests/change-room', [RequestChangeRoomController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| MANAGER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    Route::get('/requests/change-room', [RequestChangeRoomController::class, 'index']);
    Route::post('/requests/change-room/{id}/approve', [RequestChangeRoomController::class, 'approve']);
    Route::post('/requests/change-room/{id}/reject', [RequestChangeRoomController::class, 'reject']);
});
