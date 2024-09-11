<?php

namespace App\Services\Pterodactyl\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'App\Services\Pterodactyl\Http\Controllers';

    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('pterodactyl', 'Routes/web.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', 'admin'])
            ->namespace($this->moduleNamespace)
            ->prefix('admin/pterodactyl')
            ->group(module_path('pterodactyl', '/Routes/admin.php'));
    }
}
