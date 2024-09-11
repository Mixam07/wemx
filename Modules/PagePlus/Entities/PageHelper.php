<?php

namespace Modules\PagePlus\Entities;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\PagePlus\Http\Controllers\PagePlusController;

class PageHelper
{
    public static function registerRoutes($page = null, $pathPrefix = ''): void
    {
        try {
            $pages = is_null($page) ? PagePlus::whereNull('parent_id')->get() : $page->children;
            foreach ($pages as $page) {
                $path = $pathPrefix . '/' . $page->slug;
                Route::get($path, [PagePlusController::class, 'show'])->name(str_replace('/', '.', $page->slug))->defaults('slug', $page->slug);
                if ($page->children()->count() > 0) {
                    self::registerRoutes($page, $path);
                }
            }
        } catch (\Exception $e) {
            ErrorLog('pageplus', $e->getMessage());
        }

    }

    public static function cacheKey($key, $id): string
    {
        return sprintf('pagePlus_%s_%s', $key, $id);
    }

    public static function clearAllCache(): void
    {
        // Clear cache for PagePlus
        $pagePlusIds = PagePlus::pluck('id');
        foreach ($pagePlusIds as $id) {
            Cache::forget(self::cacheKey('fullSlug', $id));
            Cache::forget(self::cacheKey('childrenIds', $id));
        }

        // Clear cache for PagePlusMeta
        $pagePlusMeta = PagePlusMeta::all();
        foreach ($pagePlusMeta as $meta) {
            Cache::forget(self::cacheKey('meta', $meta->page_id) . '_' . $meta->key);
        }

        // Clear cache for PagePlusTranslation
        $pagePlusTranslations = PagePlusTranslation::all();
        foreach ($pagePlusTranslations as $translation) {
            Cache::forget(self::cacheKey('translation', $translation->page_id) . '_' . $translation->locale);
        }
    }
}
