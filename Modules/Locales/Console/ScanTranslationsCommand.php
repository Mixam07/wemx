<?php

namespace Modules\Locales\Console;

use FilesystemIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ScanTranslationsCommand extends Command
{
    protected $signature = 'translations:scan {directory : Path to the folder}';

    protected $description = 'Scanning files and extracting trans(), __(), @lang() function keys and values';

    public function handle(): void
    {
        $directory = $this->argument('directory');
        $translations = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $defaultPattern = '/(?<!\w)(trans|__|@lang)\([\'"]([^\'"]*)[\'"],\s*\[[^\]]*\'default\'\s*=>\s*([\'"][^\'"]*[\'"])\]/';
        $allKeysPattern = '/(?<!\w)(trans|__|@lang)\([\'"]([^\'"]*)[\'"](,\s*\[[^]]+\])?\)/';


        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());

                $matches = [];
                preg_match_all($defaultPattern, $content, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $key = $match[2];
                    $value = $match[3];

                    $translations[$key] = $value;
                }

                $matches = [];
                preg_match_all($allKeysPattern, $content, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $key = $match[2];
                    if (!isset($translations[$key])) {
                        $translations[$key] = $key;
                    }
                }
            }
        }

        $this->warn('Found keys and values of functions trans(), __(), @lang():');
        $this->info("");
        $locFile = '';
        $oldModule = '';
        $oldLocFile = '';

        foreach ($translations as $key => $value) {

            if (str_contains($key, "::")) {
                [$module, $key] = explode('::', $key, 2);
                [$locFile, $key] = explode('.', $key, 2);
                if ($module != $oldModule){
                    $oldModule = $module;
                    $this->warn("Localizations module: $module");
                }
            } elseif (str_contains($key, ".")) {
                [$locFile, $key] = explode('.', $key, 2);
            }

            if ($locFile != $oldLocFile){
                $oldLocFile = $locFile;
                $this->warn("Localizations path: $locFile.php");
            }
            $this->info("'$key' => $value,");
        }
    }
}
