<?php

namespace Tokenly\PlatformAdmin\Router;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Router
{

    public function routeFromConfig($routes) {
        if (!$routes) { return; }
        $dev_mode_enabled = env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED', false);

        foreach($routes as $route) {
            // ignore developmentMode routes
            if (!$dev_mode_enabled AND isset($route['developmentMode']) AND $route['developmentMode']) {
                continue;
            }

            if ($route['type'] == 'resource') {
                $name = $this->trimRouteName($route['name']);
                $controller = $route['controller'];
                $options = isset($route['options']) ? $route['options'] : [];
                Route::group(['as' => 'platform.admin.'], function() use ($name, $controller, $options) {
                    Route::resource($name, $controller, $options);
                });
            }

        }
    }


    protected function trimRouteName($full_name) {
        if (substr($full_name, 0, 15) == 'platform.admin.') {
            return substr($full_name, 15);
        }

        return $full_name;
    }
}
