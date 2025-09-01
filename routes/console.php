<?php

use Illuminate\Support\Facades\Schedule;

// Your scheduled tasks

Schedule::command('marketing:send')->everyTenMinutes();

Schedule::command('newsletters:send')->everyTenMinutes();

Schedule::command('locations:update-from-ip --limit=5')->everyFifteenMinutes();

// test task: writes to storage/logs/schedule-test.log every minute
// Schedule::command('inspire')->everyMinute()->appendOutputTo(storage_path('logs/schedule-test.log'));


Schedule::command('logs:clear')->daily();
