<?php

namespace App\Console\Commands\Modules;

use Nwidart\Modules\Facades\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Facades\Service;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:makes {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service';

    /**
     * The directory where this service should be installed in
     *
     * @var string
     */
    protected $path = 'app/Services';

    /**
     * Temporary storage path
     *
     * @var string
     */
    protected $storage_path = 'storage/';

    /**
     * Github stub file branch
     *
     * @var string
     */
    protected $branch = 'https://github.com/WemXPro/service-example/archive/refs/heads/main.zip';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(!is_writable(base_path($this->storage_path))) {
            $this->components->error(base_path($this->storage_path). " is not writable");
            return;
        }

        $name = Str::studly($this->argument('name'));
        $name_lower = Str::lower($name);

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            $this->components->error("$name contains invalid characters");
            return;
        }

        if(Module::find($name_lower) || file_exists(base_path("{$this->path}/$name"))) {
            $this->components->error("$name already exists");
            return;
        }

        shell_exec("curl -s -L -o {$this->storage_path}/$name.zip {$this->branch}");
        shell_exec("unzip -o {$this->storage_path}/$name.zip -d {$this->storage_path}/$name");
        
        // replace Example in stub files
        $this->replaceInFiles(base_path("{$this->storage_path}/$name/service-example-main"), 'Example', $name);
        $this->replaceInFiles(base_path("{$this->storage_path}/$name/service-example-main"), 'example', $name_lower);

        // rename the service provider file
        $path_to_service = base_path("{$this->storage_path}/$name/service-example-main");
        shell_exec("mv $path_to_service/Providers/ExampleServiceProvider.php $path_to_service/Providers/{$name}ServiceProvider.php");

        shell_exec("mv $path_to_service {$this->path}/$name");

        $this->deleteDirectory("{$this->storage_path}/$name");

        shell_exec("rm {$this->storage_path}/$name.zip");

        $this->components->info("Created {$name}");
    }

    protected function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
    
        if (!is_dir($dir)) {
            return unlink($dir);
        }
    
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
    
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
    
        return rmdir($dir);
    }

    protected function replaceInFiles($directory, $search, $replace)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $this->replaceInFile($file->getRealPath(), $search, $replace);
        }
    }

    protected function replaceInFile($filePath, $search, $replace)
    {
        $contents = file_get_contents($filePath);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($filePath, $contents);
    }
}
