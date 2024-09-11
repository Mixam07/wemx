<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class PageTranslation extends Model
{
    protected $fillable = [
        'page_id',
        'name',
        'locale',
        'title',
        'content',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('active_pages');
        });

        static::deleted(function () {
            Cache::forget('active_pages');
        });
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
