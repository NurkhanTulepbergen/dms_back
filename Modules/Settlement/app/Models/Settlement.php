<?php

namespace Modules\Settlement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Settlement\Database\Factories\SettlementFactory;

class Settlement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Settlement
     * - student_id
     * - room_id
     * - start_date
     * - end_date
     * - status
     */
    protected $fillable = [];

    // protected static function newFactory(): SettlementFactory
    // {
    //     // return SettlementFactory::new();
    // }
}
