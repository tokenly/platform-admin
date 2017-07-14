<?php

namespace Tokenly\PlatformAdmin\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelEventLog\Facade\EventLog;
use Tokenly\PlatformAdmin\Health\ServicesChecker;

class ServiceCheck extends Command {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'platformadmin:service-check
                            {service : The service name}
                            {config? : The service check parameters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks service health status';



    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire(ServicesChecker $services_checker)
    {

        $config_string = $this->argument('config');
        $params = strlen($config_string) ? json_decode($config_string, true) : [];

        $service = $this->argument('service');
        $result = $services_checker->checkService($service, $params);

        if ($result['up']) {
            $this->line("service {$service} ok");
            return 0;
        }

        $this->line($result['note']);
        return 255;
    }

 }
