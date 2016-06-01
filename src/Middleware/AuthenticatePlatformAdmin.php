<?php

namespace Tokenly\PlatformAdmin\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tokenly\HmacAuth\Exception\AuthorizationException;
use Tokenly\LaravelApiProvider\Contracts\APIUserRepositoryContract;
use Tokenly\LaravelEventLog\Facade\EventLog;

class AuthenticatePlatformAdmin {

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    public function __construct(Guard $auth, APIUserRepositoryContract $user_repository) {
        $this->auth            = $auth;
        $this->user_repository = $user_repository;
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

        try {
            $user = $this->auth->user();
            if (!$user) {
                // redirect
                return redirect('/');
            }

            $authenticated = $user->hasPermission('platformAdmin');

        } catch (Exception $e) {
            // something else went wrong
            EventLog::logError('error.platformAuth.unexpected', $e);
            $error_message = 'An unexpected error occurred';
            $error_code = 500;
        }

        if (!$authenticated) {
            $error_code = (isset($error_code) ? $error_code :  403);
            $error_message = (isset($error_message) ? $error_message : "You do not have privileges to perform this operation");
            EventLog::logError('error.platformAuth.unauthenticated', ['remoteIp' => $request->getClientIp()]);

            return new Response($error_message, $error_code);

            $response = new JsonResponse([
                'message' => $error_message,
                'errors' => [$error_message],
            ], $error_code);
            return $response;
        }

        return $next($request);
    }

}
