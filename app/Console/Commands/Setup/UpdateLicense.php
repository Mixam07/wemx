<?php

namespace App\Console\Commands\Setup;

use App\Traits\EnvironmentWriterTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class UpdateLicense extends Command
{
    use EnvironmentWriterTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:update {license_key?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update your license key';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $license_key = $this->argument('license_key') ?? $this->ask('Enter your license key', config('app.license'));

        try {

            $response = Http::get("https://api.wemx.pro/api/wemx/licenses/$license_key/check");

            if(!$response->successful()) {
                if(isset($response['success']) AND !$response['success']) {
                    return $this->error($response['message']);
                }

                return $this->error('Failed to connect to remote server');
            }

            if($response['success']) {
                $this->writeToEnvironment(['LICENSE_KEY' => $license_key]);
                $this->info("License has been updated to ". $license_key);
            }

        } catch(\Exception $error) {
            return $this->error('Something went wrong, please try again.');
        }
    }
}
