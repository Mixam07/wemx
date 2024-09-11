<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Backups extends Pterodactyl
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
    public function all(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/backups');
    }

    /**
     * @param string $uuidShort
     * @param string $backup
     * @return mixed
     */
    public function get(string $uuidShort, string $backup): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/backups/' . $backup);
    }

    /**
     * @param string $uuidShort
     * @param string $backup
     * @return mixed
     */
    public function download(string $uuidShort, string $backup): mixed
    {
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/backups/' . $backup . '/download');
    }

    /**
     * @param string $uuidShort
     * @param string $name
     * @param string $ignoredFiles
     * @return mixed
     */
    public function create(string $uuidShort, string $name, string $ignoredFiles = ''): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/backups',
            ['name' => $name, 'ignored' => $ignoredFiles]);
    }

    /**
     * @param string $uuidShort
     * @param string $backup
     * @return mixed
     */
    public function delete(string $uuidShort, string $backup): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $uuidShort . '/backups/' . $backup);
    }

    /**
     * @param string $uuidShort
     * @param string $backup
     * @param bool $truncate
     * @return mixed
     */
    public function restore(string $uuidShort, string $backup, bool $truncate = false): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/backups/' . $backup . '/restore', ['truncate' => $truncate]);
    }

    /**
     * @param string $uuidShort
     * @param string $backup
     * @return mixed
     */
    public function lockToggle(string $uuidShort, string $backup): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/backups/' . $backup . '/lock');
    }



}
