{{-- Blanco pagina in de MijnPizzeria-huisstijl voor formulieren vanuit mails --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titel', 'MijnPizzeria')</title>
    <meta name="robots" content="noindex">

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
<body class="bg-crema font-body text-cacao min-h-screen antialiased overflow-x-hidden">

    <main class="relative max-w-xl mx-auto px-5 py-10 md:py-14">
        <div class="flex items-center gap-2 mb-8">
            <i class="fa-solid fa-pizza-slice text-2xl text-tomato" aria-hidden="true"></i>
            <span class="font-display text-2xl">MijnPizzeria</span>
        </div>

        <div class="flex items-center gap-3 mb-5">
            <span class="w-10 h-10 shrink-0 rounded-full bg-white border border-crema-dark grid place-items-center text-tomato"><i class="fa-solid fa-@yield('icoon', 'pizza-slice')" aria-hidden="true"></i></span>
            <h1 class="font-display text-2xl md:text-3xl leading-tight">@yield('titel', 'MijnPizzeria')</h1>
        </div>

        <div class="dash-card rise" style="--d:.05s">
            @yield('inhoud')
        </div>

        <p class="mt-6 text-xs font-semibold text-cacao/40">MijnPizzeria, jouw eigen bestelpagina zonder commissie</p>
    </main>
</body>
</html>
