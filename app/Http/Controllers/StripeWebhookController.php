<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Support\StripeConnect;
use Illuminate\Http\Request;
use Stripe\Webhook;

/**
 * Stripe meldt hier wat er met een betaling gebeurd is. Dit is de bron van
 * waarheid: de statuspagina van de klant controleert ook zelf, maar als de klant
 * zijn browser sluit komt de bevestiging alsnog hierlangs.
 *
 * Instellen bij Stripe (Connect-webhook, luistert op alle gekoppelde accounts):
 *   checkout.session.completed, checkout.session.async_payment_succeeded,
 *   checkout.session.expired, account.updated
 * Het ondertekeningsgeheim komt in STRIPE_WEBHOOK_SECRET.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $geheim = config('services.stripe.webhook_secret');
        if (! $geheim) {
            return response()->json(['message' => 'Webhook is niet ingesteld.'], 400);
        }

        try {
            $gebeurtenis = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $geheim
            );
        } catch (\Throwable $e) {
            // Geen geldige handtekening: niet van Stripe, dus niets mee doen
            return response()->json(['message' => 'Ongeldige handtekening.'], 400);
        }

        match ($gebeurtenis->type) {
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded' => $this->betaald($gebeurtenis->data->object),
            'checkout.session.expired',
            'checkout.session.async_payment_failed' => $this->afgebroken($gebeurtenis->data->object),
            'account.updated' => $this->accountBijgewerkt($gebeurtenis->data->object),
            default => null,
        };

        return response()->json(['ok' => true]);
    }

    /** De betaling is binnen: de bestelling afronden (mails, spaarpunten, zichtbaar in het dashboard) */
    private function betaald(object $sessie): void
    {
        $order = $this->orderVan($sessie);
        if (! $order || $order->betaal_status === 'betaald') {
            return;
        }
        if (($sessie->payment_status ?? null) !== 'paid') {
            return;
        }

        $order->forceFill(['stripe_payment_intent' => (string) ($sessie->payment_intent ?? '')])->save();
        app(BestelController::class)->betalingAfronden($order);
    }

    /** De klant heeft afgehaakt of de sessie is verlopen: de bestelling blijft onzichtbaar */
    private function afgebroken(object $sessie): void
    {
        $order = $this->orderVan($sessie);
        if ($order && $order->betaal_status === 'open') {
            $order->forceFill(['betaal_status' => 'verlopen'])->save();
        }
    }

    /** Stripe is klaar met (of vraagt om) gegevens van een zaak */
    private function accountBijgewerkt(object $account): void
    {
        $user = User::where('stripe_account_id', $account->id ?? '')->first();
        if ($user) {
            StripeConnect::ververs($user);
        }
    }

    /** De bestelling bij een Checkout-sessie, ook als hij nog op betaling wacht */
    private function orderVan(object $sessie): ?Order
    {
        $id = $sessie->metadata->order_id ?? null;

        return Order::inclusiefOnbetaald()
            ->when($id, fn ($q) => $q->where('id', (int) $id), fn ($q) => $q->where('stripe_session_id', $sessie->id ?? ''))
            ->first();
    }
}
