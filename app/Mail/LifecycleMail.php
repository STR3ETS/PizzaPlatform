<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * De automatische mails in de klantreis: activeren, converteren en terugwinnen.
 * Welke wanneer gaat, bepaalt het command mails:lifecycle; het mail-logboek
 * voorkomt dat iemand dezelfde mail twee keer krijgt.
 */
class LifecycleMail extends Mailable
{
    public const SOORTEN = [
        'zaak-afmaken' => ['onderwerp' => 'Je bestelpagina staat bijna live, maak je zaak even af', 'view' => 'mail.lifecycle.zaak-afmaken'],
        'proef-bijna-af' => ['onderwerp' => 'Nog een weekje gratis proberen, daarna is het aan jou', 'view' => 'mail.lifecycle.proef-bijna-af'],
        'proef-laatste-dag' => ['onderwerp' => 'Vandaag is de laatste dag van je proefperiode', 'view' => 'mail.lifecycle.proef-laatste-dag'],
        'winback-1' => ['onderwerp' => 'We missen je bij MijnPizzeria', 'view' => 'mail.lifecycle.winback-1'],
        'winback-2' => ['onderwerp' => 'Twee weken extra proberen? Het staat voor je klaar', 'view' => 'mail.lifecycle.winback-2'],
    ];

    public function __construct(public User $user, public string $soort) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: self::SOORTEN[$this->soort]['onderwerp']);
    }

    public function content(): Content
    {
        return new Content(view: self::SOORTEN[$this->soort]['view']);
    }
}
