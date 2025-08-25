<?php

namespace App\Mail;

use App\Models\EmailSequence;
use App\Models\MarketingStep;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarketingEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $sequence;
    public $step;
    public $unsubscribeUrl;

    public function __construct(EmailSequence $sequence)
    {
        $this->sequence = $sequence;
        $this->step = $sequence->current_step;
        $this->unsubscribeUrl = url('/unsubscribe/marketing/' . $sequence->unsub_token);
    }

    public function build()
    {
        $marketingStep = MarketingStep::where('order', $this->step)->first();
        
        if (!$marketingStep) {
            // Fallback to welcome template
            $viewName = 'emails.marketing.welcome';
            $subject = "Marketing Email Step {$this->step}";
        } else {
            $viewName = 'emails.marketing.' . str_replace('.blade.php', '', $marketingStep->filename);
            $subject = $marketingStep->title;
        }
        
        // Log the email send with detailed info
        \Log::info('Marketing email sent', [
            'recipient_name' => trim($this->sequence->first . ' ' . $this->sequence->last),
            'recipient_email' => $this->sequence->email,
            'step_number' => $this->step,
            'step_title' => $subject,
            'sent_at' => now()->toDateTimeString()
        ]);
        
        // Get the email content
        $emailContent = view($viewName, [
            'sequence' => $this->sequence,
            'record' => $this->sequence,
            'firstName' => $this->sequence->first,
            'lastName' => $this->sequence->last,
            'email' => $this->sequence->email,
            'currentStep' => $this->step,
            'unsubscribeUrl' => $this->unsubscribeUrl
        ])->render();
        
        return $this->subject($subject)
            ->view('email-templates.wrapper')
            ->with([
                'title' => $subject,
                'emailContent' => $emailContent,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'hasUnsubscribe' => false
            ]);
    }
}
