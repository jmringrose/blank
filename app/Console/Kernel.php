<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register custom commands here
        \App\Console\Commands\marketingEmails::class,
        \App\Console\Commands\SendTestEmail::class,
        \App\Console\Commands\NewsletterEmails::class,
        \App\Console\Commands\ClearLogs::class,
        \App\Console\Commands\SimpleTestEmail::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled tasks here
        $schedule->command('marketing:send')->everyTenMinutes();
        $schedule->command('newsletters:send')->everyTenMinutes();
        $schedule->command('logs:clear')->daily();

    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
