<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;


class Charge extends Model
{
    protected $fillable = [
        'user_id',
        'settlement_id',
        'gym_plan_id',
        'amount',
        'currency',
        'type',
        'period_start',
        'period_end',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
