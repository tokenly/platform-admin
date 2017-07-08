<?php

namespace Tokenly\PlatformAdmin\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AuthenticatePlatformControl {

    public function __construct() {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authenticated = false;

        $username = $request->getUser();
        $password = $request->getPassword();
        $expected_username = Config::get('platformadmin.control.authBasicUsername');
        $expected_password = Config::get('platformadmin.control.authBasicPassword');
        if (!strlen($expected_password)) { $expected_password = null; }

        $authenticated = false;

        if (
            $expected_password !== null
            AND $username === $expected_username
            AND $password === $expected_password
        ) {
            $authenticated = true;
        }

        if (!$authenticated) {
            return response('Invalid credentials.', 401, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }

}
