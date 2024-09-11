<?php

use Modules\PagePlus\Entities\PageHelper;
use Modules\PagePlus\Entities\PagePlus;
use Illuminate\Support\Facades\Cache;

if (!function_exists('pages_by_location')) {
    function pages_by_location($location = null)
    {
        return Cache::rememberForever(PageHelper::cacheKey('byLocation', $location), function () use ($location) {
            $pages = PagePlus::whereNull('parent_id')->get();
            return $pages->filter(function ($page) use ($location) {
                return $page->getMeta('location') == $location;
            });
        });
    }
}

