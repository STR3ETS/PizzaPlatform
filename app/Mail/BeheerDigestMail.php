<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Dagelijkse samenvatting voor het beheer: wat vraagt er vandaag om aandacht */
class BeheerDigestMail extends Mailable
{
    public function __construct(public array $digest) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Je MijnPizzeria-dag in het kort');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.beheer-digest');
    }
}
