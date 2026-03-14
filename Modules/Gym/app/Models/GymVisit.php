<?php

namespace Modules\Gym\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class GymVisit extends Model
{
    protected $fillable = [
        'membership_id',
        'user_id',
        'visit_date',
        'check_in_at',
        'check_out_at',
        'duration_minutes',
        'sessions_used',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'duration_minutes' => 'integer',
        'sessions_used' => 'integer',
    ];

    public function membership()
    {
        return $this->belongsTo(GymMembership::class, 'membership_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
