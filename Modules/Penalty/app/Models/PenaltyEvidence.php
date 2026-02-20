<?php

namespace Modules\Penalty\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyEvidence extends Model
{
    protected $table = 'penalty_evidences';

    protected $fillable = [
        'penalty_id',
        'file_path',
    ];

    public function penalty()
    {
        return $this->belongsTo(Penalty::class, 'penalty_id');
    }
}
