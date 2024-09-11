<?php

namespace Modules\Locales\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Modules\Locales\Models\ISO639;

class LangCommand extends Command
{

    protected $signature = 'locales:lang {--action= : import/generate} {--locale= : en}';
    protected $description = 'Manage localization of the application';

    private const SEP = DIRECTORY_SEPARATOR;
    private $locale, $fallback_locale, $lang_path, $iosLangs;
    private $args = [];

    private const ACTIONS = [
        // 'import' => 'Import localization (Imports additional Billing translations into all existing localizations, if any)',
        'generate' => 'Generate new localization (This command will generate files of the selected localization of Billing and Pterodactyl)',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->locale = config('app.locale') ? config('app.locale') : 'en';
        $this->fallback_locale = config('app.fallback_locale') ? config('app.fallback_locale') : 'en';
        $this->lang_path = resource_path('lang');
        $this->createDir([
            $this->lang_path . self::SEP . $this->locale,
            $this->lang_path . self::SEP . $this->fallback_locale,
        ]);
    }

    public function handle()
    {
        $this->args['ACTIONS'] = $this->option('action') ?? $this->choice(
            'Choose an action',
            self::ACTIONS
        );

        switch ($this->args['ACTIONS']) {
            case 'generate':
                $this->generate();
                break;
        }
    }

    private function generate()
    {
        $code = $this->option('locale') ?? $this->choice(
            'Choose an locale',
            $this->languages()
        );

        $file = $this->lang_path . self::SEP . $code . self::SEP . 'auth.php';
        if (!file_exists($file)) {
            $this->copyDirectory($this->lang_path . self::SEP . 'en', $this->lang_path . self::SEP . $code);
            $this->info("The localization {$this->languages()[$code]} was generated with a standard translation");
        } else {
            $this->warn("Canceled {$this->languages()[$code]}!!! Localization already exists");
        }
    }

    private function getExistLocalization()
    {
        $langs_dirs = File::directories($this->lang_path);
        $langs = [];
        foreach ($langs_dirs as $value) {
            $langs[basename($value)] = $value;
        }
        return $langs;
    }

    private function createDir($path)
    {
        if (is_array($path)) {
            foreach ($path as $dir) {
                if (!file_exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
            }
            return;
        }
        if (!file_exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function copyDirectory($source_path, $destination_path)
    {
        if (!File::isDirectory($destination_path)) {
            File::makeDirectory($destination_path, 0755, true);
        }
        File::copyDirectory($source_path, $destination_path);
    }

    private function languages()
    {
        $this->iosLangs = new ISO639;
        $codes = [];
        foreach ($this->iosLangs->allLanguages() as $key => $value) {
            $codes[$value[0]] = $value[4];
        }
        return $codes;
    }
}
