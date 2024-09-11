<?php

namespace Modules\Tickets\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;

class TicketTimeline extends Model
{
    use HasFactory;
    protected $table = 'ticket_timeline';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ticket_id',
        'type',
        'content',
        'data',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'collection',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($timeline) {
            $timeline->ticket->touch();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Create a message on the timeline
     * 
     * required
     * - user_id
     * - ticket_id
     * - message
     * - data (optional)
     */
    public static function createMessage(array $data) 
    {
        TicketTimeline::create([
            'user_id' => $data['user_id'],
            'ticket_id' => $data['ticket_id'],
            'type' => 'message',
            'content' => $data['message'],
            'data' => $data['data'] ?? null,
        ]);
    }

    /**
     * Get messages for a ticket from the timeline
     */
    public function getMessages($ticket_id) 
    {
        return TicketTimeline::where('type', 'message')
                            ->where('ticket_id', $ticket_id);
    }
}