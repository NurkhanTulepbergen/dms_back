<?php

namespace Modules\User\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'role',
        'email',
        'password',
        'phone_number',
        'lastname',
        'name',
        'middlename',
        'uni_id',
        'gender',
    ];

    public function dormStudent() {
        return $this->hasOne(DormStudent::class, 'user_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        Log::info('canAccessPanel()', [
            'panel' => $panel->getId(),
            'user_id' => $this->id,
            'email' => $this->email,
            'role_raw' => $this->role,
        ]);

        return $this->role === 'admin';
    }
}
