<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestDeliveryMail;

class SendTestEmail extends Command
{
    protected $signature = 'mail:test
                            {email : The recipient email address}
                            {--name= : Recipient name to personalize (default: "there")}
                            {--subject= : Custom subject line}
                            {--queue : Queue the email instead of sending synchronously}
                            {--testDMARC : Send a DMARC test email }';

    protected $description = 'Send a simple delivery test email to a recipient';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = $this->option('name') ?: 'there';
        $subject = $this->option('subject');
        $dmarc = $this->option('testDMARC');

        if ($dmarc > '') {
            $email = 'check-auth@verifier.port25.com';
            $subject = 'DMARC test';
            $name = 'DMARC';
        }

        $mailable = new TestDeliveryMail(
            recipientName: $name,
            customSubject: $subject
        );

        $this->info("Sending and email to: ".$email);
        $this->info("Email subject: ".$subject);
        $this->info("Person's name: ".$name);

        if ($this->option('queue')) {
            Mail::to($email)->queue($mailable);
            $this->info("Queued test email to {$email}.");
        } else {
            Mail::to($email)->send($mailable);
            $this->info("Sent test email to {$email}.");
        }

        return self::SUCCESS;
    }
}
