<?php

namespace Modules\Requests\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\Dormitory\Models\Room;
use Modules\User\Models\DormStudent;
use Modules\User\Models\User;

class RequestLive extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'status',
        'documents'
    ];

    public function student() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room() {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
