<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

/**
 * @property mixed $redirect_url
 * @property mixed $new_tab
 * @property mixed $content
 * @property mixed $placement
 * @property mixed $icon
 * @property mixed $path
 * @property mixed $title
 * @property mixed $name
 * @property mixed $basic_page
 * @property mixed $is_enabled
 * @property mixed $id
 * @method static wherePath($page)
 */
class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'title',
        'content',
        'is_enabled',
        'placement',
        'redirect_url',
    ];

    protected $casts = [
        'placement' => 'array',
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

    public function translate($locale = null): void
    {
        $locale = $locale ?: App::getLocale();
        $translation = $this->translations()->where('locale', $locale)->first();
        if ($translation) {
            $this->name = $translation->name;
            $this->title = $translation->title;
            $this->content = $translation->content;
        }
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public static function getActive()
    {
        $cacheKey = 'active_pages';
        return Cache::remember($cacheKey, 3600, function () {
            return Page::whereIsEnabled(1)->get()->each(function ($page) {
                $page->translate();
            });
        });
    }
}
