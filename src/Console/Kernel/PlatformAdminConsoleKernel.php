<?php

namespace Tokenly\PlatformAdmin\Console\Kernel;

use App\Console\Kernel as AppKernel;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Tokenly\PlatformAdmin\Console\Kernel\PlatformAdminConsoleApplication as Artisan;

class PlatformAdminConsoleKernel extends AppKernel
{

    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            $this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
                ->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

    public function getResolvedArtisanCommand(InputInterface $input = null)
    {
        $this->bootstrap();

        $artisan = $this->getArtisan();
        return $artisan->getResolvedArtisanCommand($input);
    }

}
