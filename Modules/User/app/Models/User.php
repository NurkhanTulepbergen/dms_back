<?php

namespace Modules\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;

class User extends Authenticatable
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
        return $this->role === 'admin';
    }
}
