<?php

namespace Modules\Requests\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Dormitory\Models\Room;
use Modules\User\Models\User;

class RepairRequest extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'handled_by_id',
        'category',
        'title',
        'description',
        'status',
        'employee_comment',
        'started_at',
        'resolved_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by_id');
    }

    public function attachments()
    {
        return $this->hasMany(RepairRequestAttachment::class, 'repair_request_id');
    }
}
