<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Eggs extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/nests';
    }

    /**
     * @param int $nest_id
     * @return mixed
     */
    public function all(int $nest_id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $nest_id . '/eggs?include=nest,servers,variables');
    }

    /**
     * @param int $nest_id
     * @param int $egg_id
     * @return mixed
     */
    public function get(int $nest_id, int $egg_id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $nest_id . '/eggs/' . $egg_id . '?include=nest,servers,variables');
    }
}
