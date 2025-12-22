<?php

namespace Modules\User\Models;
use Illuminate\Database\Eloquent\Model;

class DormStudent extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'user_id',
        'live_status',
        'room_id',
        'start_live',
        'end_live',
        'warning_count'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
