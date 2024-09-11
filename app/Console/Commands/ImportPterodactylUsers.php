<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Address;
use Carbon\Carbon;

class ImportPterodactylUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pterodactyl:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from the Pterodactyl Database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $host = $this->ask('What is the database host?', '127.0.0.1');
        $port = $this->ask('What is the database port?', '3306');
        $database = $this->ask('What is the database name?', 'panel');
        $username = $this->ask('What is the database username?', 'pterodactyl');
        $password = $this->secret('What is your DB password?');

        // Setup the connection dynamically
        config(['database.connections.import' => [
            'driver'    => 'mysql',
            'host'      => $host,
            'port'      => $port,
            'database'  => $database,
            'username'  => $username,
            'password'  => $password,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]]);

        // Check the connection
        try {
            DB::connection('import')->getPdo();

            if(Schema::connection('import')->hasTable('users')) {
                // Import users from the external DB
                $pterodactyl_users = DB::connection('import')->table('users')->get();
                $pterodactyl_billing_users = DB::connection('import')->table('billing_users')->get();

                $this->info("Connected successfully to $database. About to import {$pterodactyl_users->count()}");
                if (!$this->confirm('Are you sure you want to continue with importing users?')) {
                    return $this->error('Operation ended');
                }

                foreach ($pterodactyl_users as $user) {

                    if(User::where('id', $user->id)->exists()) {
                        if (!$this->confirm("User with $user->id id already exists, replace existing user with Pterodactyl one?", false)) {
                            continue;
                        }

                        $this->info("Replaced $user->id");
                        User::where('id', $user->id)->first()->delete();
                    }

                    if(User::where('email', $user->email)->exists()) {
                        if (!$this->confirm("User with $user->email email already exists, replace existing user with Pterodactyl one?", false)) {
                            continue;
                        }

                        $this->info("Replaced $user->email");
                        User::where('email', $user->email)->first()->delete();
                    }

                    if(User::where('username', $user->username)->exists()) {
                        $this->error("User with username $user->username already exists. Generating new username for this user.");
                        $user->username = $user->username . rand(1,1000);
                    }

                    $pterodactyl_billing_user = $pterodactyl_billing_users->where('user_id', $user->id)->first();

                    $wemx_user = DB::table('users')->insert([
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->name_first,
                        'last_name' => $user->name_last,
                        'balance' => $pterodactyl_billing_user->balance ?? 0,
                        'status' => 'active',
                        'email_verified_at' => Carbon::now(),
                        'password' => $user->password,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]);

                    $address = DB::table('addresses')->insert([
                        'user_id' => $user->id,
                        'company_name' => NULL,
                        'address' => $pterodactyl_billing_user->address ?? NULL,
                        'address_2' => NULL,
                        'region' => NULL,
                        'country' => $pterodactyl_billing_user->country ?? NULL,
                        'city' => $pterodactyl_billing_user->city ?? NULL,
                        'zip_code' => $pterodactyl_billing_user->postal_code ?? NULL,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                }

                $this->info('Users imported successfully!');
                $this->info('The root administrators email is '. User::whereId(1)->first()->email);
            } else {
                $this->error('Users table does not exist in the provided database.');
            }
        } catch (\Exception $e) {
            $this->error('Could not connect to the database. Please check your input. Error: ' . $e->getMessage());
        }
    }
}
