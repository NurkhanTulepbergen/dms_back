<?php

namespace Modules\BuySell\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class BuySellListing extends Model
{
    protected $table = 'buy_sell_listings';

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'condition',
        'price',
        'pickup_location',
        'contact_phone',
        'status',
        'description',
        'image_paths',
        'published_at',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'image_paths' => 'array',
            'published_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
