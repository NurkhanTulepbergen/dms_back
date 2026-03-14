<?php

namespace Modules\Gym\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\Charge;

class GymPlan extends Model
{
    protected $fillable = [
        'name',
        'total_sessions',
        'price',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function memberships()
    {
        return $this->hasMany(GymMembership::class, 'plan_id');
    }

    public function charges()
    {
        return $this->hasMany(Charge::class, 'gym_plan_id');
    }
}
