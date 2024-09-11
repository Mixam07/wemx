<?php

namespace Modules\Artisan\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\Artisan\Http\Controllers';

    public function map(): void
    {
        $this->mapAdminRoutes();
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware(['web', 'admin', 'auth', 'permission'])
            ->namespace($this->moduleNamespace)
            ->group(module_path('Artisan', '/Routes/admin.php'));
    }
}
