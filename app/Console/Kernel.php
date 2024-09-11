<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // setup cron queue
        $schedule->command('queue:start')->everyMinute();

        // cron tasks
        $schedule->command('subscriptions:check')->daily();
        $schedule->command('cron:orders:suspend-expired')->hourly();
        $schedule->command('cron:orders:suspend-cancelled')->hourly();
        $schedule->command('cron:orders:terminate-suspended')->hourly();
        $schedule->command('cron:payments:delete-expired')->hourly();
        $schedule->command('permissions:save')->everyMinute();
        $schedule->command('user:remove-requested')->everyMinute();

        // packages
        $schedule->command('cloudflare:reload')->daily();

        // check if cronjobs are active
        $schedule->call(function() { $this->checkCronStatus(); })->everyTenSeconds();

        // check if queue is active
        $schedule->call(function() { $this->checkQueueStatus(); })->everyTenSeconds();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function checkCronStatus()
    {
        if(!Cache::has('cron_active')) {
            Cache::put('cron_active', true, 10800);
        }
    }

    protected function checkQueueStatus()
    {
        if(!Cache::has('queue_active') AND !Cache::has('last_queue_check_at')) {
            \App\Jobs\CheckQueue::dispatch();
            Cache::put('last_queue_check_at', now(), 10800);
        }
    }
}
