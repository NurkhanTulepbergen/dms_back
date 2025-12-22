<?php

namespace Modules\Dormitory\Models;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'floor_id',
        'room_number',
        'capacity',
        'live_cap'
    ];

    public function floor() {
        return $this->belongsTo(Floor::class);
    }
}
