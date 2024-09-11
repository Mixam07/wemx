<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PaymentTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'country',
        'included_in_price'
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

}

