<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light" class="@yield('html-klasse')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('titel', 'Shop & Eat, online bestellen voor jouw zaak zonder commissie')</title>
    <meta name="description" content="@yield('omschrijving', 'Zet je zaak in tien minuten online met een eigen bestelpagina, betalen via iDEAL en een spaarprogramma dat klanten laat terugkomen. Voor pizzeria, snackbar en elke andere keuken. Zonder commissie per bestelling.')">
    @yield('meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- De oude tokens van de blogpagina's wijzen naar het palet van designsysteem 3.0,
         zodat die pagina's meekleuren tot ze zelf herbouwd zijn --}}
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: var(--soft);
            --color-crema-dark: var(--line);
            --color-tomato: var(--accent);
            --color-tomato-dark: var(--accent);
            --color-basil: var(--ink2);
            --color-basil-dark: var(--ink);
            --color-gold: var(--ink3);
            --color-cacao: var(--ink);
            --color-white: var(--card);
            --font-display: "Figtree", sans-serif;
            --font-body: "Figtree", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/stijl/systeem.css') }}?v={{ filemtime(public_path('assets/stijl/systeem.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/site.css') }}?v={{ filemtime(public_path('assets/stijl/site.css')) }}">
    <script>document.documentElement.classList.add('js');</script>
</head>
<body>
    {{-- De wrapper voor GSAP ScrollSmoother (alleen de homepagina zet 'm aan, zie html.smooth);
         op de andere pagina's zijn het gewone divs. Wat vast op het scherm moet staan hoort
         erbuiten, in @section('buiten'). --}}
    <div id="smooth-wrapper">
        <div id="smooth-content">
            <div class="page">
                {{-- De vaste kop; de homepagina zet 'm uit, daar staat de navigatie in de hero --}}
                @section('kop')
                    @include('site.deel.kop')
                @show

                @yield('inhoud')

                {{-- De afsluiter en voet; de homepagina zet 'm uit, die is alleen de hero --}}
                @section('voet')
                    @include('site.deel.voet')
                @show
            </div>
        </div>
    </div>
    @yield('buiten')

    <script>
        (function () {
            /* Kop: schuift weg bij naar beneden scrollen, omhoog scrollen brengt hem direct terug */
            const kop = document.querySelector('#siteKop');
            const menu = document.querySelector('#mobielMenu');
            const menuKnop = document.querySelector('#menuKnop');
            if (kop) {
                let vorigeScroll = window.scrollY;
                const bijScroll = () => {
                    const y = window.scrollY;
                    kop.classList.toggle('weg', y > 140 && y > vorigeScroll && ! menu.classList.contains('open'));
                    vorigeScroll = y;
                };
                window.addEventListener('scroll', bijScroll, { passive: true });

                menuKnop.addEventListener('click', () => {
                    const open = menu.classList.toggle('open');
                    menuKnop.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => {
                    menu.classList.remove('open');
                    menuKnop.setAttribute('aria-expanded', 'false');
                }));
            }

            /* Foto's: faden in zodra ze geladen zijn, het kleurvlak eronder blijft anders staan */
            document.querySelectorAll('.photo img').forEach((img) => {
                const ok = () => img.classList.add('ok');
                const fout = () => img.classList.add('fail');
                if (img.complete) { img.naturalWidth ? ok() : fout(); }
                img.addEventListener('load', ok);
                img.addEventListener('error', fout);
            });

            /* Onthullen: een vlak krijgt .toon zodra het in beeld komt, de .rv-blokken erin volgen */
            const vlakken = [...document.querySelectorAll('.page > section')];
            if ('IntersectionObserver' in window) {
                const kijker = new IntersectionObserver((items) => {
                    items.forEach((item) => { if (item.isIntersecting) item.target.classList.add('toon'); });
                }, { threshold: .12 });
                vlakken.forEach((v) => kijker.observe(v));
            } else {
                vlakken.forEach((v) => v.classList.add('toon'));
            }
        })();
    </script>
    @yield('scripts')
</body>
</html>
