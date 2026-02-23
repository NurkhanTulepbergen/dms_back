<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
