<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/_debug/logtest', function () {
    return response()->json([
        'ok' => true,
        'auth_check' => auth('web')->check(),
        'user_id' => auth('web')->id(),
        'user_email' => auth('web')->user()->email ?? null,
        'user_role' => auth('web')->user()->role ?? null,
        'user_class' => auth('web')->user() ? get_class(auth('web')->user()) : null,
        'session_id' => session()->getId(),
        'cookie_names' => array_keys(request()->cookies->all()),
    ]);
});

Route::get('/', function () {
    return view('welcome');
});
