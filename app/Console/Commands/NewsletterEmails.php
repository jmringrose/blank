<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsletterSequence;
use App\Models\NewsletterStep;
use App\Mail\NewsletterEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NewsletterEmails extends Command
{
    protected $signature = 'newsletters:send';
    protected $description = 'Send scheduled newsletter emails';

    public function handle()
    {
        $this->info('Starting newsletter email processing...');

        $sequences = NewsletterSequence::where('next_send_at', '<=', Carbon::now())
            ->where('current_step', '>', 0)
            ->get();

        $this->info("Found {$sequences->count()} sequences ready to send.");

        foreach ($sequences as $sequence) {
            $this->processSequence($sequence);
        }

        $this->info('Newsletter email processing completed.');
    }

    private function processSequence($sequence)
    {
        if ($sequence->current_step <= 0) {
            $this->warn("Sequence {$sequence->id} is unsubscribed (step {$sequence->current_step})");
            return;
        }

        $step = NewsletterStep::where('order', $sequence->current_step)
            ->where('draft', false)
            ->first();

        if (!$step) {
            $this->warn("No published step found for sequence {$sequence->id} at step {$sequence->current_step}");
            return;
        }

        if ($step->draft) {
            $this->warn("Step {$sequence->current_step} for sequence {$sequence->id} is in draft - skipping");
            return;
        }

        try {
            Mail::to($sequence->email)->send(new NewsletterEmail($sequence, $step));
            $this->info("Sent newsletter '{$step->title}' to {$sequence->email}");

            $sequence->current_step += 1;
            $sequence->next_send_at = Carbon::now()->addDays(3);
            $sequence->save();
            
            sleep(5); // Rate limit delay

        } catch (\Exception $e) {
            $this->error("Failed to send newsletter to {$sequence->email}: {$e->getMessage()}");
        }
    }
}
