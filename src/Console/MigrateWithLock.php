<?php

namespace Tokenly\PlatformAdmin\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelEventLog\Facade\EventLog;
use Tokenly\RecordLock\RecordLock;

class MigrateWithLock extends Command {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'platformadmin:migrate-with-lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs laravel migrations within a lock';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $lock = app(RecordLock::class);
        $lock->acquireAndExecute('migrate', function() {
            $this->call('migrate', ['--force']);
        });

    }

 }
