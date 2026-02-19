<?php

namespace Modules\Settlement\Models;

use Modules\Dormitory\Models\Room;
use Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Settlement\Database\Factories\SettlementFactory;

class Settlement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'start_at',
        'end_at',
        'status',
        'source',
        'end_reason',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // protected static function newFactory(): SettlementFactory
    // {
    //     // return SettlementFactory::new();
    // }
}
