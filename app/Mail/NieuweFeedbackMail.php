<?php

namespace App\Mail;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Naar het beheer zodra een zaak een feedbackformulier invult */
class NieuweFeedbackMail extends Mailable
{
    public function __construct(
        public User $zaak,
        public Feedback $feedback,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Feedback van ' . ($this->zaak->onboarding['name'] ?? $this->zaak->name) . ' (' . $this->feedback->context . ')');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nieuwe-feedback');
    }
}
