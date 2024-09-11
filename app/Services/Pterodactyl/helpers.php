<?php

if (!function_exists('ptero')) {
    function ptero(): App\Services\Pterodactyl\Entities\PteroUtil
    {
        return app(App\Services\Pterodactyl\Entities\PteroUtil::class);
    }
}

if (!function_exists('serviceHelper')) {
    function serviceHelper(): App\Services\Pterodactyl\Entities\ServiceHelper
    {
        return app(App\Services\Pterodactyl\Entities\ServiceHelper::class);
    }
}

if (!function_exists('bytesToHuman')) {
    function bytesToHuman($bytes, $precision = 2): string
    {
        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        if ($bytes <= 0) {
            return '0 Bytes';
        }
        $exponent = floor(log($bytes, 1024));
        $value = round($bytes / pow(1024, $exponent), $precision);
        return $value . ' ' . $units[$exponent];
    }
}

if (!function_exists('megabytesToGigabytes')) {
    function megabytesToGigabytes($megabytes, $precision = 2): float
    {
        $gigabytes = $megabytes / 1024;
        return round($gigabytes, $precision);
    }
}

if (!function_exists('parseLocationName')) {
    function parseLocationName($useShort = false, $short = '', $description = '')
    {
        $isEmpty = empty($short) ? $description : $short;
        return $useShort ? $isEmpty : (empty($description) ? $short : $description);
    }
}




