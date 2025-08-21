<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\NewsletterEmail;
use App\Models\NewsletterSequence;
use App\Models\NewsletterStep;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestNewsletterEmail extends Command
{
    protected $signature = 'email:test-newsletter {step}';
    protected $description = 'Send test newsletter email for specified step';

    public function handle()
    {
        $stepNumber = $this->argument('step');
        $step = NewsletterStep::where('order', $stepNumber)->first();
        
        if (!$step) {
            \Log::error("Newsletter step {$stepNumber} not found");
            $this->error("Newsletter step {$stepNumber} not found");
            return 1;
        }
        
        \Log::info("Found newsletter step {$stepNumber}: {$step->title}");
        
        // Create test sequence data with tour info
        $testSequence = new NewsletterSequence();
        $testSequence->first = 'Test';
        $testSequence->last = 'User';
        $testSequence->email = config('mail.from.address', 'admin@example.com');
        $testSequence->current_step = $stepNumber;
        $testSequence->unsub_token = Str::random(32);
        $testSequence->tour_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $testSequence->tour_date_str = Carbon::now()->addDays(30)->format('j M y');
        $testSequence->id = 999999; // Fake ID for test
        
        try {
            Mail::to(config('mail.from.address', 'admin@example.com'))
                ->sendNow(new NewsletterEmail($testSequence, $step));
                
            $this->info("Test newsletter email sent for step {$stepNumber}: {$step->title}");
            return 0;
        } catch (\Exception $e) {
            \Log::error("Failed to send test newsletter email: {$e->getMessage()}");
            $this->error("Failed to send test email: {$e->getMessage()}");
            return 1;
        }
    }
}
