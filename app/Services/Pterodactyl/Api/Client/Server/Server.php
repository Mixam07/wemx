<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Server extends Pterodactyl
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
    public function resources(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/resources');
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function details(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '?include=egg,subusers');
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function websocket(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/websocket');
    }

    /**
     * @param string $uuidShort
     * @param $signal
     * @return mixed
     */
    public function power(string $uuidShort, $signal): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/power', ['signal' => $signal]);
    }

    /**
     * @param string $uuidShort
     * @param string $command
     * @return mixed
     */
    public function command(string $uuidShort, string $command): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/command', ['command' => $command]);
    }
}
