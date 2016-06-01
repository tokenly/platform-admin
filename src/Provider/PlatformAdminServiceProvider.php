<?php

namespace Tokenly\PlatformAdmin\Provider;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PlatformAdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            require $this->moduleDir('routes/platformAdmin-routes.php');
        }

        // register views like platformAdmin::home
        $this->loadViewsFrom($this->moduleDir('public/views'), 'platformAdmin');

        // register vendor resources
        $this->publishes([
            $this->moduleDir('public/resources') => public_path('vendor/platformAdmin'),
        ], 'public');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('platformAdmin.platformAdminAuth', function($app) {
            return $app->make('Tokenly\PlatformAdmin\Middleware\AuthenticatePlatformAdmin');
        });
    }

    // ------------------------------------------------------------------------
    
    protected function moduleDir($ext=null) {
        if (!isset($this->base_dir)) { $this->base_dir = realpath(__DIR__.'/../..'); }
        return $this->base_dir.($ext === null ? '' : '/'.$ext);
    }
}
