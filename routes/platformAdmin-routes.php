<?php

use Illuminate\Support\Facades\Config;
use Tokenly\PlatformAdmin\Router\Router;

// the entire platform admin must enabled
if (env('PLATFORM_ADMIN_ENABLED', true)) {
    Route::group([
        'prefix'     => 'platform/admin',
        'middleware' => ['web', 'platformAdmin.platformAdminAuth'],
    ], function () {

        Route::get('/', ['as' => 'platform.admin.home', function () { return view('platformAdmin::home'); }]);

        Route::group(['as' => 'platform.admin.'], function() {
            Route::resource('user', 'Tokenly\PlatformAdmin\Controllers\UsersController', []);
        });

        if (env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED')) {
            Route::get('/xchain', ['as' => 'platform.admin.xchain', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainController@index']);

            Route::get('/xchain/settings', ['as' => 'platform.admin.xchain.settings', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainSettingsController@edit']);
            Route::post('/xchain/settings', ['as' => 'platform.admin.xchain.settings', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainSettingsController@update']);

            Route::get('/xchain/balances', ['as' => 'platform.admin.xchain.balances.index', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@index']);
            Route::get('/xchain/balances/{id}/edit', ['as' => 'platform.admin.xchain.balances.edit', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@edit']);
            Route::post('/xchain/balances/{id}', ['as' => 'platform.admin.xchain.balances.update', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@update']);
            Route::delete('/xchain/balances/{id}', ['as' => 'platform.admin.xchain.balances.destroy', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@destroy']);
        }

        // artisan command
        Route::get('/artisan/command', ['as' => 'platform.admin.artisan.command', 'uses' => 'Tokenly\PlatformAdmin\Controllers\ArtisanCommandController@showForm']);
        Route::post('/artisan/command', ['as' => 'platform.admin.artisan.command', 'uses' => 'Tokenly\PlatformAdmin\Controllers\ArtisanCommandController@runCommand']);


        // configured routes
        $router = new Router();
        $router->routeFromConfig(Config::get('platformadmin.routes'));

    });
}



// the entire platform admin must enabled
if (env('PLATFORM_CONTROL_ENABLED', true)) {
    Route::group([
        'prefix'     => 'platform/control',
        'middleware' => ['web', 'platformAdmin.controlAuth'],
    ], function () {

        Route::get('/version', ['as' => 'platform.control.version', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PlatformControlController@version']);

    });
}

// the entire platform admin must enabled
if (env('PLATFORM_HEALTH_CHECK_ENABLED', true)) {
    Route::get('/_health', ['as' => 'platform.control.healthcheck', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PlatformHealthCheckController@healthcheck']);
}
