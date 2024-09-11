<?php

namespace Modules\PagePlus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class PagePlusTranslation extends Model
{
    protected $fillable = ['page_id', 'locale', 'title', 'content'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PagePlus::class, 'page_id');
    }

    protected static function booted(): void
    {
        static::saved(function ($translation) {
            Cache::forget(PageHelper::cacheKey('translation', $translation->page_id) . '_' . $translation->locale);
        });

        static::deleted(function ($translation) {
            Cache::forget(PageHelper::cacheKey('translation', $translation->page_id) . '_' . $translation->locale);
        });
    }
}

