<?php

namespace App\Listeners\Webhooks;

use App\Events\OrderUnsuspended;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Http;

class OrderUnsuspendedWebhook
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
    public function handle(OrderUnsuspended $event): void
    {
        $package = $event->order->package;
        $order = $event->order;

        $renewalPrice = price($order->price['renewal_price']);
        $period = $order->price['type'] == 'recurring' ? $order->period() : 'Once';

        $response = Http::post(settings('event_webhook_url'), [
            // Message
            "content" => "Order Unsuspended",
        
            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => "The following order has been unsuspended",

                    // add url
                    "url" => route('orders.edit', $order->id),
        
                    // Embed Type
                    "type" => "rich",
                        
                    // Embed left border color in HEX
                    "color" => hexdec( "0284c7" ),
        
                    // Additional Fields array
                    "fields" => [
                        [
                            "name" => "Order ID",
                            "value" => "#{$order->id}",
                            "inline" => false
                        ],
                        [
                            "name" => "Order Package",
                            "value" => "{$package->name}",
                            "inline" => false
                        ],
                        [
                            "name" => "Service",
                            "value" => "{$package->service}",
                            "inline" => false
                        ],
                        [
                            "name" => "User",
                            "value" => "{$order->user->username} ({$order->user->email})",
                            "inline" => false
                        ],
                        [
                            "name" => "Order Status",
                            "value" => "{$order->status}",
                            "inline" => false
                        ],
                        [
                            "name" => "Price",
                            "value" => "{$renewalPrice} / {$period}",
                            "inline" => true
                        ],
                        [
                            "name" => "Price Type",
                            "value" => $order->price['type'] ?? 'recurring',
                            "inline" => true
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
    public function failed(OrderUnsuspended $event, Throwable $exception): void
    {
        ErrorLog('OrderUnsuspendedWebhook', $exception->getMessage());
    }
}
