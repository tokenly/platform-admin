<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelApiProvider\Contracts\APIPermissionedUserContract;
use Tokenly\LaravelApiProvider\Contracts\APIUserRepositoryContract;
use Tokenly\PlatformAdmin\Controllers\Controller;

class PlatformControlController extends Controller
{

    public function version() {
        $response_code = null;
        $error = null;
        $response_data = [];

        $github_version_filepath = Config::get('platformadmin.control.versionFilepath');
        Log::debug("\$github_version_filepath=".json_encode($github_version_filepath, 192));
        try {
            if (file_exists($github_version_filepath)) {
                $github_version_details = json_decode(file_get_contents($github_version_filepath), true);
                $commit = $github_version_details['object']['sha'];

                $response_code = 200;
                $response_data = [
                    'success' => true,
                    'commit'  => $commit,
                ];

            } else {
                $response_code = 404;
                $error = 'not found';
            }
            
        } catch (Exception $e) {
            $response_code = 500;
            $error = $e->getMessage();
        }


        if (!$response_data AND $error) {
            $response_data = [
                'success' => false,
                'message'  => $error,
            ];
        }

        return response()->json($response_data, $response_code);
    }

    public function promotePlatformAdmin(Request $request, APIUserRepositoryContract $user_repository) {
        $email = $request->get('email');
        if (!$email) { return response('Missing required email parameter', 400); }

        // get the user
        $user = $user_repository->findByEmail($email);
        if (!$user) { return response('User Not Found', 404); }

        if (!($user instanceof APIPermissionedUserContract)) {
            return response('User can not be permissioned', 400);
        }

        if ($user->hasPermission('platformAdmin')) {
            return response('User already has platformAdmin.  User was unchanged.', 200);
        }

        $privileges = $user['privileges'];
        if (!$privileges) {
            $privileges = [];
        }
        $privileges['platformAdmin'] = true;
        $user_repository->update($user, ['privileges' => $privileges]);

        return response('User was promoted with the platformAdmin privilege.', 200);
    }

    public function demotePlatformAdmin(Request $request, APIUserRepositoryContract $user_repository) {
        $email = $request->get('email');
        if (!$email) { return response('Missing required email parameter', 400); }

        // get the user
        $user = $user_repository->findByEmail($email);
        if (!$user) { return response('User Not Found', 404); }

        if (!($user instanceof APIPermissionedUserContract)) {
            return response('User can not be permissioned', 400);
        }

        if (!$user->hasPermission('platformAdmin')) {
            return response('User already is not a platformAdmin.  User was unchanged.', 200);
        }

        $privileges = $user['privileges'];
        if (!$privileges) {
            $privileges = [];
        }
        unset($privileges['platformAdmin']);
        $user_repository->update($user, ['privileges' => $privileges]);

        return response('Removed the platformAdmin privilege from this user.', 200);
    }

}
