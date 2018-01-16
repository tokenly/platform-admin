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

        if (env('PLATFORM_CONTROL_PROMOTE_ADMIN_ENABLED', false)) {
            Route::get('/promote-platform-admin', ['as' => 'platform.control.promoteadmin', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PlatformControlController@promotePlatformAdmin']);
            Route::get('/demote-platform-admin', ['as' => 'platform.control.demoteadmin', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PlatformControlController@demotePlatformAdmin']);
        }
    });
}

// the entire platform admin must enabled
if (env('PLATFORM_HEALTH_CHECK_ENABLED', true)) {
    Route::get('/_health', ['as' => 'platform.control.healthcheck', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PlatformHealthCheckController@healthcheck']);
}
