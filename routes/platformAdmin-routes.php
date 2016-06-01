<?php

// , 
Route::group([
    'prefix'     => 'platform/admin',
    'middleware' => 'platformAdmin.platformAdminAuth',
], function () {

    Route::get('/', ['as' => 'platform.admin.home', function () { return view('platformAdmin::home'); }]);
    Route::resource('user', '\Tokenly\PlatformAdmin\Controllers\UsersController', []);

});

