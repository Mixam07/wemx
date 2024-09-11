<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Node extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/nodes';
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
     * @return mixed
     */
    public function all(): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function config(int $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $id . '/configuration');
    }

    /**
     * @param array $params
     * @return mixed
     * $params = [
     * "name" => "New Node",
     * "location_id" => 1,
     * "fqdn" => "node2.example.com",
     * "scheme" => "https",
     * "memory" => 10240,
     * "memory_overallocate" => 0,
     * "disk" => 50000,
     * "disk_overallocate" => 0,
     * "upload_size" => 100,
     * "daemon_sftp" => 2022,
     * "daemon_listen" => 8080
     * ]
     */
    public function create(array $params): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint, $params);
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     * * $params = [
     * "name" => "New Node",
     * "description" => "Test",
     * "location_id" => 1,
     * "fqdn" => "node2.example.com",
     * "scheme" => "https",
     * "behind_proxy" => false,
     * "maintenance_mode" => false,
     * "memory" => 10240,
     * "memory_overallocate" => 0,
     * "disk" => 50000,
     * "disk_overallocate" => 0,
     * "upload_size" => 100,
     * "daemon_sftp" => 2022,
     * "daemon_listen" => 8080
     * ]
     */
    public function update(int $id, array $params): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $id, $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $id);
    }
}
