<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'klant_id', 'bezorger_id', 'nummer', 'token', 'klant', 'items', 'totaal', 'punten', 'punten_gebruikt', 'status', 'betaal_status', 'stripe_session_id', 'stripe_payment_intent', 'betaald_om', 'type', 'adres', 'opmerking', 'eta_minuten', 'bezorgd_om', 'is_demo'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_demo' => 'boolean',
            'bezorgd_om' => 'datetime',
            'betaald_om' => 'datetime',
        ];
    }

    /**
     * Een bestelling die nog op betaling wacht bestaat voor de zaak nog niet: hij
     * hoort niet in het dashboard, niet op het teamscherm en niet in de cijfers.
     * Daarom filteren we die hier één keer weg, in plaats van in elke query apart.
     * Nodig je ze toch (webhook, statuspagina), gebruik dan Order::inclusiefOnbetaald().
     */
    protected static function booted(): void
    {
        static::addGlobalScope('betaald', fn (Builder $query) => $query->where('orders.betaal_status', '!=', 'open'));
    }

    /** Ook de bestellingen die nog op een betaling wachten */
    public static function inclusiefOnbetaald(): Builder
    {
        return static::withoutGlobalScope('betaald');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Het teamlid dat deze rit bezorgt (null zolang niemand hem heeft opgepakt) */
    public function bezorger(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'bezorger_id');
    }

    public function isBetaald(): bool
    {
        return $this->betaal_status === 'betaald';
    }
}
