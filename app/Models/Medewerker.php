<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Een teamlid van een pizzeria: bezorger of keukenhulp. Teamleden hebben geen
 * dashboard-account; ze werken op hun eigen scherm (/bezorger) en kunnen daar
 * alleen bij de bestellingen van hun eigen zaak.
 */
#[Fillable(['user_id', 'naam', 'email', 'telefoon', 'wachtwoord', 'rol', 'actief', 'uitnodiging_token', 'uitgenodigd_op', 'laatst_actief_op'])]
#[Hidden(['wachtwoord', 'uitnodiging_token'])]
class Medewerker extends Model
{
    protected $table = 'medewerkers';

    public const ROLLEN = ['bezorger', 'keuken'];

    protected function casts(): array
    {
        return [
            'actief' => 'boolean',
            'wachtwoord' => 'hashed',
            'uitgenodigd_op' => 'datetime',
            'laatst_actief_op' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** De ritten die dit teamlid heeft opgepakt */
    public function ritten(): HasMany
    {
        return $this->hasMany(Order::class, 'bezorger_id');
    }

    /** Uitnodiging nog niet geaccepteerd: er is nog geen wachtwoord gekozen */
    public function isUitgenodigd(): bool
    {
        return $this->wachtwoord === null;
    }

    public function isBezorger(): bool
    {
        return $this->rol === 'bezorger';
    }

    public function rolLabel(): string
    {
        return $this->rol === 'keuken' ? 'Keuken' : 'Bezorger';
    }
}
