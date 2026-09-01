<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard-acties zijn pas bruikbaar met een actief abonnement. De overlay
 * in het dashboard is alleen de voorkant; wie die via devtools weghaalt,
 * loopt hier alsnog vast.
 */
class AbonnementActief
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ! $user->is_admin && ! $user->heeftToegang()) {
            return response()->json([
                'message' => $user->proef_tot?->isPast()
                    ? 'Je proefperiode is voorbij. Start je abonnement om verder te gaan.'
                    : 'Start eerst je abonnement om het dashboard te gebruiken.',
            ], 402);
        }

        return $next($request);
    }
}
