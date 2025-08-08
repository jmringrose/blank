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
        return new Content(
            view: 'emails.test-delivery',
            text: 'emails.test-delivery_plain',
            with: [
                'recipientName' => $this->recipientName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
