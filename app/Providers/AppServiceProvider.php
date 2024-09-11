<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use App\Facades\AdminTheme;
use App\Facades\Theme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // force https if FORCE_HTTPS is set to true
        if(config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        // define @settings('key') directive
        Blade::directive('settings', function ($key, $default = null) {
            return "<?php echo App\\Models\\Settings::get({$key}, {$default}); ?>";
        });

        // create @admin directive
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->is_admin();
        });

        // define theme components
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('queue:start')->cron("* * * * *");
            $schedule->command('queue:start --force')->cron('0 0 * * *');
            Theme::registerComponents();
            AdminTheme::registerComponents();
        });
    }


}
