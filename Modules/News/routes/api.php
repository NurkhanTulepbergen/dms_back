<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\NewsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('news', NewsController::class)->names('news');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
});

// Панель Админа
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::post('/news', [NewsController::class, 'store']);
    Route::put('/news/{id}', [NewsController::class, 'update']);
    Route::delete('/news/{id}', [NewsController::class, 'destroy']);
});

