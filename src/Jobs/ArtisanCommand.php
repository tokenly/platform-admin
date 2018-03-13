<?php

namespace Tokenly\PlatformAdmin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\StringInput;
use Tokenly\PlatformAdmin\Jobs\Output\PusherChannelOutput;

class ArtisanCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $command_string = '';
    protected $command_id = '';

    public function __construct($command_string, $command_id)
    {
        $this->command_string = $command_string;
        $this->command_id = $command_id;
    }

    // handle the job
    public function handle()
    {
        Log::debug("ArtisanCommand handle begin $this->command_string");

        $input = new StringInput($this->command_string);
        $output = new PusherChannelOutput();

        // init pusher
        $pusher = Broadcast::connection()->getPusher();
        $channel_id = config('platformadmin.console.pusher_channel_base').$this->command_id;
        $output->setPusherConnectionAndChannel($pusher, $channel_id);

        // begin pushing updates
        $output->open();

        // handle the artisan command
        $kernel = app(Kernel::class);
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);

        // delay a bit to make sure the front-end loaded
        usleep(350000);
        $output->writeln('');

        // wait a little longer and send the final output to pusher
        usleep(750000);
        $output->close($status);

        Log::debug("ArtisanCommand handle end $this->command_string");
    }

}
