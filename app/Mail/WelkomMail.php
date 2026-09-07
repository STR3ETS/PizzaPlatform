<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar de pizzeria, direct na de registratie */
class WelkomMail extends Mailable
{
    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welkom bij MijnPizzeria, je proefmaand is gestart');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.welkom');
    }
}
