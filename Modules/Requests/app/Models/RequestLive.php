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
        'preferred_room_id',
        'status',
    ];

    public function student() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function preferredRoom() {
        return $this->belongsTo(Room::class, 'preferred_room_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'request_id');
    }
}
