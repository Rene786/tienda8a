<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $contactSubject,
        public string $contactMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->senderEmail, $this->senderName)],
            subject: 'Contacto TechStore: '.$this->contactSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-message',
            with: [
                'senderName' => $this->senderName,
                'senderEmail' => $this->senderEmail,
                'contactMessage' => $this->contactMessage,
            ],
        );
    }
}
