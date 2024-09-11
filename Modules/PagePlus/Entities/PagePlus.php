<?php

namespace Modules\PagePlus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class PagePlus extends Model
{
    protected $fillable = ['parent_id', 'slug', 'icon', 'created_by', 'updated_by', 'order'];

    protected static function booted(): void
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });

        $clearCache = function ($model) {
            Cache::forget(PageHelper::cacheKey('fullSlug', $model->id));
            Cache::forget(PageHelper::cacheKey('childrenIds', $model->id));
            Cache::forget(PageHelper::cacheKey('bySlug', $model->slug));
            Cache::forget(PageHelper::cacheKey('children', $model->parent_id));
            Cache::forget(PageHelper::cacheKey('topmostAncestor', $model->id));
            if ($parent = $model->parent()->first()) {
                Cache::forget(PageHelper::cacheKey('fullSlug', $parent->id));
                Cache::forget(PageHelper::cacheKey('childrenIds', $parent->id));
                Cache::forget(PageHelper::cacheKey('bySlug', $parent->slug));
                Cache::forget(PageHelper::cacheKey('children', $parent->id));
                Cache::forget(PageHelper::cacheKey('topmostAncestor', $parent->id));
            }
            $allLocations = PagePlusMeta::where('key', 'location')->pluck('value')->unique();
            foreach ($allLocations as $location) {
                Cache::forget(PageHelper::cacheKey('byLocation', $location));
            }
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    public function metas(): HasMany
    {
        return $this->hasMany(PagePlusMeta::class, 'page_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PagePlusTranslation::class, 'page_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PagePlus::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PagePlus::class, 'parent_id');
    }

    public function childrenCached()
    {
        return Cache::rememberForever(PageHelper::cacheKey('children', $this->id), function () {
            return $this->children()->orderBy('order')->get();
        });
    }

    public function getFullSlug(): string
    {
        return Cache::rememberForever(PageHelper::cacheKey('fullSlug', $this->id), function () {
            $slugs = [];
            $page = $this;
            while ($page) {
                array_unshift($slugs, $page->slug);
                $page = $page->parent;
            }
            return implode('/', $slugs);
        });
    }

    public function getMeta($key, $default = null): ?string
    {
        $cacheKey = PageHelper::cacheKey('meta', $this->id) . '_' . $key;
        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $meta = $this->metas()->where('key', $key)->first();
            return $meta ? $meta->value : $default;
        });
    }

    public function getTranslation($locale = null)
    {
        $locale = $locale ?? (auth()->check() ? auth()->user()->language : app()->getLocale());
        $cacheKey = PageHelper::cacheKey('translation', $this->id) . '_' . $locale;
        return Cache::rememberForever($cacheKey, function () use ($locale) {
            $translation = $this->translations()->where('locale', $locale)->first();
            return $translation ?: $this->translations()->orderBy('created_at', 'asc')->first();
        });
    }

    public function getAllChildrenIds(): array
    {
        return Cache::rememberForever(PageHelper::cacheKey('childrenIds', $this->id), function () {
            $ids = [];
            foreach ($this->children as $child) {
                $ids[] = $child->id;
                $ids = array_merge($ids, $child->getAllChildrenIds());
            }
            return $ids;
        });
    }

    public function availableParents()
    {
        $excludedIds = $this->getAllChildrenIds();
        $excludedIds[] = $this->id;
        return PagePlus::whereNotIn('id', $excludedIds)->get();
    }

    public function getTopmostAncestor()
    {
        return Cache::rememberForever(PageHelper::cacheKey('topmostAncestor', $this->id), function () {
            $ancestor = $this;
            while ($ancestor->parent()->exists()) {
                $ancestor = $ancestor->parent()->first();
            }
            return $ancestor;
        });
    }


    public static function getBySlug($slug)
    {
        return Cache::rememberForever(PageHelper::cacheKey('bySlug', $slug), function () use ($slug) {
            $slugs = explode('/', trim($slug, '/'));
            $page = self::where('slug', array_shift($slugs))->firstOrFail();
            foreach ($slugs as $slug) {
                $page = $page->children()->where('slug', $slug)->firstOrFail();
            }
            return $page;
        });
    }

}

