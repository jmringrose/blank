<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\QuestionSequence;
use App\Models\QuestionStep;

class QuestionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $sequence;
    public $step;
    public $unsubscribeUrl;

    public function __construct(QuestionSequence $sequence, QuestionStep $step)
    {
        $this->sequence = $sequence;
        $this->step = $step;
        $this->unsubscribeUrl = url('/unsubscribe/question/' . $sequence->unsub_token);
    }

    public function build()
    {
        // Ensure filename exists
        if (!$this->step->filename) {
            $this->step->filename = 'question' . $this->step->order . '.blade.php';
            $this->step->save();
        }
        
        $viewName = 'emails.questions.' . str_replace('.blade.php', '', $this->step->filename);
        
        // Create name for template use (don't save to database)
        $name = trim($this->sequence->first . ' ' . $this->sequence->last);
        
        // Log the email send
        \Log::info('Question email sent', [
            'recipient_name' => trim($this->sequence->first . ' ' . $this->sequence->last),
            'recipient_email' => $this->sequence->email,
            'question_title' => $this->step->title,
            'sent_at' => now()->toDateTimeString()
        ]);
        
        return $this->subject($this->step->title)
            ->view($viewName)
            ->with([
                'record' => $this->sequence,
                'firstName' => $this->sequence->first,
                'lastName' => $this->sequence->last,
                'email' => $this->sequence->email,
                'name' => $name,
                'unsubscribeUrl' => $this->unsubscribeUrl
            ]);
    }
}