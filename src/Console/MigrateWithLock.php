<?php

namespace Tokenly\PlatformAdmin\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;
use Tokenly\PlatformAdmin\Console\Contracts\RunsInPlatformAdmin;
use Tokenly\RecordLock\RecordLock;

class MigrateWithLock extends Command implements RunsInPlatformAdmin {


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
    public function handle()
    {
        $lock = app(RecordLock::class);

        $lock->acquireAndExecute('migrate', function() {
            $args = ['--force' => true];
            if ($this->input->hasOption('path') && $this->option('path')) {
                $args = array_merge($args, ['--path' => $this->option('path')]);
            }
            $this->call('migrate', $args);
        });

    }

    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.'],
        ];
    }

 }
