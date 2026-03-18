<?php

namespace Modules\Dormitory\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'total_floors',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'total_floors' => 'integer',
    ];

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }
}
