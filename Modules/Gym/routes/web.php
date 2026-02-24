<?php

use Illuminate\Support\Facades\Route;
use Modules\Gym\Http\Controllers\GymController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('gyms', GymController::class)->names('gym');
});
