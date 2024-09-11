<?php

namespace Modules\Artisan\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Modules\Artisan\Console\Commands\EnvEditorCommand;

class ArtisanServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Artisan';
    protected string $moduleNameLower = 'artisan';

    protected array $commands = [
        EnvEditorCommand::class
    ];

    public function boot(): void
    {
        if (\Cache::get('admin_debug', false) and str_contains(request()->path(), 'admin')) {
            Config::set('app.debug', true);
        }
        $this->registerConfig();
        $this->registerViews();
        $this->commands($this->commands);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
