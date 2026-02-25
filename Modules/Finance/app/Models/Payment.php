<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    protected $fillable = [
        'charge_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'amount',
        'status',
        'paid_at',
        'raw_payload',
    ];


    protected $casts = [
        'raw_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
