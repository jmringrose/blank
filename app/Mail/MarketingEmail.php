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
        $this->unsubscribeUrl = url('/unsubscribe?token=' . $sequence->unsub_token);
    }

    public function build()
    {
        $marketingStep = MarketingStep::where('order', $this->step)
            ->where('draft', false)
            ->first();
        
        if (!$marketingStep) {
            throw new \Exception("Marketing step {$this->step} not found or is draft");
        }
        
        $viewName = 'emails.marketing.' . str_replace('.blade.php', '', $marketingStep->filename);
        
        return $this->subject($marketingStep->title)
            ->view($viewName)
            ->with([
                'sequence' => $this->sequence,
                'firstName' => $this->sequence->first,
                'lastName' => $this->sequence->last,
                'email' => $this->sequence->email,
                'currentStep' => $this->step,
                'unsubscribeUrl' => $this->unsubscribeUrl
            ]);
    }
}
