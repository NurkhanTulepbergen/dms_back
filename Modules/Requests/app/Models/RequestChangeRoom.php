<?php

namespace Modules\Requests\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Dormitory\Models\Room;
use Modules\User\Models\DormStudent;

class RequestChangeRoom extends Model
{
    protected $fillable = [
        'student_id',
        'room_id',
        'status',
        'description',
    ];

    public function student() {
        return $this->belongsTo(DormStudent::class, 'student_id', 'user_id');
    }

    public function room() {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
