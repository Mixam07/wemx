<?php

namespace App\Services\Pterodactyl\Entities;

use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;
use Illuminate\Support\Facades\Cache;

class Location
{
    public function api(): Pterodactyl
    {
        return app(PteroUtil::class)->api();
    }

    /**
     * Get all locations or specific locations based on an optional array of IDs.
     *
     * @param array|null $locationIds Array of location IDs to filter by.
     * @return array
     */
    public function allLocations(array $locationIds = null): array
    {
        try {
            $allLocations = Cache::remember(PteroUtil::LOCATIONS_DATA_KEY, PteroUtil::TIME, function () {
                $locations = $this->api()->locations->all();
                $allLocations = [];
                foreach ($locations['data'] ?? [] as $location) {
                    $location['attributes']['nodes'] = Node::getByLocationsIds([$location['attributes']['id']]);
                    $location['attributes']['is_full'] = collect($location['attributes']['nodes'])->every(fn($node) => $node['is_full']);
                    $allLocations[$location['attributes']['id']] = $location['attributes'];
                }
                return $allLocations;
            });

            if ($locationIds === null) {
                return $allLocations;
            }
            return array_intersect_key($allLocations, array_flip($locationIds));
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Location::allLocations', $e->getMessage(), 'CRITICAL');
            redirect()->back()->with('error', $e->getMessage())->send();
            return [];
        }
    }

    public function getLocationById(int $id): array
    {
        return $this->allLocations()[$id] ?? [];
    }
}
