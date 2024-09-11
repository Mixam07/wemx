<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Servers extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/servers';
    }

    /**
     * @param int $page
     * @return mixed
     */
    public function pagination(int $page): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint, ['page' => $page]);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function all(array $params = []): mixed
    {
        $params = array_merge($params, ['include' => 'egg,nest,allocations,user,node,location']);
        return $this->ptero->makeRequest('GET', $this->endpoint, $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $id . '?include=egg,nest,allocations,user,node,location');
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function getUuid(string $uuid): mixed
    {
        $servers = $this->all();
        foreach ($servers['data'] as $server) {
            if ($server['attributes']['uuid'] === $uuid) {
                return $server;
            }
        }

        return "Server with UUID $uuid not found";
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getExternal(string $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/external/' . $id . '?include=egg,nest,allocations,user,node,location');
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     * $params = [
     * "name" => "Gaming",
     * "user" => 1,
     * "external_id" => "RemoteID1",
     * "description" => "Matt from Wii Sports"
     * ]
     */
    public function update(int $id, array $params): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $id . '/details', $params);
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     * $params = [
     * "allocation" => 1,
     * "swap" => 0,
     * "disk" => 200,
     * "io" => 500,
     * "cpu" => 0,
     * "threads" => null,
     * "feature_limits" => [
     *    "databases" => 5,
     *    "allocations" => 5,
     *    "backups" => 2
     *    ]
     * ]
     */
    public function build(int $id, array $params): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $id . '/build', $params);
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     * $params = [
     * "startup" => "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}",
     * "environment" => [
     *    "SERVER_JARFILE" => "server.jar",
     *    "VANILLA_VERSION" => "latest"
     *    ],
     * "egg" => 5,
     * "image" => "quay.io/pterodactyl/core:java",
     * "skip_scripts" => false
     * ]
     */
    public function startup(int $id, array $params): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $id . '/startup', $params);
    }

    /**
     * @param array $params
     * @return mixed
     * $params = [
     * "name" => "Building",
     * "user" => 1,
     * "egg" => 1,
     * "docker_image" => "quay.io/pterodactyl/core:java",
     * "startup" => "java -Xms128M -Xmx128M -jar server.jar",
     * "environment" => [
     *    "BUNGEE_VERSION" => "latest",
     *    "SERVER_JARFILE" => "server.jar"
     *    ],
     * "limits" => [
     *    "memory" => 128,
     *    "swap" => 0,
     *    "disk" => 512,
     *    "io" => 500,
     *    "cpu" => 100
     *    ],
     * "feature_limits" => [
     *    "databases" => 5,
     *    "backups" => 1
     *    ],
     * "allocation" => ["default" => 17],
     * ];
     */
    public function create(array $params): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint. '?include=egg,nest,allocations,user,node,location', $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function suspend(int $id): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $id . '/suspend');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function unsuspend(int $id): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $id . '/unsuspend');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function reinstall(int $id): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $id . '/reinstall');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function forceDelete(int $id): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $id . '/force');
    }
}
