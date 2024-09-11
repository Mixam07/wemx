<?php

namespace App\Services\Pterodactyl\Api;

// Applications
use App\Services\Pterodactyl\Api\Aplications\Allocations;
use App\Services\Pterodactyl\Api\Aplications\Eggs;
use App\Services\Pterodactyl\Api\Aplications\Locations;
use App\Services\Pterodactyl\Api\Aplications\Nests;
use App\Services\Pterodactyl\Api\Aplications\Node;
use App\Services\Pterodactyl\Api\Aplications\Servers;
use App\Services\Pterodactyl\Api\Aplications\Users;
use App\Services\Pterodactyl\Api\Client\Server\Backups;
use App\Services\Pterodactyl\Api\Client\Server\Database;
use App\Services\Pterodactyl\Api\Client\Server\FileManager;
use App\Services\Pterodactyl\Api\Client\Server\Network;
use App\Services\Pterodactyl\Api\Client\Server\Schedules;
use App\Services\Pterodactyl\Api\Client\Server\Server;
use App\Services\Pterodactyl\Api\Client\Server\Settings;
use App\Services\Pterodactyl\Api\Client\Server\Startup;
use Exception;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

// Client

/**
 * @property-read Servers $servers
 * @property-read Locations $locations
 * @property-read Allocations $allocations
 * @property-read Users $users
 * @property-read Nests $nests
 * @property-read Eggs $eggs
 * @property-read Node $node
 * @property-read Network $network
 * @property-read Server $clientServer
 * @property-read FileManager $fileManager
 * @property-read Schedules $schedules
 * @property-read Database $database
 * @property-read Backups $backups
 * @property-read Startup $startup
 * @property-read Settings $settings
 */

class Pterodactyl
{
    protected string $api;
    protected string $url;
    protected string $api_type;

    public Servers $servers;
    public Locations $locations;
    public Allocations $allocations;
    public Users $users;
    public Nests $nests;
    public Eggs $eggs;
    public Node $node;


    public Server $server;
    public FileManager $files;
    public Network $network;
    public Database $database;
    public Schedules $schedules;
    public Backups $backups;
    public Startup $startup;
    public Settings $settings;

    /**
     * Summary of __construct
     * @param string $api_key
     * @param string $base_url
     * @param string $api_type
     */
    public function __construct(string $api_key, string $base_url, string $api_type = 'application')
    {
        $this->api = $api_key;
        $this->url = $base_url;
        $this->api_type = 'api/' . $api_type;

        // Applications
        $this->servers = new Servers($this);
        $this->locations = new Locations($this);
        $this->allocations = new Allocations($this);
        $this->users = new Users($this);
        $this->nests = new Nests($this);
        $this->eggs = new Eggs($this);
        $this->node = new Node($this);

        // Client
        $this->server = new Server($this);
        $this->files = new FileManager($this);
        $this->database = new Database($this);
        $this->schedules = new Schedules($this);
        $this->network = new Network($this);
        $this->backups = new Backups($this);
        $this->startup = new Startup($this);
        $this->settings = new Settings($this);
    }

    protected function makeRequest($method, $url, $data = null)
    {

        $method = strtolower($method);
        $allowedMethods = ['get', 'post', 'put', 'delete', 'patch'];

        if (!in_array($method, $allowedMethods)) {
            throw new InvalidArgumentException('Invalid HTTP method.');
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->api,
            'Accept' => 'application/json',
        ];
        try {
            if (is_string($data)) {
                $response = Http::withHeaders($headers)->withBody($data, 'text/plain')->$method($this->url . '/' . $url, $data);
            } else {
                $response = Http::withHeaders($headers)->$method($this->url . '/' . $url, $data);
            }
            if ($response->successful()) {
                return $response->json() ?? $response;
            } else {
                return ['error' => 'Request failed with status code ' . $response->status(), 'response' => $response];
            }
        } catch (Exception $e) {
            return ['error' => 'Exception occurred: ' . $e->getMessage()];
        }
    }

    public function checkAuthorization(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api,
                'Accept' => 'application/json',
            ])->get("$this->url/api/application/nodes");
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkAuthorizationClient(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api,
                'Accept' => 'application/json',
            ])->get("$this->url/api/client/account");
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

}
