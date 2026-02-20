<?php

namespace Modules\Penalty\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;

class Penalty extends Model
{
    protected $fillable = [
        'user_id',
        'settlement_id',
        'rule_id',
        'created_by',
        'points',
        'description',
        'status',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }

    public function rule()
    {
        return $this->belongsTo(PenaltyRule::class, 'rule_id');
    }

    public function evidences()
    {
        return $this->hasMany(PenaltyEvidence::class, 'penalty_id');
    }

    public function redemptions()
    {
        return $this->hasMany(PenaltyRedemption::class, 'penalty_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
