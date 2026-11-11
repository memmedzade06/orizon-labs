<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Saytdakı "Bizə yazın" formasından gələn müraciəti şirkətin poçtuna göndərir.
 */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->contactMessage->type === 'partner' ? 'Tərəfdaşlıq müraciəti: ' : 'Saytdan yeni müraciət: ')
                . $this->contactMessage->name
                . ($this->contactMessage->company ? ' (' . $this->contactMessage->company . ')' : ''),
            // "Cavab ver" basanda məktub birbaşa müraciət edənə getsin
            replyTo: $this->contactMessage->email
                ? [new Address($this->contactMessage->email, $this->contactMessage->name)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-message');
    }
}
