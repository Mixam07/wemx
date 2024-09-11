<?php 

namespace Modules\Tickets\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendTimelineUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $message;

    public function __construct($ticket, $message)
    {
        $this->ticket = $ticket;
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
            "username" => settings('app_name', 'Tickets'),
            'content' => $this->message,
        ]);
    }
}