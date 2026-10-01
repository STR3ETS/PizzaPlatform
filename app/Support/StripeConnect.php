<?php

namespace App\Support;

use App\Models\User;
use Stripe\StripeClient;

/**
 * Stripe Connect: elke pizzeria krijgt een eigen Express-account. Klanten betalen
 * rechtstreeks op dat account (direct charges), dus het geld komt nooit op onze
 * rekening en wij hebben geen vergunning nodig. Wij rekenen geen commissie, dus
 * er gaat ook geen application_fee overheen; alleen de transactiekosten van
 * Stripe zelf komen ten laste van de zaak.
 */
class StripeConnect
{
    /** Is Stripe überhaupt ingesteld? Zonder sleutels draait alles zonder betaalstap (lokaal, demo). */
    public static function beschikbaar(): bool
    {
        return (bool) config('services.stripe.secret');
    }

    /**
     * Staat de betaalstap aan? Zet STRIPE_BETALINGEN=true zodra het Connect-platform
     * bij Stripe is goedgekeurd. Staat hij uit, dan komen bestellingen binnen zoals
     * voorheen: zonder betaling, zodat demo's en tests blijven werken.
     */
    public static function betalingenAan(): bool
    {
        return self::beschikbaar() && filter_var(config('services.stripe.betalingen'), FILTER_VALIDATE_BOOL);
    }

    public static function client(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Stripe stuurt soms adviesmeldingen mee in een response-header (bijvoorbeeld over
     * een nieuwere API-versie). De SDK maakt daar een PHP-waarschuwing van en Laravel
     * maakt van elke waarschuwing een exception, waardoor een gelukte aanroep alsnog
     * zou klappen. Alleen waarschuwingen uit de Stripe-SDK negeren we daarom hier.
     */
    public static function stil(callable $aanroep)
    {
        set_error_handler(
            fn (int $ernst, string $bericht, string $bestand = '') => str_contains($bestand, 'stripe-php'),
            E_USER_WARNING | E_USER_DEPRECATED | E_USER_NOTICE
        );

        try {
            return $aanroep();
        } finally {
            restore_error_handler();
        }
    }

    /** Kan deze zaak op dit moment geld ontvangen? */
    public static function kanOntvangen(User $user): bool
    {
        return $user->stripe_account_id !== null && (bool) $user->stripe_uitbetalingen;
    }

    /** Het Express-account van deze zaak, of een nieuw account als het er nog niet is */
    public static function account(User $user): string
    {
        if ($user->stripe_account_id) {
            return $user->stripe_account_id;
        }

        $ob = $user->onboarding ?? [];
        $account = self::stil(fn () => self::client()->accounts->create([
            'type' => 'express',
            'country' => 'NL',
            'email' => $user->email,
            'default_currency' => 'eur',
            'business_type' => 'company',
            'business_profile' => [
                'name' => $ob['name'] ?? $user->name,
                'mcc' => '5812',   // eetgelegenheden
                'url' => self::bestelUrl($user),
                'product_description' => 'Online bestellingen van pizza en aanverwante gerechten',
            ],
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'ideal_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'settings' => [
                'payouts' => ['schedule' => ['interval' => 'daily', 'delay_days' => 'minimum']],
            ],
            'metadata' => ['user_id' => (string) $user->id],
        ]));

        $user->forceFill(['stripe_account_id' => $account->id])->save();

        return $account->id;
    }

    /** De actuele stand bij Stripe ophalen en bij de zaak vastleggen */
    public static function ververs(User $user): void
    {
        if (! $user->stripe_account_id || ! self::beschikbaar()) {
            return;
        }

        try {
            $account = self::stil(fn () => self::client()->accounts->retrieve($user->stripe_account_id));
            $user->forceFill([
                'stripe_uitbetalingen' => (bool) $account->payouts_enabled && (bool) $account->charges_enabled,
                'stripe_gegevens_nodig' => ! empty($account->requirements->currently_due ?? [])
                    || ! empty($account->requirements->past_due ?? []),
                'stripe_gecontroleerd_op' => now(),
            ])->save();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** De link waarmee de zaak zijn gegevens bij Stripe invult of aanvult */
    public static function onboardingLink(User $user): string
    {
        $account = self::account($user);

        return self::stil(fn () => self::client()->accountLinks->create([
            'account' => $account,
            'refresh_url' => route('stripe.connect.start'),
            'return_url' => route('stripe.connect.terug'),
            'type' => 'account_onboarding',
        ])->url);
    }

    /** Doorklik naar het eigen Stripe-dashboard van de zaak (omzet, uitbetalingen) */
    public static function stripeDashboardLink(User $user): ?string
    {
        if (! $user->stripe_account_id) {
            return null;
        }

        try {
            return self::stil(fn () => self::client()->accounts->createLoginLink($user->stripe_account_id)->url);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** De publieke bestelpagina van de zaak, voor het Stripe-profiel */
    private static function bestelUrl(User $user): string
    {
        $domein = config('app.centraal_domein');

        return $domein && $user->slug
            ? 'https://' . $user->slug . '.' . $domein
            : rtrim(config('app.url'), '/') . '/bestellen/' . $user->slug;
    }
}
