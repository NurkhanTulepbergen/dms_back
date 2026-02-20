<?php

namespace Modules\Penalty\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class PenaltyRedemption extends Model
{
    protected $fillable = [
        'penalty_id',
        'user_id',
        'event_type',
        'description',
        'file_path',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function penalty()
    {
        return $this->belongsTo(Penalty::class, 'penalty_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
