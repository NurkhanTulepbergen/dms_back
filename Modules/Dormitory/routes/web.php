<?php

use Illuminate\Support\Facades\Route;
use Modules\Dormitory\Http\Controllers\DormitoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dormitories', DormitoryController::class)->names('dormitory');
});
