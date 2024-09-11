<?php

namespace App\Services\Pterodactyl\Api\Aplications;

use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;

class Allocations extends Pterodactyl
{
    protected string $endpoint;
    protected Pterodactyl $ptero;

    public function __construct(Pterodactyl $ptero)
    {
        $this->ptero = $ptero;
        $this->endpoint = $ptero->api_type . '/nodes';
    }

    /**
     * @param int $node_id
     * @param int|null $page
     * @return mixed
     */
    public function get(int $node_id, int $page = null): mixed
    {
        $resp = $this->ptero->makeRequest('GET', $this->endpoint . '/' . $node_id . '/allocations');
        $total_pages = $resp['meta']['pagination']['total_pages'];
        if ($page == null) {
            $resp = $this->ptero->makeRequest('GET', $this->endpoint . '/' . $node_id . '/allocations', ['page' => $total_pages]);
        } else {
            $resp = $this->ptero->makeRequest('GET', $this->endpoint . '/' . $node_id . '/allocations', ['page' => $page]);
        }
        return $resp;
    }

    /**
     * @param int $node_id
     * @param array $params ['ip' => '0.0.0.0', 'ports' => [25580, 25581]]
     * @return mixed
     */
    public function create(int $node_id, array $params): mixed
    {
        return $this->ptero->makeRequest('POST', $this->endpoint . '/' . $node_id . '/allocations', $params);
    }

    /**
     * @param int $node_id
     * @param int $allocation_id
     * @return mixed
     */
    public function delete(int $node_id, int $allocation_id): mixed
    {
        return $this->ptero->makeRequest('DELETE', $this->endpoint . '/' . $node_id . '/allocations/' . $allocation_id);
    }

    /**
     * @param $nodeId
     * @param bool $includeIP
     * @return array
     * @throws Exception
     */
    public function getFreePorts($nodeId, bool $includeIP = false): array
    {
        $allocations =  $this->fetchAllPages($nodeId);
        $freePorts = [];
        if (!$includeIP) {
            foreach ($allocations as $allocation) {
                if (!$allocation['attributes']['assigned']) {
                    $freePorts[$allocation['attributes']['id']] = $allocation['attributes']['port'];
                }
            }
            return $freePorts;
        }

        foreach ($allocations as $allocation) {
            if (!$allocation['attributes']['assigned']) {
                $freePorts[$allocation['attributes']['port']]['port'] = $allocation['attributes']['port'];
                $freePorts[$allocation['attributes']['port']]['ip'] = $allocation['attributes']['ip'];
            }
        }
        return $freePorts;
    }

    /**
     * @param $nodeId
     * @param bool $includeIP
     * @return array
     * @throws Exception
     */
    public function getAllPorts($nodeId, bool $includeIP = false): array
    {
        $allocations =  $this->fetchAllPages($nodeId);
        $allPorts = [];
        if (!$includeIP) {
            foreach ($allocations as $allocation) {
                $allPorts[$allocation['attributes']['id']] = $allocation['attributes']['port'];
            }
            return $allPorts;
        }

        foreach ($allocations as $allocation) {
            $allPorts[$allocation['attributes']['port']]['port'] = $allocation['attributes']['port'];
            $allPorts[$allocation['attributes']['port']]['ip'] = $allocation['attributes']['ip'];
        }
        return $allPorts;
    }


    /**
     * @param $nodeId
     * @return array
     * @throws Exception
     */
    private function fetchAllPages($nodeId): array
    {
        $page = 1;
        $allData = [];

        do {
            $response = $this->ptero->makeRequest('GET', $this->endpoint . "/$nodeId/allocations?page=$page");

            if (!isset($response['data'])) {
                throw new Exception('Unexpected API response: "data" key is missing.');
            }

            $allData = array_merge($allData, $response['data']);
            if (!isset($response['meta']['pagination'])) {
                break;
            }
            $currentPage = $response['meta']['pagination']['current_page'];
            $totalPages = $response['meta']['pagination']['total_pages'];
            $page++;
        } while ($currentPage < $totalPages);
        return $allData;
    }

}
