<?php

namespace Tokenly\PlatformAdmin\Provider;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class PlatformAdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // routs
        if (!$this->app->routesAreCached()) {
            require $this->moduleDir('routes/platformAdmin-routes.php');
        }

        // register views int the form of platformAdmin::home
        $this->loadViewsFrom($this->moduleDir('public/views'), 'platformAdmin');

        // register vendor resources
        $this->publishes([
            $this->moduleDir('public/resources') => public_path('vendor/platformAdmin'),
        ], 'public');

        $this->publishes([
            $this->moduleDir('migrations') => database_path('migrations'),
        ], 'migrations');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/platformadmin.php';
        $this->mergeConfigFrom($configPath, 'platformadmin');

        $this->app->bind('platformAdmin.platformAdminAuth', function($app) {
            return $app->make('Tokenly\PlatformAdmin\Middleware\AuthenticatePlatformAdmin');
        });
        $this->app->bind('platformAdmin.controlAuth', function($app) {
            return $app->make('Tokenly\PlatformAdmin\Middleware\AuthenticatePlatformControl');
        });


        $this->commands([
            \Tokenly\PlatformAdmin\Console\CreatePlatformAdmin::class,
            \Tokenly\PlatformAdmin\Console\MigrateWithLock::class,
            \Tokenly\PlatformAdmin\Console\SetServiceHealthStatus::class,
            \Tokenly\PlatformAdmin\Console\ServiceCheck::class,
        ]);

    }

    // ------------------------------------------------------------------------
    
    protected function moduleDir($ext=null) {
        if (!isset($this->base_dir)) { $this->base_dir = realpath(__DIR__.'/../..'); }
        return $this->base_dir.($ext === null ? '' : '/'.$ext);
    }
}
