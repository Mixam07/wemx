<?php 

namespace Modules\Tickets\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendMessageDiscord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $user;
    protected $message;

    public function __construct($ticket, $user, $message)
    {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->message = $message;

    }

    public function handle()
    {
        if(!settings('tickets::discord_sync', false)) {
            return;
        }
        
        $ticket = $this->ticket;
        if(!$ticket->webhook_url) {
            return;
        }

        Http::post($ticket->webhook_url, [
            "username" => $this->user->username,
            'avatar_url' => $this->user->avatar(),
            'content' => $this->message,
        ]);
    }
}