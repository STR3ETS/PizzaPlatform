<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MijnPizzeria, binnenkort online</title>
    <meta name="description" content="MijnPizzeria komt eraan: jouw eigen bestelpagina zonder commissie per bestelling. Start alvast gratis of log in.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: #FAF8F4;
            --color-crema-dark: #E9E2D8;
            --color-tomato: #B04A3F;
            --color-tomato-dark: #93382F;
            --color-basil: #2C7A4B;
            --color-basil-dark: #1F5C38;
            --color-gold: #B97F10;
            --color-cacao: #241712;
            --font-display: "Young Serif", serif;
            --font-body: "Inter Tight", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen antialiased">
    <div class="min-h-screen flex flex-col">
        <main class="flex-1 grid place-items-center px-5 py-16">
            <div class="w-full max-w-xl text-center">
                <img src="{{ asset('logo.png') }}" alt="MijnPizzeria" class="h-28 sm:h-36 w-auto mx-auto drop-shadow-sm">

                <p class="mt-10 inline-flex items-center gap-2.5 rounded-full bg-white border border-crema-dark px-4 py-2 text-xs font-semibold uppercase tracking-wide text-cacao/60">
                    <span class="relative flex w-2 h-2">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-basil opacity-60 animate-ping"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-basil"></span>
                    </span>
                    Binnenkort online
                </p>

                <h1 class="font-display text-3xl sm:text-5xl text-cacao mt-6">Jouw pizzeria online, <span class="text-tomato">zonder commissie.</span></h1>
                <p class="mt-5 text-lg font-bold text-cacao/60">We leggen de laatste hand aan MijnPizzeria. Wil je er vanaf het begin bij zijn? Zet je zaak alvast klaar.</p>

                <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                    <a href="/onboarding" class="btn-primary !text-lg !px-8 !py-3.5">Gratis starten</a>
                    <a href="/login" class="btn-primary btn-grey !text-lg !px-8 !py-3.5">Inloggen</a>
                </div>
            </div>
        </main>

        <footer class="px-5 py-6">
            <div class="max-w-xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-semibold text-cacao/40">
                <p>&copy; {{ date('Y') }} MijnPizzeria. Alle rechten voorbehouden.</p>
                <p>{{ config('app.centraal_domein') }}</p>
            </div>
        </footer>
    </div>
</body>
</html>
