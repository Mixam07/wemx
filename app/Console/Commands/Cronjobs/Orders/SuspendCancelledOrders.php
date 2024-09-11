<?php

namespace App\Console\Commands\Cronjobs\Orders;

use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Console\Command;
use App\Models\ErrorLog;
use App\Models\Order;

class SuspendCancelledOrders extends Command
{
    protected $signature = 'cron:orders:suspend-cancelled';
    protected $description = 'Suspend orders that are cancelled and past grace period';

    public function handle()
    {
        $cancelled_orders = Order::whereStatus('cancelled')->where('cancelled_at', '<', now())->get();

        $this->info("Loaded a list of expired orders ". $cancelled_orders->count());
        $progressBar = $this->output->createProgressBar(count($cancelled_orders));
        $progressBar->start();

        foreach ($cancelled_orders as $order) {

            // make sure one time orders are skipped
            if(!$order->isRecurring()) {
                continue;
            }

            try {

                $order->due_date = now();
                $order->save();

                // suspend the order
                $order->suspend();
            } catch(\Exception $error) {
                // Catch any exceptions thrown by the service handler
                // Handle the error appropriately and register it as an event
                ErrorLog::updateOrCreate([
                    'source' => 'cron:orders:suspend-expired',
                    'severity' => 'CRITICAL',
                    'message' => "Automatic suspension service was unable to suspend order $order->id - Error: ". $error->getMessage(),
                ]);

                $order->forceSuspend();
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line(''); // Move to the next line after progress bar completion

        $this->info('Task Completed: all cancelled orders were suspended');

    }
}
