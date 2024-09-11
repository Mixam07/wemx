<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Locations extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/locations';
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
        return $this->ptero->makeRequest('GET', $this->endpoint . '?include=nodes');
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
     * @param string $short
     * @param string $long
     * @return mixed
     */
    public function create(string $short, string $long): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint, ['short' => $short, 'long' => $long]);
    }

    /**
     * @param int $id
     * @param string $short
     * @param string $long
     * @return mixed
     */
    public function update(int $id, string $short, string $long): mixed
    {
        return $this->ptero->makeRequest('PATCH', $this->endpoint . '/' . $id, ['short' => $short, 'long' => $long]);
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
