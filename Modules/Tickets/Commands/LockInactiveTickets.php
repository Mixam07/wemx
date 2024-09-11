<?php

namespace Modules\Tickets\Commands;

use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Console\Command;
use Modules\Tickets\Entities\Ticket;


class LockInactiveTickets extends Command
{
    protected $signature = 'cron:tickets:lock-inactive';
    protected $description = 'Lock inactive tickets';

    public function handle()
    {
        $tickets = Ticket::where('is_open', false)->get();

        foreach ($tickets as $ticket) {
            if($ticket->is_locked) {
                continue;
            }

            try {
                if($ticket->department->auto_lock_after != 0) {
                    if($ticket->updated_at < now()->subHours($ticket->department->auto_lock_after)) {
                        $ticket->botMessage("There has been no activity for {$ticket->department->auto_lock_after} hours. This ticket will automatically lock.");
                        $ticket->lock();
                    }
                }
            } catch(\Exception $error) {
                $this->info($error);
            }

        }

        $this->line(''); // Move to the next line after progress bar completion
        $this->info('Done');

    }
}
