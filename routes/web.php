<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/_debug/logtest', function () {
    Log::info('LOGTEST_OK', [
        'auth_check' => auth('web')->check(),
        'user_id' => auth('web')->id(),
        'role' => optional(auth('web')->user())->role,
        'session_id' => session()->getId(),
        'cookie_names' => array_keys(request()->cookies->all()),
    ]);

    return response()->json(['ok' => true]);
});

Route::get('/', function () {
    return view('welcome');
});
