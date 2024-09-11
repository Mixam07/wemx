<?php

namespace Modules\Locales\Models;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Manager
{
    public ?ISO639 $iso639 = null;
    private ?Filesystem $filesystem = null;
    private ?array $languages = null;
    private array $langs = [];
    private Collection $countries;

    public function __construct()
    {
        $this->iso639 = $this->getIsoInstance();
        $this->filesystem = $this->getFilesystemInstance();
        $this->languages = $this->filesystem->directories(resource_path('lang'));
        $this->countries = collect(require module_path('Locales', 'Config/languages.php'));

        foreach ($this->languages as $key => $path) {
            $code = basename($path);
            $this->langs[$code] = $this->getCountryName($code);
        }
    }

    public function getInstalled(): array
    {
        return $this->langs;
    }

    public function getAvailable(): array
    {
        foreach ($this->iso639->allLanguages() as $key => $value) {
            $codes[$value[0]] = $value[4];
        }
        return $codes;
    }

    public function getCountryCode($lang_key): string
    {
        $parts = explode('_', $lang_key);
        if (count($parts) > 1) {
            $lang_key = $parts[1];
        }
        if ($this->countries->keyBy('iso-639-1')->get($lang_key)){
            return array_key_first($this->countries->keyBy('iso-639-1')->get($lang_key)['countries']) ?? $lang_key;
        } else {
            return $lang_key;
        }
    }

    public function getCountryName($lang): string
    {
        $parts = explode('_', $lang);
        if (count($parts) > 1) {
            try {
                $lang = $this->countries->keyBy('iso-639-1')->get($parts[0])['countries'][$parts[1]];
            } catch (\Exception $e) {
                $lang = $this->countries->keyBy('iso-639-1')->get($parts[0])['name'];
            }
        } else {
            $lang = $this->countries->keyBy('iso-639-1')->get($lang)['name'] ?? 'Unknown';
        }
        return ucfirst($lang);
    }

    private
    function getIsoInstance(): ISO639
    {
        return app()->make(ISO639::class);
    }

    private
    function getFilesystemInstance(): Filesystem
    {
        return app()->make(Filesystem::class);
    }
}
