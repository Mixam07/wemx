<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Startup extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = 'api/client/servers';
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function variables(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/startup');
    }

    /**
     * @param string $uuidShort
     * @param array $data
     * @return mixed
     */
    public function update(string $uuidShort, array $data): mixed
    {
        return $this->ptero->makeRequest('PUT', $this->endpoint . '/' . $uuidShort . '/startup/variable', $data);
    }
}
