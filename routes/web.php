<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
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

Route::get('/_debug/gates', function () {
    $u = auth('web')->user();

    return response()->json([
        'auth' => auth('web')->check(),
        'user' => [
            'id' => $u?->id,
            'email' => $u?->email,
            'role' => $u?->role,
            'class' => $u ? get_class($u) : null,
        ],
        'allows_viewAny_modules_user' => $u ? Gate::forUser($u)->allows('viewAny', \Modules\User\Models\User::class) : null,
        'allows_viewAny_app_user' => class_exists(\App\Models\User::class) && $u
            ? Gate::forUser($u)->allows('viewAny', \App\Models\User::class)
            : null,
    ]);
});

Route::get('/', function () {
    return view('welcome');
});
