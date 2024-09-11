<?php

namespace App\Console\Commands;

use App\Models\Gateways\Gateway;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Payment;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update subscription statuses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $orders = Order::getExpiredOrders();
        $this->info("Found {$orders->count()} to check");
        $orders->each(function ($order) {
            $subscription = $order->payments->sortByDesc('created_at')->firstWhere('type', 'subscription');
            if ($subscription != null) {
                $class = new $subscription->gateway['class'];
                $gateway = Gateway::where('class', $subscription->gateway['class'])->first();
                if  ($class::checkSubscription($gateway, $subscription->transaction_id)) {
                    $order->extend($subscription->price['period']);
                    $this->info("Subscription with ID: {$subscription->transaction_id} successfully updated");
                    $this->extendEmail($order->user, $subscription);
                } else {
                    $order->suspend();
                    $subscription->update(['status' => 'unpaid']);
                    $this->warn("Subscription with ID: {$subscription->transaction_id} has been canceled due to non-payment");
                    $this->suspendEmail($order->user, $subscription);
                }
            }
        });
        $this->info('Subscription statuses checked and updated.');
    }

    private function extendEmail(User $user, Payment $payment): void
    {
        app()->setLocale($user->language);
        $user->email([
            'subject' => __('client.email_subscription_payment_completed_subject'),
            'content' =>
                emailMessage('subscription_paid', $user->language) . __('client.email_subscription_payment_content', [
                    'id' => $payment->id,
                    'currency' => $payment->currency,
                    'amount_rounded' => $payment->amount,
                    'description' => $payment->description,
                    'gateway_name' => $payment->gateway['name']
                ]),
            'button' => [
                'name' => __('client.email_payment_completed_button'),
                'url' => route('invoice', ['payment' => $payment->id]),
            ],
        ]);
    }

    private function suspendEmail(User $user, Payment $payment): void
    {
        app()->setLocale($user->language);
        $user->email([
            'subject' => __('client.email_subscription_payment_cancel_subject'),
            'content' =>
                emailMessage('subscription_cancel', $user->language) . __('client.email_subscription_payment_cancel_content', [
                    'id' => $payment->id,
                    'currency' => $payment->currency,
                    'amount_rounded' => $payment->amount,
                    'description' => $payment->description,
                    'gateway_name' => $payment->gateway['name']
                ]),
            'button' => [
                'name' => __('client.email_payment_completed_button'),
                'url' => route('invoice', ['payment' => $payment->id]),
            ],
        ]);
    }
}
