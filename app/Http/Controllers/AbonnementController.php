<?php

namespace App\Http\Controllers;

use App\Mail\AbonnementGestartMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

/**
 * Het abonnement (24,95 per maand) via Stripe Checkout. Zonder Stripe-config
 * activeren we direct, zodat de flow lokaal altijd te testen blijft.
 */
class AbonnementController extends Controller
{
    public function starten(Request $request)
    {
        $user = $request->user();
        if ($user->abonnement_actief) {
            return redirect()->route('dashboard');
        }

        if (! config('services.stripe.secret')) {
            return $this->activeer($request, null, null);
        }

        $params = [
            'mode' => 'subscription',
            'locale' => 'nl',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 2495,
                    'recurring' => ['interval' => 'month'],
                    'product_data' => [
                        'name' => 'MijnPizzeria abonnement',
                        'description' => 'Eigen bestelpagina zonder commissie, opzegbaar per maand',
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => ['user_id' => (string) $user->id],
            'subscription_data' => ['metadata' => ['user_id' => (string) $user->id]],
            'success_url' => route('abonnement.klaar') . '?sessie={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('dashboard'),
        ];
        // Een bekende Stripe-klant hergebruiken we, anders vullen we alvast het e-mailadres in
        if ($user->stripe_customer_id) {
            $params['customer'] = $user->stripe_customer_id;
        } else {
            $params['customer_email'] = $user->email;
        }

        try {
            $sessie = \App\Support\StripeConnect::stil(fn () => (new StripeClient(config('services.stripe.secret')))->checkout->sessions->create($params));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('dashboard')->with('abonnementFout', 'Betalen lukte even niet. Probeer het zo nog eens.');
        }

        return redirect()->away($sessie->url);
    }

    /** Terug van Stripe: de sessie serverzijdig controleren en dan pas activeren */
    public function klaar(Request $request)
    {
        $user = $request->user();
        $sessieId = (string) $request->query('sessie');
        if ($sessieId === '' || ! config('services.stripe.secret') || $user->abonnement_actief) {
            return redirect()->route('dashboard');
        }

        try {
            $sessie = \App\Support\StripeConnect::stil(fn () => (new StripeClient(config('services.stripe.secret')))->checkout->sessions->retrieve($sessieId));
            $klopt = ($sessie->metadata->user_id ?? null) === (string) $user->id
                && $sessie->status === 'complete'
                && in_array($sessie->payment_status, ['paid', 'no_payment_required'], true);
            if (! $klopt) {
                return redirect()->route('dashboard')->with('abonnementFout', 'De betaling is niet afgerond. Probeer het nog eens.');
            }

            return $this->activeer($request, (string) $sessie->customer, (string) $sessie->subscription);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('dashboard')->with('abonnementFout', 'We konden je betaling niet controleren. Probeer het nog eens.');
        }
    }

    private function activeer(Request $request, ?string $stripeKlant, ?string $stripeAbonnement)
    {
        $user = $request->user();
        $user->forceFill([
            'abonnement_actief' => true,
            'abonnement_sinds' => now(),
            'stripe_customer_id' => $stripeKlant ?: $user->stripe_customer_id,
            'stripe_subscription_id' => $stripeAbonnement ?: $user->stripe_subscription_id,
        ])->save();

        try {
            Mail::to($user->email)->send(new AbonnementGestartMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('dashboard')->with('abonnementGestart', true);
    }
}
