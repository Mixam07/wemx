<?php

namespace Modules\Locales\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class UpdateTranslationFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $key;
    protected ?string $locale;
    protected array $replace;

    /**
     * Create a new job instance.
     *
     * @param string $key
     * @param string|null $locale
     * @param array $replace
     * @return void
     */
    public function __construct(string $key, string $locale = null, array $replace = [])
    {
        $this->key = $key;
        $this->locale = $locale;
        $this->replace = $replace;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $key = $this->key;
        $locale = $this->locale;

        if (str_contains($key, " ")) {
            return;
        } else if (str_contains($key, "::")) {
            $keyData = $this->processModuleKey($key);
        } else if (str_contains($key, ".")) {
            $keyData = $this->processStandardKey($key);
        } else {
            return;
        }

        [$filename, $itemKey] = explode('.', $keyData['key'], 2);

        $directories = File::directories($keyData['localisationPath']);

        foreach ($directories as $directory) {
            $file = $directory . '/' . $filename . '.php';

            if (!File::exists($file)) {
                if (!File::isWritable($directory)) {
                    continue;
                }
                $fileData = [];
            } else if (File::isWritable($file)) {
                $fileData = require($file);
            } else {
                continue;
            }

            $newItem = $this->createNewItem($itemKey);

            $exists = $this->checkKeyExists($fileData, $itemKey);


            if (!$exists) {
                $fileData = array_merge_recursive($fileData, $newItem);
                $output = '<?php return ' . var_export($fileData, true) . ';';
                File::put($file, $output);
            }
        }
    }

    private function createNewItem($itemKey)
    {
        $newItem = [];
        $parts = explode('.', $itemKey);
        $item[$itemKey] = ucfirst(end($parts));
        array_walk_recursive($item, function ($value, $key) use (&$newItem) {
            $parts = explode('.', $key);
            $current = &$newItem;
            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
            if (array_key_exists('default', $this->replace)) {
                $value = $this->replace['default'];
            }
            $current = $value;
        });
        return $newItem;
    }

    private function checkKeyExists($fileData, $itemKey): bool
    {
        $keyParts = explode('.', $itemKey);
        $exists = true;
        $temp = $fileData;
        foreach ($keyParts as $part) {
            if (!isset($temp[$part])) {
                $exists = false;
                break;
            }
            $temp = $temp[$part];
        }
        return $exists;
    }

    private function processModuleKey($key): array
    {
        $keyParts = explode('::', $key);
        $localisationPath = Module::getModulePath($keyParts[0]) . 'Resources/lang';
        $key = $keyParts[1];
        return compact('localisationPath', 'key');
    }

    private function processStandardKey($key): array
    {
        $localisationPath = resource_path('lang');
        return compact('localisationPath', 'key');
    }
}
