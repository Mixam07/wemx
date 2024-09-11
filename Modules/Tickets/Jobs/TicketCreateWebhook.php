<?php 

namespace Modules\Tickets\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class TicketCreateWebhook implements ShouldQueue
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
        if(!settings('tickets::discord_server', false)) {
            return;
        }

        if(!settings('encrypted::tickets::api_key')) {
            return;
        }

        $response = Http::withHeaders(['WEBHOOK_SECRET' => settings('encrypted::tickets::discord_webhook_secret')])->post('http://beta.wemx.net:3000/webhook', array_merge([
            'discord_server' => settings('tickets::discord_server', ''),
            'discord_channel' => settings('tickets::discord_channel_id', ''),
            'api_key' => settings('encrypted::tickets::api_key'),
        ], $ticket->toArray()));
        if(!isset($response['webhookUrl'])) {
            ErrorLog('tickets::create::webhook', 'The webhook URL was not returned by the discord bot!');
            return;
        }

        $ticket->update(['webhook_url' => $response['webhookUrl']]);

        Http::post($ticket->webhook_url, [
            "username" => settings('app_name'),
            'content' => "New Ticket Created #{$ticket->id}",
            
            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => $ticket->subject,

                    // URL of title link
                    "url" => route('tickets.view', $ticket->id),

                    // Embed Type
                    "type" => "rich",

                    // Timestamp of embed must be formatted as ISO8601
                    "timestamp" => $ticket->created_at,

                    // Embed left border color in HEX
                    "color" => hexdec( "3366ff" ),

                    // Author
                    "author" => [
                        "name" => $ticket->user->username,
                        "url" => route('users.edit', $ticket->user->id),
                    ],

                    // Additional Fields array
                    "fields" => [
                        // Field 1
                        [
                            "name" => "User",
                            "value" => $ticket->user->username . " ({$ticket->user->email})",
                            "inline" => false
                        ],
                        // Field 2
                        [
                            "name" => "Department",
                            "value" => $ticket->department->name,
                            "inline" => false
                        ],
                        [
                            "name" => "Subject",
                            "value" => $ticket->subject,
                            "inline" => false
                        ]
                        // Etc..
                    ]
                ]
            ]
        ]);

        SendMessageDiscord::dispatch($ticket, $ticket->user, $this->message);
    }
}