<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Settings extends Pterodactyl
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
     * @param string $name
     * @return mixed
     */
    public function rename(string $uuidShort, string $name): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/settings/rename', ['name' => $name]);
    }

    /**
     * @param string $uuidShort
     * @return mixed
     */
    public function reinstall(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/settings/reinstall');
    }

    /**
     * @param string $uuidShort
     * @param string $docker_image
     * @return mixed
     */
    public function setDockerImage(string $uuidShort, string $docker_image): mixed
    {
        return $this->ptero->makeRequest('PUT', $this->endpoint . '/' . $uuidShort . '/settings/docker-image', ['docker_image' => $docker_image]);
    }



}
