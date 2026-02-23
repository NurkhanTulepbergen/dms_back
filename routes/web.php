<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

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

Route::get('/_debug/filament', function () {
    $user = auth('web')->user();

    return response()->json([
        'auth' => auth('web')->check(),
        'user' => [
            'id' => $user?->id,
            'email' => $user?->email,
            'role' => $user?->role,
        ],
        'can_access_panel_admin' => $user?->canAccessPanel(Filament::getPanel('admin')),
    ]);
});

Route::get('/', function () {
    return view('welcome');
});
