<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\MarketingEmail;
use App\Models\EmailSequence;
use App\Models\MarketingStep;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestMarketingEmail extends Command
{
    protected $signature = 'email:test-marketing {step} {--email= : Email address to send test to}';
    protected $description = 'Send test marketing email for specified step';

    public function handle()
    {
        $stepNumber = $this->argument('step');
        $step = MarketingStep::where('order', $stepNumber)->first();
        
        if (!$step) {
            $this->error("Marketing step {$stepNumber} not found");
            return 1;
        }
        
        // Create test sequence data
        $testEmail = $this->option('email') ?: config('mail.from.address', 'admin@example.com');
        $testSequence = new EmailSequence([
            'first' => 'Test',
            'last' => 'User',
            'email' => $testEmail,
            'current_step' => $stepNumber,
            'unsub_token' => Str::random(32)
        ]);
        
        try {
            \Log::info("Sending test marketing email for step {$stepNumber} to {$testEmail}");
            
            Mail::to($testEmail)
                ->sendNow(new MarketingEmail($testSequence, $step));
                
            \Log::info("Test marketing email sent successfully for step {$stepNumber}: {$step->title}");
            $this->info("Test marketing email sent to {$testEmail} for step {$stepNumber}: {$step->title}");
            return 0;
        } catch (\Exception $e) {
            \Log::error("Failed to send test marketing email: {$e->getMessage()}");
            $this->error("Failed to send test email: {$e->getMessage()}");
            return 1;
        }
    }
}
