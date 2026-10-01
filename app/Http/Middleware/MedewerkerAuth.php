<?php

namespace App\Http\Middleware;

use App\Models\Medewerker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Het teamscherm heeft zijn eigen inlog, los van het dashboard van de zaak
 * (net als de klantaccounts op de bestelpagina). Bij elk verzoek kijken we of
 * het teamlid nog bestaat, nog actief is en of de zaak zelf nog toegang heeft.
 */
class MedewerkerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $medewerker = Medewerker::with('user')->find($request->session()->get('medewerker_id'));

        if (! $medewerker || ! $medewerker->actief || $medewerker->isUitgenodigd() || ! $medewerker->user?->heeftToegang()) {
            $request->session()->forget('medewerker_id');

            return $request->expectsJson()
                ? response()->json(['message' => 'Je bent uitgelogd. Log opnieuw in.'], 401)
                : redirect()->route('bezorger.login');
        }

        // Voor de views en controllers: het ingelogde teamlid en zijn zaak
        $request->attributes->set('medewerker', $medewerker);
        view()->share('medewerker', $medewerker);

        return $next($request);
    }
}
