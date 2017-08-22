<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tokenly\PlatformAdmin\Controllers\Controller;

class ArtisanCommandController extends Controller
{



    public function showForm() {
        return view('platformAdmin::artisan.command', [
            'command'  => '',
            'posted'   => false,
            'status'   => '',
            'response' => '',
        ]);


    }

    public function runCommand(Request $request) {
        $response = '';

        $command_string = $request->input('command');
        $input = new StringInput($command_string);

        $kernel = app(\Illuminate\Contracts\Console\Kernel::class);

        $output = new BufferedOutput();
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);
        $response = $output->fetch();



        // // look up the command
        // $console_app = app(Application::class);

        // $output = new BufferedOutput();
        // // $console_app->setCatchExceptions(false);
        // $result = $console_app->run(new ArrayInput($parameters->toArray()), $output);
        // // $console_app->setCatchExceptions(true);

        // $response = $output->fetch();

        // Artisan::call('alexandria:sync', ['--quiet' => true]);

        return view('platformAdmin::artisan.command', [
            'command'  => $command_string,
            'posted'   => true,
            'status'   => $status,
            'response' => $response,
        ]);

    }

}
