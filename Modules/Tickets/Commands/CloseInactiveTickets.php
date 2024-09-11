<?php

namespace Modules\Tickets\Commands;

use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Console\Command;
use Modules\Tickets\Entities\Ticket;


class CloseInactiveTickets extends Command
{
    protected $signature = 'cron:tickets:close-inactive';
    protected $description = 'Close inactive tickets';

    public function handle()
    {
        $tickets = Ticket::where('is_open', true)->get();

        foreach ($tickets as $ticket) {

            try {
                if($ticket->department->auto_close_after != 0) {
                    if($ticket->updated_at < now()->subHours($ticket->department->auto_close_after)) {
                        $ticket->botMessage("There has been no activity for {$ticket->department->auto_close_after} hours. This ticket will automatically close.");
                        $ticket->close();
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
