<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Dormitory\Filament\Resources\Buildings\BuildingResource;
use Modules\Dormitory\Models\Building;

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

Route::get('/_debug/buildings-access', function () {
    $authUser = auth('web')->user();
    $filamentUser = Filament::auth()->user();

    return response()->json([
        'auth_web' => auth('web')->check(),
        'auth_web_user' => [
            'id' => $authUser?->id,
            'email' => $authUser?->email,
            'role' => $authUser?->role,
            'class' => $authUser ? get_class($authUser) : null,
        ],
        'filament_auth_user' => [
            'id' => $filamentUser?->id,
            'email' => $filamentUser?->email,
            'role' => $filamentUser?->role,
            'class' => $filamentUser ? get_class($filamentUser) : null,
        ],
        'building_resource' => [
            'can_access' => BuildingResource::canAccess(),
            'can_view_any' => BuildingResource::canViewAny(),
        ],
        'gate' => [
            'viewAny_building' => $authUser
                ? Gate::forUser($authUser)->allows('viewAny', Building::class)
                : null,
            'create_building' => $authUser
                ? Gate::forUser($authUser)->allows('create', Building::class)
                : null,
        ],
    ]);
});

Route::get('/', function () {
    return view('welcome');
});
