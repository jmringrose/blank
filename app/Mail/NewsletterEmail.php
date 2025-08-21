<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsletterSequence;
use App\Models\NewsletterStep;

class NewsletterEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $sequence;
    public $step;
    public $unsubscribeUrl;

    public function __construct(NewsletterSequence $sequence, NewsletterStep $step)
    {
        $this->sequence = $sequence;
        $this->step = $step;
        $this->unsubscribeUrl = url('/unsubscribe/newsletter/' . $sequence->unsub_token);
    }

    public function build()
    {
        $viewName = 'emails.newsletters.' . str_replace('.blade.php', '', $this->step->filename);
        
        // Calculate days to go if tour_date exists
        $daysToGo = null;
        if ($this->sequence->tour_date) {
            $tourDate = \Carbon\Carbon::parse($this->sequence->tour_date);
            $daysToGo = round($tourDate->diffInDays(now(), false));
            $daysToGo = $daysToGo < 0 ? abs($daysToGo) : 0; // Future dates = positive, past dates = 0
        }
        
        // Add name property to sequence for template compatibility
        $this->sequence->name = trim($this->sequence->first . ' ' . $this->sequence->last);
        
        return $this->subject($this->step->title)
            ->view($viewName)
            ->with([
                'record' => $this->sequence,
                'firstName' => $this->sequence->first,
                'lastName' => $this->sequence->last,
                'email' => $this->sequence->email,
                'currentStep' => $this->sequence->current_step,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'daysToGo' => $daysToGo
            ]);
    }
}
