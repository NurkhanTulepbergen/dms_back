<?php

use Illuminate\Support\Facades\Route;
use Modules\Settlement\Http\Controllers\SettlementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('settlements', SettlementController::class)->names('settlement');
});
