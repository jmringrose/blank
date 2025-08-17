<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SimpleTestEmail extends Command
{
    protected $signature = 'email:simple-test';
    protected $description = 'Send a simple test email to admin';

    public function handle()
    {
        $adminEmail = env('ADMIN_EMAIL');
        
        if (!$adminEmail) {
            $this->error('ADMIN_EMAIL not set in .env file');
            return 1;
        }

        try {
            Mail::raw('This is a simple test email to verify email functionality.', function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('Email Test - ' . now()->format('Y-m-d H:i:s'));
            });

            $this->info("Test email sent to {$adminEmail}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send test email: {$e->getMessage()}");
            return 1;
        }
    }
}
