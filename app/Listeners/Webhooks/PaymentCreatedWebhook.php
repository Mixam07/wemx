<?php

namespace App\Listeners\Webhooks;

use App\Events\PaymentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Http;

class PaymentCreatedWebhook
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        $response = Http::post(settings('event_webhook_url'), [
            // Message
            "content" => "Payment Created",
        
            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => "Payment has been created",

                    // add url
                    "url" => route('payments.edit', $payment->id),
        
                    // Embed Type
                    "type" => "rich",
                        
                    // Embed left border color in HEX
                    "color" => hexdec( "059669" ),
        
                    // Additional Fields array
                    "fields" => [
                        [
                            "name" => "Payment ID",
                            "value" => "{$payment->id}",
                            "inline" => false
                        ],
                        [
                            "name" => "Description",
                            "value" => "{$payment->description}",
                            "inline" => false
                        ],
                        [
                            "name" => "Price",
                            "value" => price($payment->amount),
                            "inline" => false
                        ],
                        [
                            "name" => "User",
                            "value" => $user->email ?? 'N/A',
                            "inline" => false
                        ],
                        // Etc..
                    ]
                ]
            ]
        
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(PaymentCreated $event, Throwable $exception): void
    {
        ErrorLog('PaymentCreatedWebhook', $exception->getMessage());
    }
}
