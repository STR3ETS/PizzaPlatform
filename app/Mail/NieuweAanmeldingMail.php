<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar het beheer, bij elke nieuwe aanmelding */
class NieuweAanmeldingMail extends Mailable
{
    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nieuwe pizzeria aangemeld: ' . ($this->user->onboarding['name'] ?? $this->user->name));
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nieuwe-aanmelding');
    }
}
