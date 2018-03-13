<?php

namespace Tokenly\PlatformAdmin\Console\Kernel;

use Exception;
use Illuminate\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Tokenly\LaravelEventLog\Facade\EventLog;


class PlatformAdminConsoleApplication extends Application
{

    // resolve the artisan command
    public function getResolvedArtisanCommand(InputInterface $input = null) {
        $name = $this->getCommandName($input);
        try {
            $command = $this->find($name);
        } catch (Exception $e) {
            EventLog::logError('command.notFound', $e, ['name' => $name]);
            return null;
        }
        return $command;
    }

}
