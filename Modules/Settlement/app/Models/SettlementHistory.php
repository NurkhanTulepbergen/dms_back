<?php

namespace Modules\Settlement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Settlement\Database\Factories\SettlementHistoryFactory;

class SettlementHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * SettlementHistory
     *  - settlement_id
     *  - action (created, approved, moved, evicted)
     *  - from_room_id
     *  - to_room_id
     *  - performed_by
     *  - created_at
     */
    protected $fillable = [];

    // protected static function newFactory(): SettlementHistoryFactory
    // {
    //     // return SettlementHistoryFactory::new();
    // }
}
