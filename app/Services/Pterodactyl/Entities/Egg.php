<?php

namespace App\Services\Pterodactyl\Entities;

use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;
use Illuminate\Support\Facades\Cache;

class Egg
{
    public static function api(): Pterodactyl
    {
        return app(PteroUtil::class)->api();
    }

    public static function allEggs(): array
    {
        if (Cache::has(PteroUtil::EGGS_DATA_KEY)) {
            return Cache::get(PteroUtil::EGGS_DATA_KEY);
        }
        $eggs = [];
        try {
            $nestedData = self::api()->nests->all(['eggs'])['data'] ?? [];
            foreach ($nestedData as $nest) {
                foreach ($nest['attributes']['relationships']['eggs']['data'] as $egg) {
                    $eggs[$egg['attributes']['id']] = array_merge($egg['attributes'], [
                        'nest_uuid' => $nest['attributes']['uuid'],
                        'nest_name' => $nest['attributes']['name'],
                        'nest_desc' => $nest['attributes']['description']
                    ]);
                }
            }
            Cache::put(PteroUtil::EGGS_DATA_KEY, $eggs, PteroUtil::TIME);
            return $eggs;
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Egg::allEggs', 'Error fetching eggs: ' . $e->getMessage());
            return $eggs;
        }
    }

    public static function getEggById(int $id): array
    {
        $cacheKey = PteroUtil::EGGS_DATA_KEY . '_' . $id;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try{
            $eggData = self::api()->eggs->get(self::allEggs()[$id]['nest'], self::allEggs()[$id]['id']);
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Egg::getEggById', 'Error fetching egg: ' . $e->getMessage());
            return [];
        }

        if (!$eggData) {
            return [];
        }

        $egg = $eggData['attributes'];
        foreach ($eggData['attributes']['relationships'] as $key => $relationship) {
            if ($key == 'variables') {
                foreach ($relationship['data'] as $variable) {
                    $egg[$key][$variable['attributes']['id']] = $variable['attributes'];
                }
            } elseif (isset($relationship['data'])) {
                $egg[$key] = $relationship['data'];
            } elseif (isset($relationship['attributes'])) {
                $egg[$key] = $relationship['attributes'];
            }
        }
        unset($egg['relationships']);
        Cache::put($cacheKey, $egg, PteroUtil::TIME);
        return $egg;
    }
}
