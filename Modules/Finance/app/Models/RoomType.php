<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Dormitory\Models\Room;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'semester_price',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
