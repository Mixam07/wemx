<?php

namespace Modules\Artisan\Console\Commands;

use App\Traits\EnvironmentWriterTrait;
use Illuminate\Console\Command;


class EnvEditorCommand extends Command
{
    use EnvironmentWriterTrait;
    protected $signature = 'env:editor {--key= : The key of the environment variable.} {--value= : The value of the environment variable.}';
    protected $description = 'Edit environment variables.';

    public function handle(): void
    {
        $key = $this->option('key');
        $value = $this->option('value');
        $this->writeToEnvironment([$key => $value]);
        $this->info('Environment variable has been updated.');
    }

}
