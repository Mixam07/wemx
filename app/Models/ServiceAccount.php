<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceAccount extends Model
{
    use HasFactory;

    protected $table = 'service_accounts';

    protected $fillable = [
        'user_id',
        'order_id',
        'service',
        'external_id',
        'data',
        'username',
        'password',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $hidden = [
        'data',
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
