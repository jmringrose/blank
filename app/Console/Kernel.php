<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Load all command classes in app/Console/Commands
        $this->load(__DIR__ . '/Commands');

        // Keep schedules in routes/console.php on Laravel 11+
        // If you ever need to define closures or route-based commands,
        // they will be picked up from routes/console.php by bootstrap/app.php
        require base_path('routes/console.php');
    }
}

