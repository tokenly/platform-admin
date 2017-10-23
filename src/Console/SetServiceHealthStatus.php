<?php

namespace Tokenly\PlatformAdmin\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelEventLog\Facade\EventLog;

class SetServiceHealthStatus extends Command {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'platformadmin:set-service-health
                            {status : The new status (up or down)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets service health status';



    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $health_filepath = Config::get('platformadmin.control.healthFilepath');
        switch (strtolower($this->argument('status'))) {
            case 'up':
                if (file_exists($health_filepath)) {
                    unlink($health_filepath);
                    $this->comment('status changed from down to up');
                } else {
                    $this->comment('status changed from up to up');
                }
                break;
            
            case 'down':
                $was_down = file_exists($health_filepath);
                file_put_contents($health_filepath, time());
                if ($was_down) {
                    $this->comment('status changed from down to down');
                } else {
                    $this->comment('status changed from up to down');
                }
                break;

            default:
                throw new Exception("Unknown status provided", 1);
                break;
        }

    }

 }
