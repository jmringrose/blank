<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLogs extends Command
{
    protected $signature = 'logs:clear';
    protected $description = 'Clear all Laravel log files';

    public function handle()
    {
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            $this->info('No logs directory found.');
            return;
        }

        $files = File::files($logPath);
        $count = 0;

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                File::put($file, '');
                $count++;
            }
        }

        $this->info("Cleared {$count} log files.");
    }
}
