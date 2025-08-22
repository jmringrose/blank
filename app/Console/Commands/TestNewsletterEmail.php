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
    protected $signature = 'email:test-newsletter {step} {--email= : Email address to send test to}';
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
        $testEmail = $this->option('email') ?: config('mail.from.address', 'admin@example.com');
        $testSequence->email = $testEmail;
        $testSequence->current_step = $stepNumber;
        $testSequence->unsub_token = Str::random(32);
        $testSequence->tour_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $testSequence->tour_date_str = Carbon::now()->addDays(30)->format('j M y');
        $testSequence->id = 999999; // Fake ID for test
        
        try {
            \Log::info("Sending test newsletter email", [
                'step' => $stepNumber,
                'title' => $step->title,
                'recipient' => $testEmail,
                'time' => now()->toDateTimeString()
            ]);
            
            Mail::to($testEmail)
                ->sendNow(new NewsletterEmail($testSequence, $step));
                
            \Log::info("Test newsletter email sent successfully", [
                'step' => $stepNumber,
                'recipient' => $testEmail
            ]);
            
            $this->info("Test newsletter email sent to {$testEmail} for step {$stepNumber}: {$step->title}");
            return 0;
        } catch (\Exception $e) {
            \Log::error("Failed to send test newsletter email: {$e->getMessage()}");
            $this->error("Failed to send test email: {$e->getMessage()}");
            return 1;
        }
    }
}
