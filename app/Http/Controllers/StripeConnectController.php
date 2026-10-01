<?php

namespace App\Http\Controllers;

use App\Support\StripeConnect;
use Illuminate\Http\Request;

/**
 * De koppeling met Stripe vanuit het dashboard: de zaak vult bij Stripe zelf
 * zijn gegevens in (IBAN, KvK, identiteit) en wij onthouden alleen of hij
 * uitbetalingen mag ontvangen.
 */
class StripeConnectController extends Controller
{
    /** Naar Stripe om het account aan te maken of af te maken */
    public function start(Request $request)
    {
        if (! StripeConnect::beschikbaar()) {
            return redirect()->route('dashboard', 'instellingen')
                ->with('stripeFout', 'Online betalen is nog niet ingesteld op het platform.');
        }

        try {
            return redirect()->away(StripeConnect::onboardingLink($request->user()));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('dashboard', 'instellingen')
                ->with('stripeFout', 'We konden de koppeling met Stripe niet openen. Probeer het zo nog eens.');
        }
    }

    /** Terug uit Stripe: de stand ophalen en laten zien hoe het ervoor staat */
    public function terug(Request $request)
    {
        $user = $request->user();
        StripeConnect::ververs($user);

        $melding = StripeConnect::kanOntvangen($user->fresh())
            ? 'Je bent gekoppeld aan Stripe. Betalingen komen voortaan rechtstreeks bij jou binnen.'
            : 'Stripe heeft nog niet alles van je. Maak de gegevens af, dan zetten we betalen voor je aan.';

        return redirect()->route('dashboard', 'instellingen')->with('stripeMelding', $melding);
    }

    /** De stand opnieuw ophalen zonder naar Stripe te gaan (knop in het dashboard) */
    public function status(Request $request)
    {
        $user = $request->user();
        StripeConnect::ververs($user);
        $user = $user->fresh();

        return response()->json([
            'gekoppeld' => $user->stripe_account_id !== null,
            'klaar' => StripeConnect::kanOntvangen($user),
            'gegevensNodig' => (bool) $user->stripe_gegevens_nodig,
        ]);
    }

    /** Doorklik naar het eigen Stripe-dashboard van de zaak */
    public function stripeDashboard(Request $request)
    {
        $link = StripeConnect::stripeDashboardLink($request->user());

        return $link
            ? redirect()->away($link)
            : redirect()->route('dashboard', 'instellingen')->with('stripeFout', 'Je Stripe-dashboard is even niet bereikbaar.');
    }
}
