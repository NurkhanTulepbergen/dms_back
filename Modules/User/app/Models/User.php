<?php

namespace Modules\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    ];

    public function dormStudent() {
        return $this->hasOne(DormStudent::class, 'user_id');
    }
}
