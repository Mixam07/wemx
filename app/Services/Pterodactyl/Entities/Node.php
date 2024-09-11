<?php

namespace App\Services\Pterodactyl\Entities;

use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;
use Generator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Node
{
    // Error message constants
    private const API_FREE_PORTS_ERROR = "[Node] An error occurred while trying to get free ports: ";
    private const PORTS_UNIQUE_COUNT_ERROR = "[Node] Not enough available ports to generate the requested number. ";
    private const API_GREAT_ALLOCATIONS_ERROR = "[Node] Error creating ports for server";


    public ?array $data = null;

    public function __construct($node_id)
    {
        if ($node_id) {
            $this->data = self::getById($node_id);
        }
    }

    public function api(): Pterodactyl
    {
        return app(PteroUtil::class)->api();
    }

    /**
     * Get all free ports for the node.
     *
     * @return array [id => port]
     */
    public function getFreePorts(): array
    {
        try {
            return $this->api()->allocations->getFreePorts($this->data['id']);
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Node::getFreePorts', self::API_FREE_PORTS_ERROR . $e->getMessage(), 'CRITICAL');
            redirect()->back()->with('error', self::API_FREE_PORTS_ERROR . $e->getMessage())->send();
            return [];
        }
    }

    /**
     * Fetch the specified amount of free ports for the node.
     *
     * @param int $amount
     * @return array [id => port]
     */
    public function fetchRequiredFreePorts(int $amount): array
    {
        try {
            $freePorts = $this->getFreePorts();
            if (count($freePorts) < $amount) {
                $ports = $this->generateUniquePorts($amount - count($freePorts), $freePorts);
                $params = ['ip' => $this->data['ip'], 'ports' => array_map('strval', $ports)];

                $query = $this->api()->allocations->create($this->data['id'], $params);
                if ($query->successful()) {
                    $freePorts = $this->api()->allocations->getFreePorts($this->data['id']);
                } else {
                    redirect()->back()->with('error', self::API_GREAT_ALLOCATIONS_ERROR)->send();
                }
            }
            return array_slice($freePorts, 0, $amount, true);
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Node::fetchRequiredFreePorts', self::API_FREE_PORTS_ERROR . $e->getMessage(), 'CRITICAL');
            redirect()->back()->with('error', self::API_FREE_PORTS_ERROR . $e->getMessage())->send();
            return [];
        }
    }


    // Static Methods
    public static function all(): array
    {
        if (Cache::has(PteroUtil::NODES_DATA_KEY)) {
            return Cache::get(PteroUtil::NODES_DATA_KEY);
        }

        try {
            $nodes = ptero()->api()->node->all();
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Node::all', $e->getMessage(), 'CRITICAL');
            redirect()->back()->with('error', $e->getMessage())->send();
            return [];
        }

        $data = [];
        $db_nodes = DB::table('pterodactyl_nodes')->get();
        foreach ($nodes['data'] ?? [] as $node) {
            $db_node = $db_nodes->firstWhere('node_id', $node['attributes']['id']) ?? [];
            if (!empty($db_node)) {
                $db_node = ['ports_range' => $db_node->ports_range ?? "49152-65535", 'ip' => $db_node->ip ?? $node['attributes']['fqdn']];
            }
            $data[$node['attributes']['id']] = array_merge($node['attributes'], $db_node, Node::getNodeStatus($node['attributes']));
        }
        Cache::put(PteroUtil::NODES_DATA_KEY, $data, PteroUtil::TIME);
        return $data;
    }

    public static function getById(int $id): array
    {
        return self::all()[$id] ?? [];
    }

    public static function getByLocationsIds(array $locationIds): array
    {
        $filteredNodes = [];
        $nodes = self::all();
        foreach ($nodes as $node) {
            if (in_array($node['location_id'], $locationIds)) {
                $filteredNodes[$node['id']] = $node;
            }
        }
        return $filteredNodes;
    }

    /**
     * The method is used to check the resources of the node and returns
     * all given nodes with additional parameters about the status of the node
     *
     * @param array $nodeData
     * @param array|null $requiredResources
     * @return array
     */
    public static function getNodeStatus(array $nodeData, array $requiredResources = null): array
    {
        $totalMemory = $nodeData['memory'];
        $usedMemory = $nodeData['allocated_resources']['memory'];
        $totalDisk = $nodeData['disk'];
        $usedDisk = $nodeData['allocated_resources']['disk'];

        $totalMemoryAvailable = $nodeData['memory_overallocate'] == -1 ? PHP_INT_MAX :
            $totalMemory + round($totalMemory * ($nodeData['memory_overallocate'] / 100));
        $totalDiskAvailable = $nodeData['disk_overallocate'] == -1 ? PHP_INT_MAX :
            $totalDisk + round($totalDisk * ($nodeData['disk_overallocate'] / 100));

        if ($requiredResources) {
            $usedMemory += $requiredResources['memory'] ?? 0;
            $usedDisk += $requiredResources['disk'] ?? 0;
        }

        $isFull = ($usedMemory > $totalMemoryAvailable || $usedDisk > $totalDiskAvailable);
        $availableMemory = max($totalMemoryAvailable - $usedMemory, 0);
        $availableDisk = max($totalDiskAvailable - $usedDisk, 0);

        if ($isFull) {
            ErrorLog('pterodactyl::Node::getNodeStatus', 'Node name: ' . $nodeData['name'] . ' is full', 'WARNING');
        }

        return ['is_full' => $isFull, 'available_memory' => $availableMemory, 'available_disk' => $availableDisk];
    }


    // Private Methods
    private function getPortRange(): string
    {
        return $this->data['ports_range'] ?? '49152-65535';
    }

    private function generateUniquePorts(int $numPorts, array $excludedPorts = []): array
    {
        $range = $this->getPortRange();
        [$start, $end] = explode('-', $range);

        $excludedPorts = array_flip($excludedPorts);
        $ports = [];

        foreach ($this->randomPortGenerator($start, $end) as $port) {
            if (isset($excludedPorts[$port])) {
                continue;
            }
            $ports[(string)$port] = $port;
            if (count($ports) >= $numPorts) {
                break;
            }
        }

        if ($numPorts > count($ports)) {
            ErrorLog('pterodactyl::Node::generateUniquePorts', self::PORTS_UNIQUE_COUNT_ERROR, 'CRITICAL');
            redirect()->back()->with('error', self::PORTS_UNIQUE_COUNT_ERROR)->send();
        }
        return $ports;
    }

    private function randomPortGenerator(int $start, int $end): Generator
    {
        $rangeSize = $end - $start + 1;
        while (true) {
            yield $start + mt_rand(0, $rangeSize - 1);
        }
    }

}
