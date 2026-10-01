<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Stripe stuurt de webhook zonder sessie of formulier-token
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);

        // De taal van de site (nl, of en als de bezoeker dat koos)
        $middleware->web(append: [\App\Http\Middleware\ZetTaal::class]);

        $middleware->alias([
            'abonnement' => \App\Http\Middleware\AbonnementActief::class,
            'medewerker' => \App\Http\Middleware\MedewerkerAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
