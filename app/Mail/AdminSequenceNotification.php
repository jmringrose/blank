<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminSequenceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $action;
    public $sequence;

    public function __construct($action, $sequence)
    {
        $this->action = $action;
        $this->sequence = $sequence;
    }

    public function build()
    {
        return $this->subject("User {$this->action} notification")
            ->view('emails.admin-sequence-notification');
    }
}
