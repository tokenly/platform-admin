<?php

namespace Tokenly\PlatformAdmin\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tokenly\LaravelEventLog\Facade\EventLog;

class CreatePlatformAdmin extends Command {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'platformadmin:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a platform admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['username', InputArgument::REQUIRED, 'Username'],
            ['email',    InputArgument::REQUIRED, 'Email Address'],
            ['password', InputArgument::REQUIRED, 'Password'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit number of items to archive.', null],
        ];
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_repository = app('Tokenly\LaravelApiProvider\Contracts\APIUserRepositoryContract');
        Log::debug("\$user_repository is ".get_class($user_repository));
        $user_vars = [
            'username'        => $this->argument('username'),
            'email'           => $this->argument('email'),
            'confirmed_email' => $this->argument('email'),
            'password'        => $this->argument('password'),
            'privileges'      => ['platformAdmin' => true],
        ];
        $user_model = $user_repository->create($user_vars);
        
        // log
        EventLog::log('user.platformAdmin.cli', $user_model, ['id', 'username', 'email', ]);

        // show the new user
        $user = clone $user_model;
        $user['password'] = '********';
        $this->line(json_encode($user, 192));

        $this->comment('Done.');
    }

}
