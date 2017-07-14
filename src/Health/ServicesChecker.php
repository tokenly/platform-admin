<?php

namespace Tokenly\PlatformAdmin\Health;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * checks various services
 */
class ServicesChecker {

    public function __construct() {
    }

    public function checkService($service_name, $params) {
        if (!method_exists($this, "check_{$service_name}")) {
            return ['up' => false, 'note' => "Service check for $service_name not found"];
        }

        return call_user_func([$this, "check_{$service_name}"], $params);
    }

    ////////////////////////////////////////////////////////////////////////
    // Built in Checks
    
    public function check_mysql() {
        try {
            $result = DB::selectOne('SELECT 1 AS n');
            if ($result->n != 1) { throw new Exception("Unexpected Database Connection Result", 1); }
            return ['up' => true, 'note' => null];
        } catch (Exception $e) {
            Log::warning("Database Connection Failed: ".$e->getMessage());
            $note = $e->getMessage();
            return ['up' => false, 'note' => $note];
        }
    }
    
    public function check_queueConnection($params) {
        $connection = isset($params['connection']) ? $params['connection'] : null;

        try {
            $pheanstalk = Queue::connection($connection)->getPheanstalk();
            $stats = $pheanstalk->stats();
            if ($stats['uptime'] < 1) { throw new Exception("Unexpected Queue Connection", 1); }
            return ['up' => true, 'note' => null];
        } catch (Exception $e) {
            Log::warning("Queue Connection Failed: ".$e->getMessage());
            $note = $e->getMessage();
            return ['up' => false, 'note' => $note];
        }
    }

    public function check_queueSizes($params) {
        $queue_params = isset($params['queue_params']) ? $params['queue_params'] : [];
        $connection   = isset($params['connection']) ? $params['connection'] : null;

        $errors = [];
        foreach($queue_params as $queue_name => $max_size) {
            try {
                $queue_size = $this->getQueueSize($queue_name, $connection);
                if ($queue_size >= $max_size) {
                    $errors[] = "Queue $queue_name was $queue_size";
                }
            } catch (Exception $e) {
                $note = "checkQueueSizes ($queue_name) failed: ".$e->getMessage();
                Log::error($note);
                $errors[] = $note;
            }
        }

        if ($errors) {
            $note = implode(", ", $errors);
            return ['up' => false, 'note' => $note];
        }

        return ['up' => true, 'note' => null];
    }

    public function check_totalQueueJobsVelocity($params) {
        $queue_velocity_params = isset($params['queue_velocity_params']) ? $params['queue_velocity_params'] : [];
        $connection   = isset($params['connection']) ? $params['connection'] : null;

        $errors = [];
        foreach($queue_velocity_params as $queue_name => $velocity_params) {
            // echo "checking queue $queue_name.  Now is ".Carbon::now()."\n";

            try {
                $minumum_velocity = $velocity_params[0];
                $time_description = $velocity_params[1];

                $now = Carbon::now();
                $old_time = Carbon::parse('-'.$time_description);
                $seconds_to_check = $old_time->diffInSeconds($now);
                $total_size_now = $this->getTotalQueueJobs($queue_name, $connection);
                $total_size_past = $this->getTotalQueueJobsInThePast($queue_name, $old_time);

                // cache $total_size_now
                $expires_at_time = $now->copy()->addSeconds($seconds_to_check)->addMinutes(10);
                $key = 'qTotalJobs_'.$queue_name.'_'.$now->format('Ymd_Hi');
                Cache::add($key, $total_size_now, $expires_at_time);


                if ($total_size_past === null) {
                    // not enough information - pass for now
                    continue;
                }

                $actual_velocity = $total_size_now - $total_size_past;
                // echo "$queue_name \$actual_velocity=$actual_velocity\n";
                if ($actual_velocity >= $minumum_velocity) {
                    // passes check
                } else {
                    $note = "Queue $queue_name velocity was $actual_velocity in $time_description";
                    $errors[] = $note;
                    Log::warn($note);
                }
            } catch (Exception $e) {
                $note = "Error checking queue $queue_name velocity: ".$e->getMessage();
                Log::error($note);
                $errors[] = $note;
            }
        }

        if ($errors) {
            $note = implode(", ", $errors);
            return ['up' => false, 'note' => $note];
        }

        return ['up' => true, 'note' => null];
    }

    // ------------------------------------------------------------------------

    protected function getTotalQueueJobsInThePast($queue_name, $old_time) {
        $now = Carbon::now()->second(0);
        $working_time = $old_time->copy()->second(0);

        $max_minutes_to_check = 10;
        while($working_time->lte($now)) {
            $key = 'qTotalJobs_'.$queue_name.'_'.$working_time->format('Ymd_Hi');
            $value = Cache::get($key);
            if ($value !== null) { return $value; }

            $working_time->addMinutes(1);
        }
        return null;
    }

    public function getTotalQueueJobs($queue_name, $connection=null) {
        $pheanstalk = Queue::connection($connection)->getPheanstalk();
        $stats = $pheanstalk->statsTube($queue_name);
        return $stats['total-jobs'];
    }


    public function getQueueSize($queue_name, $connection=null) {
        $pheanstalk = Queue::connection($connection)->getPheanstalk();
        $stats = $pheanstalk->statsTube($queue_name);
        return $stats['current-jobs-urgent'];
    }

}
