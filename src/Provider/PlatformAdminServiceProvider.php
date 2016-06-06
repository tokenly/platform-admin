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
        $this->app->bind('platformAdmin.platformAdminAuth', function($app) {
            return $app->make('Tokenly\PlatformAdmin\Middleware\AuthenticatePlatformAdmin');
        });

        if (PlatformAdminMeta::get('xchainMockActive')) {
            $this->initXChainMock();

            $this->app->bind(\Tokenly\XChainClient\Client::class, function($app) {
                Log::debug("binding xchain client");
                return $this->xchain_client_mock;
            });
        }
    }

    // ------------------------------------------------------------------------
    
    protected function moduleDir($ext=null) {
        if (!isset($this->base_dir)) { $this->base_dir = realpath(__DIR__.'/../..'); }
        return $this->base_dir.($ext === null ? '' : '/'.$ext);
    }

    protected function initXChainMock() {
        if (!isset($this->xchain_client_mock)) {
            // build the mock
            $builder = app('Tokenly\XChainClient\Mock\MockBuilder');
            list($xchain_client_mock, $xchain_recorder) = $builder->buildXChainMockAndRecorder();
            $this->xchain_client_mock = $xchain_client_mock;

            // bind the events
            $xchain_hooks_manager = app('Tokenly\PlatformAdmin\XChainHooks\XChainHooksManager')->init($builder);
        }
        return $this->xchain_client_mock;
    }
}
