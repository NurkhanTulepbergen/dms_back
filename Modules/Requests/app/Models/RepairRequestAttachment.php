<?php

namespace Modules\Requests\Models;

use Illuminate\Database\Eloquent\Model;

class RepairRequestAttachment extends Model
{
    protected $fillable = [
        'repair_request_id',
        'file_path',
    ];

    public function repairRequest()
    {
        return $this->belongsTo(RepairRequest::class, 'repair_request_id');
    }
}
