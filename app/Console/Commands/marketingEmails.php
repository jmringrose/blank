<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailSequence;
use App\Mail\MarketingEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class marketingEmails extends Command
{
    protected $signature = 'marketing:send';
    protected $description = 'Send marketing email sequence steps';

    public function handle()
    {
        $this->info('Starting marketing email processing...');
        
        $sequences = EmailSequence::where('next_send_at', '<=', Carbon::now())
            ->where('current_step', '>', 0)
            ->get();

        $this->info("Found {$sequences->count()} sequences ready to send.");

        foreach ($sequences as $sequence) {
            $this->processSequence($sequence);
        }

        $this->info('Marketing email processing completed.');
    }

    private function processSequence($sequence)
    {
        if ($sequence->current_step <= 0) {
            $this->warn("Sequence {$sequence->id} is unsubscribed (step {$sequence->current_step})");
            return;
        }

        if ($sequence->current_step >= 5) {
            $this->warn("Sequence {$sequence->id} has completed all steps.");
            return;
        }

        try {
            Mail::to($sequence->email)->send(new MarketingEmail($sequence));
            
            $sequence->current_step += 1;
            $sequence->next_send_at = Carbon::now()->addDays(7);
            $sequence->save();
            
            $this->info("Sent marketing email step {$sequence->current_step} to {$sequence->email}");
        } catch (\Exception $e) {
            $this->error("Failed to send marketing email to {$sequence->email}: {$e->getMessage()}");
        }
    }
}
