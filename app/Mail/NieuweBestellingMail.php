<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar de pizzeria, bij elke nieuwe bestelling */
class NieuweBestellingMail extends Mailable
{
    public function __construct(
        public Order $order,
        public User $pizzeria,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nieuwe bestelling #' . $this->order->nummer . ', € ' . number_format($this->order->totaal / 100, 2, ',', '.'));
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nieuwe-bestelling');
    }
}
