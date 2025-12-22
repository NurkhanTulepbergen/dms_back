<?php

namespace Modules\Dormitory\Models;
use Illuminate\Database\Eloquent\Model;
class Building extends Model
{
    protected $fillable = [
        'address',
        'total_floors'
    ];

    public function floors() {
        return $this->hasMany(Floor::class);
    }
}
