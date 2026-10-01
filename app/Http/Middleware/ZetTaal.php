<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/* De taal van de site: Nederlands, tenzij de bezoeker via /taal/{taal} iets anders koos.
   Alleen de homepagina is vertaald; de rest van het platform is Nederlands en gebruikt geen __(). */
class ZetTaal
{
    public const TALEN = ['nl', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $taal = $request->session()->get('taal', 'nl');
        app()->setLocale(in_array($taal, self::TALEN, true) ? $taal : 'nl');

        return $next($request);
    }
}
