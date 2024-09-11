<?php

namespace Modules\PagePlus\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\PagePlus\Http\Controllers';

    public function map(): void
    {
        $this->mapAdminRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('PagePlus', '/Routes/web.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware(['web'])
            ->namespace($this->moduleNamespace)
            ->group(module_path('PagePlus', '/Routes/admin.php'));
    }
}
