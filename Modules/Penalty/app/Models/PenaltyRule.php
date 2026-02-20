<?php

namespace Modules\Penalty\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyRule extends Model
{
    protected $fillable = [
        'code',
        'title',
        'default_points',
        'redeemable',
        'creates_financial_charge',
        'financial_amount',
    ];

    protected $casts = [
        'default_points' => 'integer',
        'redeemable' => 'boolean',
        'creates_financial_charge' => 'boolean',
        'financial_amount' => 'decimal:2',
    ];

    public function penalties()
    {
        return $this->hasMany(Penalty::class, 'rule_id');
    }
}
