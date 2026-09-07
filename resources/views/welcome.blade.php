<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zet jouw pizzeria online</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* "static" zodat ook kleuren die alleen in style.css gebruikt worden blijven bestaan */
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
<body class="bg-crema font-body text-cacao min-h-screen overflow-x-hidden antialiased">

    <main class="relative z-10 min-h-screen flex items-center justify-center px-5 py-16">
        <div class="w-full max-w-2xl text-center">
            <h1 class="font-display text-5xl sm:text-6xl leading-tight mb-5">
                Zet jouw pizzeria <span class="text-tomato">online</span>
            </h1>
            <p class="text-xl font-bold text-cacao/70 max-w-lg mx-auto mb-3">
                Zonder gedoe. Zonder commissies. Gewoon jouw eigen bestelpagina.
            </p>
            <p class="text-base font-bold text-cacao/50 max-w-md mx-auto mb-10">
                Alles wat je invult kun je later nog aanpassen, dus niks moet perfect.
            </p>

            <div class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary text-2xl px-12 py-5 inline-block">
                        Naar mijn dashboard
                    </a>
                @else
                    <a href="{{ route('onboarding') }}" class="btn-primary text-2xl px-12 py-5 inline-block">
                        Gratis starten
                    </a>
                    <a href="{{ route('login') }}" class="btn-primary btn-grey text-2xl px-12 py-5 inline-block">
                        Inloggen
                    </a>
                @endauth
            </div>

            <p class="mt-6 text-sm font-semibold text-cacao/40 flex items-center justify-center gap-6"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> ± 5 minuten</span><span><i class="fa-solid fa-check" aria-hidden="true"></i> vrijblijvend</span></p>
        </div>
    </main>

</body>
</html>
