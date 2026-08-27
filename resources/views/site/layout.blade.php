<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titel', 'MijnPizzeria, jouw eigen bestelpagina zonder commissie 🍕')</title>
    <meta name="description" content="@yield('omschrijving', 'Zet je pizzeria in tien minuten online met een eigen bestelpagina, spaarprogramma en dozen met jouw opdruk. Zonder commissie per bestelling.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: #FFF6E8;
            --color-crema-dark: #F9EBD2;
            --color-tomato: #E63946;
            --color-tomato-dark: #B71F2E;
            --color-basil: #2F8F46;
            --color-basil-dark: #1F6A32;
            --color-gold: #F5B301;
            --color-cacao: #38221A;
            --font-display: "Lilita One", cursive;
            --font-body: "Nunito", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen antialiased">
    @include('site.deel.kop')

    @yield('inhoud')

    @include('site.deel.voet')

    <script>
        document.querySelector('#menuKnop')?.addEventListener('click', () => {
            document.querySelector('#mobielMenu').classList.toggle('hidden');
        });
    </script>
    @yield('scripts')
</body>
</html>
