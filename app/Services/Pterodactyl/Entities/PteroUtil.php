<?php

namespace App\Services\Pterodactyl\Entities;

use App\Models\Order;
use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;
use Illuminate\Support\Facades\Cache;

class PteroUtil
{

    const TIME = 3600;
    const EGGS_DATA_KEY = 'pterodactyl_eggs_data';
    const NODES_DATA_KEY = 'pterodactyl_nodes_data';
    const LOCATIONS_DATA_KEY = 'pterodactyl_locations_data';
    private ?Pterodactyl $api = null;
    private ?Pterodactyl $clientApi = null;

    public function api($type = 'application'): Pterodactyl
    {
        if (!$this->api) {
            $api_key = settings('encrypted::pterodactyl::api_key', false);
            if (!$api_key or !settings('encrypted::pterodactyl::api_url', false)) {
                redirect()->to('/admin/services/pterodactyl/config')->with('error', 'Please setup your Pterodactyl Panel API credentials')->send();
            }
            $this->api = new Pterodactyl($api_key, settings('encrypted::pterodactyl::api_url', $type));
        }
        return $this->api;
    }

    public function clientApi($type = 'client'): Pterodactyl
    {
        if (!$this->clientApi) {
            $api_key = settings('encrypted::pterodactyl::api_admin_key', false);
            if (!$api_key or !settings('encrypted::pterodactyl::api_url', false)) {
                redirect()->to('/admin/services/pterodactyl/config')->with('error', 'Please setup your Pterodactyl Panel API credentials')->send();
            }
            $this->clientApi = new Pterodactyl($api_key, settings('encrypted::pterodactyl::api_url', $type));
        }
        return $this->clientApi;
    }

    public function user(): User
    {
        return new User($this->api());
    }

    // Static
    public static function node($nodeId): array
    {
        return Node::getById($nodeId);
    }

    public static function server($order_id, $client = false): array
    {
        $server = ptero()->api()->servers->getExternal("wmx-$order_id");
        if($client){
            $server = ptero()->clientApi()->server->details($server['attributes']['identifier']);
        }
        if (empty($server)) {
            ErrorLog('pterodactyl::PteroUtil::server', "Could not locate server with external id wmx-$order_id on Pterodactyl.", 'CRITICAL');
            redirect()->back()->with("error", __('responses.find_server_error', ['order_id' => $order_id]))->send();
        }
        return $server['attributes'];
    }

    public static function serverIP($order_id): string|null
    {
        return Cache::remember("server.ip.order.$order_id", 36000, function () use ($order_id) {
            try {
                $data = ptero()::server($order_id);
                $defaultPort = null;
                foreach ($data['relationships']['allocations']['data'] as $allocation) {
                    if ($allocation['attributes']['id'] == $data['allocation']) {
                        $defaultPort = $allocation['attributes']['port'];
                        break;
                    }
                }
                return self::node($data['node'])['ip'] . ':' . $defaultPort;
            } catch (Exception $e) {
                ErrorLog('pterodactyl::PteroUtil::serverIP', $e->getMessage());
                return null;
            }
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::EGGS_DATA_KEY);
        foreach (Egg::allEggs() as $egg) {
            if (Cache::has(self::EGGS_DATA_KEY . '_' . $egg['id'])) {
                Cache::forget(self::EGGS_DATA_KEY . '_' . $egg['id']);
            }
        }
        Cache::forget(self::NODES_DATA_KEY);
        Cache::forget(self::LOCATIONS_DATA_KEY);
        foreach (Order::whereService('pterodactyl')->get() as $order) {
            Cache::forget("server.ip.order.$order->id");
        }
    }

    public static function determineType(array $rules): array
    {
        $response = ['type' => 'text'];
        $rulesString = implode('|', $rules);

        // Check for boolean or bool
        if (in_array('boolean', $rules) || in_array('bool', $rules)) {
            $response['type'] = 'bool';
        } elseif (preg_match('/\|in:(.*)/', $rulesString, $matches)) {
            // Select type for 'in:' rule
            $response['type'] = 'select';
            $exploded = explode(',', $matches[1]);
            $response['options'] = array_combine($exploded, $exploded);
        } elseif (in_array('numeric', $rules ) || in_array('integer', $rules)) {
            // Type for 'numeric' rule
            $response['type'] = 'number';
            // Finding the maximum value
            if (preg_match('/max:(\d+)/', $rulesString, $maxMatches)) {
                $response['max'] = $maxMatches[1];
            }
            // Finding the minimum value
            if (preg_match('/min:(\d+)/', $rulesString, $minMatches)) {
                $response['min'] = $minMatches[1];
            }
        }
        return $response;
    }


}
