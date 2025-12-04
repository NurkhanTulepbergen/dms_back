<?php

namespace App\Models;

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
}
