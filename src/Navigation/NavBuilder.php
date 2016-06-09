<?php

namespace Tokenly\PlatformAdmin\Navigation;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class NavBuilder
{

    public static function buildNav() {
        $nb = new NavBuilder();
        return $nb->buildNavigationEntries();
    }

    public function buildNavigationEntries() {
        $entries = collect($this->defaultNavigationEntries());

        // merge config
        $config_nav_entries = Config::get('platformadmin.navigation');
        if ($config_nav_entries) { $entries = $entries->merge($config_nav_entries); }

        // normalize
        $entries = $entries->map(function($entry) {
            return $this->normalizeEntry($entry);
        });

        $dev_mode_enabled = env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED', false);
        if (!$dev_mode_enabled) {
            $entries = $entries->reject(function($entry) {
                return $entry['developmentMode'];
            });
        }

        return $entries;
    }


    public function defaultNavigationEntries() {
        //    <a class="brand" href="{{ route('platform.admin.home') }}">Tokenly Platform Administration</a><!-- 
        // --><a class="nav{{ $buildActiveClass('home') }}" href="{{ route('platform.admin.home') }}">Home</a><!-- 
        // --><a class="nav{{ $buildActiveClass('user.index') }}" href="{{ route('platform.admin.user.index') }}">Users</a><!-- 
        // --><?php if (env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED')) {

        return [
            [
                'class' => 'brand',
                'route' => 'home',
                'label' => 'Tokenly Platform Administration',
            ],
            [
                'route'        => 'user.index',
                'activePrefix' => 'user',
                'label'        => 'Users',
            ],
            [
                'developmentMode' => true,
                'route'           => 'xchain',
                'label'           => 'XChain',
            ],
        ];

    }


    protected function normalizeEntry($entry) {
        $class = isset($entry['class']) ? $entry['class'] : 'nav';
        if ($this->isActive(isset($entry['activePrefix']) ? $entry['activePrefix'] : $entry['route'])) {
            $class = trim($class.' active');
        }
        $entry['class'] = $class;
        $entry['route'] = $this->prefixRouteName($entry['route']);
        $entry['developmentMode'] = isset($entry['developmentMode']) ? $entry['developmentMode'] : false;
        return $entry;
    }

    protected function isActive($route_name) {
        $l = strlen('platform.admin.'.$route_name);
        return ('platform.admin.'.$route_name) == substr(Route::current()->getName(), 0, $l);
    }

    protected function prefixRouteName($name) {
        if (substr($name, 0, 15) !== 'platform.admin.') {
            return 'platform.admin.'.$name;
        }
        return $name;
    }
}
