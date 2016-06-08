<?php

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


        // token promises
        Route::get('/promises',           ['as' => 'platform.admin.promise.index',   'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@index']);
        Route::get('/promises/{id}/edit', ['as' => 'platform.admin.promise.edit',    'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@edit']);
        Route::get('/promises/new',       ['as' => 'platform.admin.promise.create',  'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@create']);
        Route::post('/promises/new',      ['as' => 'platform.admin.promise.store',   'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@store']);
        Route::patch('/promises/{id}',    ['as' => 'platform.admin.promise.update',  'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@update']);
        Route::delete('/promises/{id}',   ['as' => 'platform.admin.promise.destroy', 'uses' => 'Tokenly\PlatformAdmin\Controllers\PromisesController@destroy']);

    }

});

