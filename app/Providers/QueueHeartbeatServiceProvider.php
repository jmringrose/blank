<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class QueueHeartbeatServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Queue::looping(function () {
            $host = gethostname() ?: 'host';
            $pid  = getmypid() ?: 'pid';
            $ts   = now()->toIso8601String();

            // Per-worker key (great if you have Redis)
            //Cache::put("queue:heartbeat:$host:$pid", $ts, now()->addSeconds(90));

            // Global key so non-Redis stores can still report "up"
            Cache::put('queue:heartbeat:global', $ts, now()->addSeconds(90));
        });
    }
}
