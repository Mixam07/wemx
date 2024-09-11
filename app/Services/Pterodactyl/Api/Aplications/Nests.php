<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Nests extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/nests';
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
     * @param array|null $includes
     * An associative array that may contain the following keys:
     * 'eggs' => string,
     *  'servers' => string,
     * @return mixed
     */
    public function all(array $includes = null): mixed
    {
        $includeString = $includes ? '?include=' . implode(',', $includes) : '';
        return $this->ptero->makeRequest('GET', $this->endpoint . $includeString);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $id);
    }
}
