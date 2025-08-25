<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestDeliveryMail extends Mailable
{
    public function __construct(
        public string $recipientName = 'there',
        public ?string $customSubject = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject ?? 'Delivery test — please reply "Received"'
        );
    }

    public function content(): Content
    {
        // Get the email content
        $emailContent = view('emails.test-delivery', [
            'recipientName' => $this->recipientName,
        ])->render();
        
        return new Content(
            view: 'email-templates.wrapper',
            text: 'emails.test-delivery_plain',
            with: [
                'title' => $this->customSubject ?? 'Delivery test — please reply "Received"',
                'emailContent' => $emailContent,
                'unsubscribeUrl' => '#',
                'hasUnsubscribe' => true // Test emails don't need unsubscribe
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
