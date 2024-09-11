<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class OrderServer
{
    public static function savePermission($orderId, $serverUuid): void
    {
        Cache::put('server-' . $orderId, $serverUuid);
    }

    public static function checkPermission($orderId, $serverUuid): void
    {
        $identifier = Cache::get('server-' . $orderId, 'none');
        if ($serverUuid !== $identifier) {
            abort(403, __('responses.no_server_accept'));
        }
    }

    public static function getServerUuid($orderId): string
    {
        return Cache::get('server-' . $orderId, 'none');
    }
}
