<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tokenly\LaravelEventLog\Facade\EventLog;
use Tokenly\PlatformAdmin\Console\Contracts\RunsInPlatformAdmin;
use Tokenly\PlatformAdmin\Console\Kernel\PlatformAdminConsoleKernel;
use Tokenly\PlatformAdmin\Controllers\Controller;
use Tokenly\PlatformAdmin\Jobs\ArtisanCommand;

class ArtisanCommandController extends Controller
{

    public function showForm()
    {
        return $this->renderView();
    }

    public function watchCommandResults($command_id)
    {
        $session_data = session($command_id);
        if (!$session_data) {
            return $this->renderView()->withErrors('command not found');
        }

        return $this->renderView([
            'command' => $session_data['command_string'],
            'pusherChannelName' => config('platformadmin.console.pusher_channel_base') . $session_data['id'],
        ]);
    }

    public function runCommand(Request $request)
    {
        $response = '';

        $command_string = $request->input('command');
        if (!strlen($command_string)) {
            return redirect(route('platform.admin.artisan.command'))
                ->withInput()
                ->withErrors(['command' => 'No command was specified']);
        }

        $kernel = app(PlatformAdminConsoleKernel::class);
        $command = $kernel->getResolvedArtisanCommand(new StringInput($command_string));
        if (!$this->canRunCommand($command, $command_string)) {
            return redirect(route('platform.admin.artisan.command'))
                ->withInput()
                ->withErrors(['command' => 'This command is not available from the platform admin.  Implement the RunsInPlatformAdmin interface for custom commands.']);
        }

        if (config('platformadmin.console.use_background_queue')) {
            $command_id = str_random(20);
            ArtisanCommand::dispatch($command_string, $command_id)->onQueue(config('platformadmin.console.queue'));

            // redirect to the watcher
            session([$command_id => [
                'id' => $command_id,
                'command_string' => $command_string,
            ]]);
            return redirect(route('platform.admin.artisan.command.watch', $command_id));
        } else {
            return $this->runLiveCommand($command_string);
        }
    }

    protected function runLiveCommand($command_string)
    {
        $input = new StringInput($command_string);
        $output = new BufferedOutput();

        $kernel = app(Kernel::class);
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);
        $response = $output->fetch();

        return $this->renderView([
            'command' => $command_string,
            'posted' => true,
            'status' => $status,
            'response' => $response,
        ]);

    }

    protected function renderView($data = [])
    {
        return view('platformAdmin::artisan.command', array_merge([
            'command' => '',
            'posted' => false,
            'status' => '',
            'response' => '',
            'pusherChannelName' => '',
            'pusherAppKey' => env('PUSHER_APP_KEY'),
        ], $data));
    }

    protected function canRunCommand($command, $command_string)
    {
        $command_name = $command_string;
        if ($command and $command instanceof SymfonyCommand) {
            $command_name = $command->getName();
            if ($command instanceof RunsInPlatformAdmin) {
                return true;
            }

            if ($this->isWhitelisted($command_name)) {
                return true;
            }

        }

        EventLog::logError('command.invalid', ['command' => $command_name]);
        return false;
    }

    protected function isWhitelisted($command_name)
    {
        static $whitelisted_commands = [
            'help',
            'inspire',
            'migrate',
            'auth:clear-resets',
            'cache:clear',
            'cache:forget',
            'config:clear',
            'queue:failed',
            'queue:retry',
            'queue:restart',
            'route:clear',
            'route:list',
            'view:clear',
        ];
        return in_array($command_name, $whitelisted_commands);

        return false;
    }
}

/*

 */
