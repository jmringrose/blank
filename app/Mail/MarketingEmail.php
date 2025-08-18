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
        
        return $this->subject($subject)
            ->view($viewName)
            ->with([
                'sequence' => $this->sequence,
                'record' => $this->sequence,
                'firstName' => $this->sequence->first,
                'lastName' => $this->sequence->last,
                'email' => $this->sequence->email,
                'currentStep' => $this->step,
                'unsubscribeUrl' => $this->unsubscribeUrl
            ]);
    }
}
