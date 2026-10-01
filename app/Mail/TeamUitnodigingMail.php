<?php

namespace App\Mail;

use App\Models\Medewerker;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar een nieuw teamlid: de link waarmee hij zijn wachtwoord kiest */
class TeamUitnodigingMail extends Mailable
{
    public function __construct(public Medewerker $medewerker) {}

    public function envelope(): Envelope
    {
        $zaak = $this->medewerker->user->onboarding['name'] ?? 'een pizzeria';

        return new Envelope(subject: $zaak . ' nodigt je uit in het team');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.team-uitnodiging', with: [
            'zaak' => $this->medewerker->user->onboarding['name'] ?? 'de pizzeria',
            'link' => route('bezorger.activeren', $this->medewerker->uitnodiging_token),
        ]);
    }
}
