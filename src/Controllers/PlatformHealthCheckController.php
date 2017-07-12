<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tokenly\PlatformAdmin\Controllers\Controller;

class PlatformHealthCheckController extends Controller
{

    public function healthcheck() {
        $all_checks_pass = true;
        $errors = [];

        // ------------------------------
        // check database
        
        $should_check_db = Config::get('platformadmin.healthcheck.checkDatabase');
        Log::debug("\$should_check_db=".json_encode($should_check_db, 192));
        try {
            if ($should_check_db) {
                Log::debug("DB::connection()->getPdo()=".json_encode(DB::connection()->getPdo(), 192));
                if (!DB::connection()->getPdo()) {
                    $errors[] = "failed to connect to database";
                    $all_checks_pass = false;
                }
            }
        } catch (Exception $e) {
            Log::warning("Failed to connect to database. ".$e->getMessage());
            $errors[] = "failed to connect to database.  ".$e->getMessage();
            $all_checks_pass = false;
        }

        if ($all_checks_pass) {
            $response_code = 200;
            $response_data = [
                'success' => true,
            ];
        } else {
            $response_code = 500;
            $response_data = [
                'success' => false,
                'message' => implode("\n", $errors),
                'errors'  => $errors,
            ];
        }


        return response()->json($response_data, $response_code);
    }
}
