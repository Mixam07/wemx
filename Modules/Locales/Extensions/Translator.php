<?php
namespace Modules\Locales\Extensions;

use Illuminate\Translation\Translator as BaseTranslator;
use Modules\Locales\Jobs\UpdateTranslationFileJob;
use Illuminate\Contracts\Translation\Loader;

class Translator extends BaseTranslator
{
    public function __construct(Loader $loader, $locale)
    {
        parent::__construct($loader, $locale);
    }
    public function get($key, array $replace = [], $locale = null, $fallback = true): array|string|null
    {
        $job = new UpdateTranslationFileJob($key, $locale, $replace);
        dispatch($job);
        if (array_key_exists('default', $replace)){
            unset($replace['default']);
        }
        return parent::get($key, $replace, $locale, $fallback);
    }
}