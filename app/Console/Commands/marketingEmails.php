<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailSequence;
use App\Mail\FollowUpStep;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class marketingEmails extends Command
{
    protected $signature = 'emails:followup';
    protected $description = 'Send follow-up emails based on sequence steps';

    public function handle()
    {
        $sent = 0;
        $seconds = 5;
        $totalEmails = EmailSequence::count();
        $this->info("Target: ". now());

        //$sequences = EmailSequence::where('id', 82)->get();
        $sequences = EmailSequence::where('next_send_at', '<=', now())->get();


        $this->info('Sending follow-up emails: ' . count($sequences) . " of ".  $totalEmails);


        $this->info(asset('img/emails/small/wide__DSC9054.jpg'));

        foreach ($sequences as $sequence) {
            $this->info("Target: ". now()." - Next: ".$sequence->next_send_at);
            Mail::to($sequence->email)->send(new FollowUpStep($sequence));
            if ($sequence->current_step < 5) {
                $sequence->current_step++;
                $sequence->next_send_at = now()->addDays(2);
                $sequence->save();
                $this->info('Sent follow-up email to ' . $sequence->email);
                $sent++;

            } else {
                //$sequence->delete();
            }
            exit(1);
            sleep($seconds);
        }
        $this->info('Follow-up emails processed. '.$sent.' emails sent.');
    }
}
