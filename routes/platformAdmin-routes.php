<?php

use Illuminate\Support\Facades\Config;
use Tokenly\PlatformAdmin\Router\Router;

// the entire platform admin must enabled
if (!env('PLATFORM_ADMIN_ENABLED', true)) { return; }

// , 
Route::group([
    'prefix'     => 'platform/admin',
    'middleware' => 'platformAdmin.platformAdminAuth',
], function () {

    Route::get('/', ['as' => 'platform.admin.home', function () { return view('platformAdmin::home'); }]);
    Route::resource('user', 'Tokenly\PlatformAdmin\Controllers\UsersController', []);

    if (env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED')) {
        Route::get('/xchain', ['as' => 'platform.admin.xchain', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainController@index']);

        Route::get('/xchain/settings', ['as' => 'platform.admin.xchain.settings', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainSettingsController@edit']);
        Route::post('/xchain/settings', ['as' => 'platform.admin.xchain.settings', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainSettingsController@update']);

        Route::get('/xchain/balances', ['as' => 'platform.admin.xchain.balances.index', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@index']);
        Route::get('/xchain/balances/{id}/edit', ['as' => 'platform.admin.xchain.balances.edit', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@edit']);
        Route::post('/xchain/balances/{id}', ['as' => 'platform.admin.xchain.balances.update', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@update']);
        Route::delete('/xchain/balances/{id}', ['as' => 'platform.admin.xchain.balances.destroy', 'uses' => 'Tokenly\PlatformAdmin\Controllers\XChainBalancesController@destroy']);
    }

    // configured routes
    $router = new Router();
    $router->routeFromConfig(Config::get('platformadmin.routes'));

});

