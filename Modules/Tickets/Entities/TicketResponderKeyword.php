<?php

namespace Modules\Tickets\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketResponderKeyword extends Model
{
    use HasFactory;
    protected $table = 'ticket_responder_keywords';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'responder_id',
        'keywords',
        'method',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
    ];

    public function keywordsToString()
    {
        return implode(',', $this->keywords);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(TicketResponder::class, 'responder_id');
    }
}