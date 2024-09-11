<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Database extends Pterodactyl
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
    public function get(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/databases?include=password');
    }

    /**
     * @param string $uuidShort
     * @param string $database
     * @param string $remote
     * @return mixed
     */
    public function create(string $uuidShort, string $database, string $remote = "%"): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/databases', ['database' => $database, 'remote' => $remote]);
    }

    /**
     * @param string $uuidShort
     * @param string $database
     * @return mixed
     */
    public function resetPassword(string $uuidShort, string $database): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/databases/' . $database . '/rotate-password');
    }

    /**
     * @param string $uuidShort
     * @param string $database
     * @return mixed
     */
    public function delete(string $uuidShort, string $database): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $uuidShort . '/databases/' . $database);
    }


}
