<?php

namespace Modules\Gym\Models;

use Illuminate\Database\Eloquent\Model;

class GymVisit extends Model
{
    protected $fillable = [
        'membership_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function membership()
    {
        return $this->belongsTo(GymMembership::class, 'membership_id');
    }
}

