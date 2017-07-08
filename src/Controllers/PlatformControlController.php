<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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

}
