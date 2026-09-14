<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titel', 'MijnPizzeria, jouw eigen bestelpagina zonder commissie')</title>
    <meta name="description" content="@yield('omschrijving', 'Zet je pizzeria in tien minuten online met een eigen bestelpagina, spaarprogramma en dozen met jouw opdruk. Zonder commissie per bestelling.')">
    @yield('meta')

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
    @include('site.deel.kop')

    @yield('inhoud')

    @include('site.deel.voet')

    <script>
        /* Kop: wit zodra je van de pizza af scrolt (homepagina), en schuift weg bij
           naar beneden scrollen; omhoog scrollen brengt hem direct terug */
        const siteKop = document.querySelector('#siteKop');
        if (siteKop) {
            const doorzichtig = siteKop.classList.contains('site-kop-doorzichtig');
            let vorigeScroll = window.scrollY;
            const bijScroll = () => {
                const y = window.scrollY;
                if (doorzichtig) siteKop.classList.toggle('vast', y > 16);
                const menuOpen = ! (document.querySelector('#mobielMenu')?.classList.contains('hidden') ?? true);
                siteKop.classList.toggle('weg', y > 140 && y > vorigeScroll && ! menuOpen);
                vorigeScroll = y;
            };
            window.addEventListener('scroll', bijScroll, { passive: true });
            bijScroll();
        }

        const menuKnop = document.querySelector('#menuKnop');
        menuKnop?.addEventListener('click', () => {
            const menu = document.querySelector('#mobielMenu');
            menu.classList.toggle('hidden');
            menuKnop.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
        });
    </script>
    @yield('scripts')
</body>
</html>
