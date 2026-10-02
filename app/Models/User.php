<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'onboarding', 'slug', 'proef_tot'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function klanten(): HasMany
    {
        return $this->hasMany(Klant::class);
    }

    /** Teamleden van deze zaak: bezorgers en keukenhulp */
    public function medewerkers(): HasMany
    {
        return $this->hasMany(Medewerker::class);
    }

    /** Nog in de gratis proefmaand (en zonder betaald abonnement)? */
    public function inProefperiode(): bool
    {
        return ! $this->abonnement_actief && $this->proef_tot !== null && $this->proef_tot->isFuture();
    }

    /** Mag het dashboard gebruiken: betaald abonnement of lopende proefperiode */
    public function heeftToegang(): bool
    {
        return $this->abonnement_actief || $this->inProefperiode();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding' => 'array',
            'intro_seen' => 'boolean',
            'is_online' => 'boolean',
            'is_admin' => 'boolean',
            'abonnement_actief' => 'boolean',
            'abonnement_sinds' => 'datetime',
            'proef_tot' => 'datetime',
            'opvolgen_vanaf' => 'datetime',
            'lat' => 'float',
            'lng' => 'float',
            'bezorgkosten' => 'array',
            'printer_aan' => 'boolean',
            'printer_opties' => 'array',
            'printer_gezien_om' => 'datetime',
        ];
    }

    /** De bonnen die de bonprinter van deze zaak nog moet ophalen of al printte */
    public function printJobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrintJob::class);
    }
}
