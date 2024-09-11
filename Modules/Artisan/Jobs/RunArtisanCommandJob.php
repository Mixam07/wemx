<?php

namespace Modules\Artisan\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class RunArtisanCommandJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function handle(): void
    {
        Artisan::call($this->command);
        $output = Artisan::output();
        $logFileName = 'artisan/artisan-commands.log';
        Storage::disk()->append($logFileName, $output);
    }
}
