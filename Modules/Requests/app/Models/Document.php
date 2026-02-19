<?php

namespace Modules\Requests\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'request_id',
        'type',
        'path',
    ];

    public function requestLive()
    {
        return $this->belongsTo(RequestLive::class, 'request_id');
    }
}

