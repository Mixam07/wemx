<?php

namespace Modules\PagePlus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class PagePlusMeta extends Model
{
    protected $fillable = ['page_id', 'key', 'value'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PagePlus::class, 'page_id');
    }

    protected static function booted(): void
    {
        static::saved(function ($meta) {
            Cache::forget(PageHelper::cacheKey('meta', $meta->page_id) . '_' . $meta->key);
        });

        static::deleted(function ($meta) {
            Cache::forget(PageHelper::cacheKey('meta', $meta->page_id) . '_' . $meta->key);
        });
    }
}

