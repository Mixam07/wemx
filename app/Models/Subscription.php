<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'price' => 'array',
        'ends_at' => 'datetime',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }
    
}
