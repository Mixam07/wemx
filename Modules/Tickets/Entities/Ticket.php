<?php

namespace Modules\Tickets\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Modules\Tickets\Jobs\SendMessageDiscord;
use Modules\Tickets\Jobs\SendTimelineUpdate;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'subject',
        'department_id',
        'is_subscribed',
        'is_open',
        'is_locked',
        'webhook_url',
        'data'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'collection',
    ];

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return parent::newQuery()->orderBy('updated_at', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(TicketDepartment::class);
    }

    public function members()
    {
        return $this->hasMany(TicketMember::class);
    }

    public function botMessage(string $message): void
    {
        TicketTimeline::create([ 
            'ticket_id' => $this->id,
            'user_id' => null,
            'type' => 'bot_response',
            'content' => $message,
            'created_at' => now()->addSeconds(1), // add 1 second to prevent issues on the timeline
        ]);
    }

    public function createMessage(array $data): void
    {
        TicketTimeline::createMessage([ 
            'ticket_id' => $this->id,
            'user_id' => $data['user_id'],
            'message' => $data['message'],
        ]);

        if(auth()->check()) {
            $message = strip_tags($data['message']);
            if(auth()->user()->id != $this->user->id AND $this->is_subscribed) {
                $this->user->email([
                    "subject" => "New Response [{$this->subject}]",
                    "content" => "Your ticket received a new response. Please click the button below to view your ticket.",
                    "button" => [
                        'name' => 'View Ticket',
                        'url' => route('tickets.view', $this->id)
                    ],
                ]);
            }

            if($this->webhook_url) {
                SendMessageDiscord::dispatch($this, auth()->user(), $message);
            }
        }

        $this->checkForKeywords($data['message']);
    }

    protected function checkForKeywords($comment) {
        $keywords = TicketResponderKeyword::all();

        foreach ($keywords as $keyword) {
            $method = $keyword->method;
            if (Str::$method(strtolower($comment), $keyword->keywords)) {
                $this->botMessage($keyword->responder->template);
            }
        }
    }
    
    public function updateTimeline(array $data): void
    {
        $timeline = TicketTimeline::create([ 
            'ticket_id' => $this->id,
            'user_id' => $data['user_id'] ?? auth()->user()->id ?? null,
            'type' => $data['type'],
            'content' => $data['content'],
            'data' => $data['data'] ?? null,
        ]);

        if($timeline->user_id) {
            if($this->webhook_url) {
                SendTimelineUpdate::dispatch($this, strip_tags($data['content']));
            }
        }
    }

    public function getMessages()
    {
        return TicketTimeline::where('ticket_id', $this->id)->where('type', 'message');
    } 

    public function timeline() 
    {
        return TicketTimeline::where('ticket_id', $this->id);
    }

    public function closeOrOpen()
    {
        if($this->is_open) {
            $this->close();
            return;
        } 
        
        $this->open();
    }

    public function close(): void
    {
        $this->update(['is_open' => false]);
        $this->updateTimeline([
            'type' => 'closed',
            'content' => 'Ticket was closed',
        ]);
    }

    public function open(): void
    {
        $this->update(['is_open' => true]);
        $this->updateTimeline([
            'type' => 'reopened',
            'content' => 'Ticket was reopened',
        ]);
    }

    public function lockOrUnlock()
    {
        if($this->is_locked) {
            $this->unlock();
            return;
        } 
        
        $this->lock();
    }

    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
        $this->updateTimeline([
            'type' => 'unlocked',
            'content' => 'Ticket was unlocked',
        ]);
    }

    public function lock(): void
    {
        if($this->webhook_url) {
            $this->update(['webhook_url' => null]);
        }

        
        $this->update(['is_locked' => true, 'is_open' => false]);
        $this->updateTimeline([
            'type' => 'locked',
            'content' => 'Ticket was locked',
        ]);
    }
}