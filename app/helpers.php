<?php

if (!function_exists('gateway')) {
    function gateway($driver): \App\Models\Gateways\Gateway
    {
        return  \App\Models\Gateways\Gateway::where('driver', $driver)->first();
    }
}

if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        if ($key === null) {
            return new \App\Models\Settings;
        }
        return \App\Models\Settings::get($key, $default);
    }
}

if (!function_exists('price')) {

    // format price and return it
    function price(int $price)
    {
        return currency('symbol') . number_format($price, 2) . ' ' . currency('short');
    }

}

if (!function_exists('str_random')) {

    function str_random($chars)
    {
        return \Illuminate\Support\Str::random($chars);
    }

}

if (!function_exists('ErrorLog')) {

    function ErrorLog($source, $error, $severity = 'ERROR')
    {
        return \App\Models\ErrorLog::catch($source, $error, $severity);
    }

}

if (! function_exists('bytesToMB')) {
    /**
     * Convert bytes to megabytes.
     *
     * @param  int $bytes
     * @param  int $precision
     * @return float
     */
    function bytesToMB($bytes, $precision = 2) {
        $megabytes = $bytes / 1024 / 1024;
        return round($megabytes, $precision);
    }
}

if (!function_exists('♙')) {
    function ♙($f154)
    {
        return base64_decode(base64_decode(base64_decode($f154)));
    }
}

if (!function_exists('is_active')) {

    function is_active(string $route, array $params = [], string $activeClass = 'text-primary-600 dark:text-primary-500 dark:border-primary-500 border-primary-600', string $inactiveClass = 'hover:text-gray-600 hover:border-gray-300 dark:hover:border-gray-400 dark:hover:text-gray-300')
    {
        $currentRoute = request()->route();

        if (array_key_exists('module', $params)){
            $request = \Request::create($route, 'GET');
            $route = \Route::getRoutes()->match($request);
            $currentRoute = explode('.', $currentRoute->getName())['0'];
            $routeName = explode('.', $route->getName())['0'];
            if ($routeName == $currentRoute){
                return $activeClass;
            }
            $currentRoute = request()->route();
        }

        if ($currentRoute->getName() !== $route) {
            return $inactiveClass;
        }

        $currentParams = $currentRoute->parameters();
        foreach ($params as $key => $value) {
            if (!isset($currentParams[$key]) || $currentParams[$key] != $value) {
                return $inactiveClass;
            }
        }

        return $activeClass;
    }

}

if (!function_exists('nav_active')) {

    function nav_active($route = null, $contains = false, $dropdown = false, $href = false, $prefix = false): ?string
    {
        $currentRoute = \Route::currentRouteName();

        if (is_array($route) && in_array($currentRoute, $route)) {
            return 'active active-nav show';
        }

        if (!$contains && $currentRoute == $route) {
            return 'active-nav';
        }

        if ($contains && str_contains($currentRoute, $route)) {
            return 'active-nav';
        }

        if ($dropdown && str_contains($currentRoute, $route)) {
            return 'active active-nav show';
        }

        if ($href && request()->path() == trim($route, '/')) {
            return 'active-nav';
        }

        if ($prefix && request()->route()->getPrefix() == $route) {
            return 'active active-nav show';
        }
        return null;
    }
}

if (!function_exists('currency')) {

    function currency($key = 'short')
    {
        return config('utils.currencies.' . settings('currency', 'USD') . '.' . $key);
    }

}

if (!function_exists('lang_module')) {

    function lang_module()
    {
        return new \Modules\Locales\Models\Manager;
    }

}


if (!function_exists('getValueByKey')) {
    function getValueByKey($path, $array, $defaultValue = null) {
        // Split the string into keys
        preg_match_all('/\[([^\]]+)\]|\b(\w+)\b/', $path, $matches);
        $keys = array_filter(array_merge($matches[2], $matches[1]));

        // We iterate through the keys to access the nested value
        foreach ($keys as $key) {
            if (is_array($array) && isset($array[$key])) {
                $array = $array[$key];
            } else {
                // The key was not found, we return the default value
                return $defaultValue;
            }
        }
        return $array;
    }
}

if (!function_exists('emailMessage')) {
    function emailMessage($key, $language = 'en')
    {
        return app(\App\Models\EmailMessage::class)->getContentBuyKey($key, $language);
    }
}

function linkGetBxIcon($icon): string
{
    $icons = explode('></i>', $icon);
    foreach ($icons as $i) {
        if (str_contains($i, 'bx')) {
            return $i . '></i>';
        }
    }
    // Return the first icon if no 'bx' icons are found, or an empty string if no icons at all
    return !empty($icons[0]) ? $icons[0] . '></i>' : '';
}

if (!function_exists('enabledModules')) {
    function enabledModules()
    {
        return Module::allEnabled();
    }
}