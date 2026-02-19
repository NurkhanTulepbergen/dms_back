<?php

namespace Modules\Dormitory\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\RoomType;

class Room extends Model
{
    protected $fillable = [
        'floor_id',
        'room_type_id',
        'room_number',
        'capacity',
        'live_cap',
        'is_active',
    ];

    public function floor() {
        return $this->belongsTo(Floor::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
}
