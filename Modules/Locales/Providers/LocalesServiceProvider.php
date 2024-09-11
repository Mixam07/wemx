<?php

namespace Modules\Locales\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Locales\Extensions\Translator;
use Modules\Locales\Http\Middleware\LocalizationMiddleware;

class LocalesServiceProvider extends ServiceProvider
{

    protected string $moduleName = 'Locales';
    protected string $moduleNameLower = 'locales';

    public function boot(): void
    {

        $this->registerViews();
        $this->registerMiddleware($this->app['router']);
        $this->languageLoad();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

//        if (env('LOCALES_GENERATOR', false)) {
//            $this->app->bind('translator', function ($app) {
//                $loader = new \Illuminate\Translation\FileLoader($app['files'], $app['path.lang']);
//                $loader->addNamespace('lang', $app['path.lang']);
//                $trans = new Translator($loader, $app['config']['app.locale']);
//                $trans->setFallback($app['config']['app.fallback_locale']);
//                return $trans;
//            });
//        }
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

    public function registerMiddleware($router): void
    {
        $router->pushMiddlewareToGroup('web', LocalizationMiddleware::class);
    }

    public function provides(): array
    {
        return [];
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

    private function languageLoad(): void
    {
        $langPath = resource_path('lang/' . $this->moduleNameLower);
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', $this->moduleNameLower);
        }
    }
}
