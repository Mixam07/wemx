<?php

namespace App\Services\Pterodactyl\Api\Client\Server;

use App\Services\Pterodactyl\Api\Pterodactyl;

class Network extends Pterodactyl
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
        return $this->ptero->makeRequest('GET', $this->endpoint . '/' . $uuidShort . '/network/allocations');
    }

    /**
     * Auto create allocation if enabled
     * @param string $uuidShort
     * @return mixed
     */
    public function assignAllocation(string $uuidShort): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/network/allocations');
    }

    /**
     * @param string $uuidShort
     * @param string $allocationId
     * @param string $note
     * @return mixed
     */
    public function setNote(string $uuidShort, string $allocationId, string $note): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/network/allocations/' . $allocationId, ['notes' => $note]);
    }

    /**
     * @param string $uuidShort
     * @param string $allocationId
     * @return mixed
     */
    public function setPrimary(string $uuidShort, string $allocationId): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $uuidShort . '/network/allocations/' . $allocationId . '/primary');
    }

    /**
     * @param string $uuidShort
     * @param string $allocationId
     * @return mixed
     */
    public function delete(string $uuidShort, string $allocationId): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $uuidShort . '/network/allocations/' . $allocationId);
    }
}
