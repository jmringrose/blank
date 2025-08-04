<?php

namespace App\Mail;

use App\Models\EmailSequence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FollowUpStep extends Mailable
{
    use Queueable, SerializesModels;

    public $sequence;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailSequence $sequence)
    {
        $this->sequence = $sequence; // Assigning the record instance to use in the emaile
        $this->step = $sequence->current_step;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email_name = "email_one";
       /* if () {}*/

        return $this->subject("Step {$this->step} – Photography Tips") // Use $step explicitly
        ->view("emails.steps.".$email_name) // Reference the step-based view file dynamically
        ->with('record', $this->sequence); // Pass the sequence to the view
    }
}
