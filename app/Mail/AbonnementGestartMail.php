<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar de pizzeria, zodra het abonnement geactiveerd is */
class AbonnementGestartMail extends Mailable
{
    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Je MijnPizzeria-abonnement is actief');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.abonnement-gestart');
    }
}
