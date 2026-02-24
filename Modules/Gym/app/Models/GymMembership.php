<?php

namespace Modules\Gym\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\Charge;
use Modules\User\Models\User;

class GymMembership extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'charge_id',
        'total_sessions',
        'remaining_sessions',
        'started_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'date',
        'expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(GymPlan::class, 'plan_id');
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class, 'charge_id');
    }

    public function visits()
    {
        return $this->hasMany(GymVisit::class, 'membership_id');
    }
}

