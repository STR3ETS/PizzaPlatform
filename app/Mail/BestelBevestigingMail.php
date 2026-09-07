<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar de klant van de pizzeria, direct na het plaatsen van een bestelling */
class BestelBevestigingMail extends Mailable
{
    public function __construct(
        public Order $order,
        public User $pizzeria,
        public string $statusLink,
    ) {}

    public function envelope(): Envelope
    {
        $zaakNaam = $this->pizzeria->onboarding['name'] ?? 'de pizzeria';

        // De mail komt op naam van de zaak binnen, niet van het platform
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $zaakNaam),
            subject: 'Bedankt voor je bestelling bij ' . $zaakNaam . ' (#' . $this->order->nummer . ')',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.bestel-bevestiging');
    }
}
