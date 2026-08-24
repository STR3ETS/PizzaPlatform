<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titel', $naam . ($plaats ? ' | Pizza bestellen in ' . $plaats : ''))</title>
    <meta name="description" content="@yield('omschrijving', 'Bestel direct online bij ' . $naam . ($plaats ? ' in ' . $plaats : '') . '. Vers bereid, bezorgen of afhalen, zonder omweg.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900{{ $thema['font'] ? '&' . $thema['font'] : '' }}&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        :root {
            --accent: {{ $accent }};
            --pagina: {{ $thema['pagina'] }};
            --kaart: {{ $thema['kaart'] }};
            --tekst: {{ $thema['tekst'] }};
            --prijs: {{ $thema['prijs'] }};
            --radius: {{ $thema['radius'] }};
            --knop-radius: {{ $thema['knopRadius'] }};
            --accent-zacht: color-mix(in srgb, {{ $accent }} 12%, transparent);
        }
        html { scroll-behavior: smooth; }
        body { background: var(--pagina); color: var(--tekst); font-family: 'Nunito', sans-serif; font-weight: 700; }
        .b-kop { font-family: {!! $thema['kopFont'] !!}; @if($thema['upper']) text-transform: uppercase; letter-spacing: .02em; @endif font-weight: 700; }
        .b-kaart { background: var(--kaart); border-radius: var(--radius); @if($thema['rand'] !== 'none') border: {{ $thema['rand'] }}; @endif }
        .b-knop {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            background: var(--accent); color: #fff; border-radius: var(--knop-radius);
            font-weight: 900; padding: .8rem 1.6rem; cursor: pointer; border: 0; font-family: inherit; font-size: 1rem;
            transition: filter .15s ease, transform .1s ease, box-shadow .15s ease;
        }
        .b-knop:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .b-knop:active { transform: scale(.98); }
        .b-knop[disabled] { opacity: .45; cursor: not-allowed; }
        .b-knop.stil { background: transparent; color: var(--tekst); border: 2px solid currentColor; }
        .b-prijs { color: var(--prijs); }
        .b-chip {
            display: inline-flex; align-items: center; gap: .4rem; cursor: pointer;
            border: 2px solid var(--prijs); color: var(--tekst); background: transparent;
            border-radius: var(--knop-radius); padding: .4rem .9rem; font-weight: 800; font-size: .85rem; font-family: inherit;
            transition: all .12s ease;
        }
        .b-chip.aan { background: var(--accent); border-color: var(--accent); color: #fff; }
        .b-veld {
            width: 100%; background: var(--kaart); color: var(--tekst);
            border: 2px solid var(--prijs); border-radius: var(--radius);
            padding: .65rem .9rem; font: inherit; font-weight: 700; outline: none;
        }
        .b-veld:focus { border-color: var(--accent); }
        .b-veld.fout { border-color: #E63946; }

        /* Navigatie */
        .b-nav { position: sticky; top: 0; z-index: 30; background: var(--kaart); box-shadow: 0 2px 18px rgb(0 0 0 / .07); }
        .b-nav-link { font-weight: 800; opacity: .7; transition: opacity .12s ease, color .12s ease; }
        .b-nav-link:hover { opacity: 1; }
        .b-nav-link.actief { opacity: 1; color: var(--accent); }

        /* Hero */
        .b-hero { position: relative; overflow: hidden; }
        .b-hero-blob {
            position: absolute; right: -12rem; top: -10rem; width: 42rem; height: 42rem; border-radius: 50%;
            background: radial-gradient(closest-side, var(--accent-zacht), transparent 70%);
            pointer-events: none;
        }
        .b-bord {
            width: min(21rem, 72vw); aspect-ratio: 1; border-radius: 50%; margin: 0 auto;
            background: radial-gradient(circle at 35% 28%, #fff8ea, #f1dfb8 62%, #e2c68f);
            box-shadow: inset 0 -14px 28px rgb(0 0 0 / .12), 0 30px 45px -22px rgb(0 0 0 / .4);
            display: grid; place-items: center; font-size: clamp(6rem, 18vw, 9.5rem); line-height: 1;
        }
        .b-zweef { position: absolute; font-size: 2rem; animation: b-zweef 6s ease-in-out infinite; filter: drop-shadow(0 6px 6px rgb(0 0 0 / .15)); }
        @keyframes b-zweef {
            0%, 100% { transform: translateY(0) rotate(-6deg); }
            50% { transform: translateY(-16px) rotate(8deg); }
        }

        /* Foto's */
        .b-foto { width: 100%; height: 100%; object-fit: cover; display: block; }
        .b-fotobord {
            width: min(23rem, 78vw); aspect-ratio: 1; border-radius: 50%; overflow: hidden; margin: 0 auto;
            box-shadow: 0 35px 60px -25px rgb(0 0 0 / .5);
        }
        .b-fotovak { border-radius: calc(var(--radius)); overflow: hidden; }
        body[data-thema="blocco"] .b-fotobord { border-radius: 0; border: 2px solid #111; box-shadow: 8px 8px 0 0 #111; }
        body[data-thema="blocco"] .b-fotovak { border: 2px solid #111; border-radius: 0; }

        /* Gerecht-kaarten */
        .b-gerecht { transition: transform .18s ease, box-shadow .18s ease; }
        .b-gerecht:hover { transform: translateY(-4px); box-shadow: 0 18px 30px -18px rgb(0 0 0 / .35); }
        .b-bordje {
            width: 5.5rem; aspect-ratio: 1; border-radius: 50%;
            background: radial-gradient(circle at 35% 28%, #fff8ea, #f1dfb8 62%, #e2c68f);
            box-shadow: inset 0 -6px 12px rgb(0 0 0 / .12), 0 12px 18px -10px rgb(0 0 0 / .35);
            display: grid; place-items: center; font-size: 2.6rem; line-height: 1;
        }
        .b-allergeen { font-size: .72rem; color: var(--prijs); }

        /* Accentband en marquee */
        .b-band { background: var(--accent); color: #fff; border-radius: var(--radius); }
        .b-marquee { display: none; overflow: hidden; white-space: nowrap; background: #111; color: #fff; }
        .b-marquee > div { display: inline-block; padding: .55rem 0; font-weight: 900; text-transform: uppercase; font-size: .8rem; letter-spacing: .12em; animation: b-marquee 22s linear infinite; }
        @keyframes b-marquee { to { transform: translateX(-50%); } }

        /* Shop-layout op de menukaart */
        :root { --plak-top: 3.55rem; }
        body[data-thema="nero"] { --plak-top: 6.4rem; }
        .m-held { position: relative; min-height: 17rem; display: flex; align-items: flex-end; overflow: hidden; }
        .m-held-tint { position: absolute; inset: 0; background: linear-gradient(to top, rgb(0 0 0 / .72), rgb(0 0 0 / .18)); }
        body[data-thema="blocco"] .m-held-tint { background: linear-gradient(to top, rgb(0 0 0 / .82), rgb(0 0 0 / .28)); }
        .m-logo {
            width: 4.6rem; height: 4.6rem; border-radius: var(--radius); overflow: hidden; flex-shrink: 0;
            background: var(--kaart); display: grid; place-items: center;
            box-shadow: 0 10px 25px -10px rgb(0 0 0 / .5);
        }
        body[data-thema="blocco"] .m-logo { border: 2px solid #111; }
        .m-plakbalk {
            position: sticky; top: var(--plak-top); z-index: 20;
            background: var(--kaart); box-shadow: 0 6px 18px rgb(0 0 0 / .08);
        }
        .m-tabs { overflow-x: auto; scrollbar-width: none; }
        .m-tabs::-webkit-scrollbar { display: none; }
        .m-tabs a { white-space: nowrap; }
        .m-typetoggle { background: color-mix(in srgb, var(--tekst) 8%, transparent); border-radius: var(--knop-radius); }
        .m-typetoggle button {
            padding: .5rem .5rem; font-weight: 800; font-size: .8rem; border-radius: var(--knop-radius);
            border: 0; cursor: pointer; background: transparent; color: var(--tekst); font-family: inherit; opacity: .65;
        }
        .m-typetoggle button.aan { background: var(--accent); color: #fff; opacity: 1; }
        .m-uitgelicht { display: grid; grid-template-columns: repeat(3, 1fr); gap: .9rem; }
        @media (max-width: 640px) { .m-uitgelicht { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; } .m-uitgelicht > * { min-width: 15rem; scroll-snap-align: start; } }

        /* Sheets en mandje */
        .b-sheet-achtergrond { position: fixed; inset: 0; background: rgb(0 0 0 / .55); z-index: 40; }
        .b-sheet {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 50;
            max-width: 34rem; margin: 0 auto; max-height: 88vh; overflow-y: auto;
            background: var(--kaart); color: var(--tekst);
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 1.25rem 1.25rem 1.5rem;
            animation: sheet-in .25s ease both;
        }
        @media (min-width: 640px) { .b-sheet { bottom: 2rem; border-radius: var(--radius); } }
        @keyframes sheet-in { from { transform: translateY(30px); opacity: 0; } to { transform: none; opacity: 1; } }
        .b-mandbalk { position: fixed; left: 1rem; right: 1rem; bottom: 1rem; z-index: 30; max-width: 32rem; margin: 0 auto; }

        /* ── Themavarianten ── */
        body[data-thema="nero"] .b-bord,
        body[data-thema="nero"] .b-bordje { background: radial-gradient(circle at 35% 28%, #4a3a2c, #2b2119 62%, #1d1712); box-shadow: inset 0 -10px 22px rgb(0 0 0 / .5), 0 30px 45px -22px rgb(0 0 0 / .8); }
        body[data-thema="nero"] .b-nav { box-shadow: 0 2px 18px rgb(0 0 0 / .5); }
        body[data-thema="nero"] .b-kop { letter-spacing: .04em; }

        body[data-thema="napoli"] .b-nav { border-bottom: 6px solid; border-image: repeating-linear-gradient(90deg, #C0392B 0 3rem, #FFFDF6 3rem 6rem, #2F8F46 6rem 9rem) 6; }

        body[data-thema="puro"] .b-kaart { border: 1px solid rgb(0 0 0 / .07); }
        body[data-thema="puro"] .b-gerecht:hover { box-shadow: 0 14px 24px -18px rgb(0 0 0 / .25); }

        body[data-thema="blocco"] .b-marquee { display: block; }
        body[data-thema="blocco"] .b-kaart,
        body[data-thema="blocco"] .b-veld { border: 2px solid #111 !important; box-shadow: 4px 4px 0 0 #111; }
        body[data-thema="blocco"] .b-knop { border: 2px solid #111; box-shadow: 4px 4px 0 0 #111; }
        body[data-thema="blocco"] .b-knop:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 0 #111; filter: none; }
        body[data-thema="blocco"] .b-bord, body[data-thema="blocco"] .b-bordje { border: 2px solid #111; box-shadow: 6px 6px 0 0 #111; }

        body[data-thema="retro"] .b-hero::before {
            content: ""; position: absolute; inset: -40% -20%;
            background: repeating-conic-gradient(from 0deg at 75% 40%, var(--accent-zacht) 0deg 8deg, transparent 8deg 20deg);
            pointer-events: none;
        }
        body[data-thema="retro"] .b-kaart { box-shadow: 0 6px 0 0 rgb(91 58 33 / .18); }
    </style>

    <script type="application/ld+json">@json($jsonld)</script>
    @yield('extra-jsonld')
</head>
<body class="min-h-screen antialiased" data-thema="{{ $themaId }}">

    <div class="b-marquee" aria-hidden="true"><div>
        @for($i = 0; $i < 2; $i++)
            Vers uit de oven ★ {{ $alleenAfhalen ? 'Snel afhalen' : 'Bezorgen en afhalen' }} ★ Direct bij de zaak bestellen ★ {{ $naam }} ★&nbsp;
        @endfor
    </div></div>

    @if($themaId === 'nero')
    <!-- Klassieke restaurant-navigatie: gecentreerd -->
    <nav class="b-nav">
        <div class="max-w-5xl mx-auto px-4 pt-4 pb-3 text-center">
            <a href="{{ route('bestel.home', $slug) }}" class="inline-flex items-center gap-2.5">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-9 w-9 rounded-full object-cover">
                @endif
                <span class="b-kop text-2xl">{{ $naam }}</span>
            </a>
            <div class="flex items-center justify-center gap-6 mt-2.5 text-sm">
                <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
                <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">De kaart</a>
                <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
                <span class="text-xs font-extrabold rounded-full px-3 py-1" style="background: {{ $online ? '#2F8F46' : 'rgb(120 120 120 / .35)' }}; color: #fff">{{ $online ? '● Open' : '● Gesloten' }}</span>
                <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-1.5 !px-4 !text-sm">Bestel nu</a>
            </div>
        </div>
    </nav>
    @else
    <nav class="b-nav">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-4">
            <a href="{{ route('bestel.home', $slug) }}" class="flex items-center gap-2.5 min-w-0">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-10 w-10 rounded-full object-cover shrink-0">
                @else
                    <span class="text-2xl">🍕</span>
                @endif
                <span class="b-kop text-xl truncate">{{ $naam }}</span>
            </a>
            <div class="flex-1"></div>
            <div class="hidden sm:flex items-center gap-5 text-sm mr-2">
                <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
                <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">Menukaart</a>
                <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
            </div>
            <span class="hidden md:inline-flex shrink-0 text-xs font-extrabold rounded-full px-3 py-1" style="background: {{ $online ? '#2F8F46' : 'rgb(120 120 120 / .35)' }}; color: #fff">{{ $online ? '● Open' : '● Gesloten' }}</span>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-4 !text-sm shrink-0">Bestel nu</a>
        </div>
        <div class="sm:hidden flex gap-5 px-4 pb-2.5 text-sm">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">Menukaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
        </div>
    </nav>
    @endif

    @unless($online)
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="b-kaart px-4 py-3 text-sm font-extrabold" style="border-left: 5px solid var(--accent)">
                We nemen op dit moment geen bestellingen aan.
                @if($vandaag['open']) Vandaag zijn we open van {{ $vandaag['van'] }} tot {{ $vandaag['tot'] }}. @endif
                Kijken op de menukaart kan altijd!
            </div>
        </div>
    @endunless

    @yield('inhoud')

    <footer class="mt-14" style="background: var(--kaart)">
        <div class="max-w-5xl mx-auto px-4 py-10 grid sm:grid-cols-3 gap-8 text-sm">
            <div>
                <p class="b-kop text-xl mb-2">{{ $naam }}</p>
                <p class="opacity-70">Vers bereide pizza's{{ $plaats ? ' in ' . $plaats : '' }}. Direct bij ons besteld, zodat je bestelling meteen in onze keuken ligt.</p>
            </div>
            <div>
                <p class="b-kop text-lg mb-2">Openingstijden</p>
                @foreach($dagen as $dag)
                    <div class="flex justify-between gap-4 {{ $dag['open'] ? 'opacity-80' : 'opacity-50' }}">
                        <span>{{ $dag['naam'] }}</span>
                        <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                    </div>
                @endforeach
            </div>
            <div>
                <p class="b-kop text-lg mb-2">Contact</p>
                @if($adres)<p class="opacity-80">{{ $adres }}</p>@endif
                @if($telefoon)<p class="mt-1"><a href="tel:{{ $telefoon }}" class="underline underline-offset-2 opacity-80">{{ $telefoon }}</a></p>@endif
                <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-4 !text-sm mt-4">Online bestellen</a>
            </div>
        </div>
        <div class="border-t px-4 py-4 text-center text-xs opacity-55" style="border-color: color-mix(in srgb, var(--tekst) 12%, transparent)">
            &copy; {{ date('Y') }} {{ $naam }}. Alle rechten voorbehouden.
        </div>
    </footer>

    <!-- GSAP: subtiele scroll-animaties, per template een eigen karakter -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        if (window.gsap && window.ScrollTrigger) {
            gsap.registerPlugin(ScrollTrigger);
            const karakter = ({
                blocco: { x: -28, y: 0, duration: .38, ease: 'back.out(1.7)' },
                nero: { x: 0, y: 36, duration: 1.05, ease: 'power3.out' },
                retro: { x: 0, y: 26, rotation: -2, duration: .7, ease: 'back.out(1.4)' },
                napoli: { x: 0, y: 20, duration: .8, ease: 'power2.out' },
                puro: { x: 0, y: 24, duration: .7, ease: 'power2.out' },
            })[document.body.dataset.thema] || { x: 0, y: 26, duration: .6, ease: 'power2.out' };
            document.querySelectorAll('[data-anim]').forEach((el) => {
                gsap.from(el, { ...karakter, opacity: 0, scrollTrigger: { trigger: el, start: 'top 90%', once: true } });
            });
            const held = document.querySelector('[data-parallax]');
            if (held) {
                gsap.to(held, { yPercent: 16, ease: 'none', scrollTrigger: { trigger: held.closest('section'), start: 'top top', end: 'bottom top', scrub: true } });
            }
        }
    </script>

    <!-- Live aftellen tot sluitingstijd (gebruikt in de offer-secties) -->
    <script>
        (function () {
            const vakken = document.querySelectorAll('[data-aftellen]');
            if (!vakken.length) return;
            const tik = () => {
                vakken.forEach((vak) => {
                    if (vak.dataset.open !== '1') { vak.textContent = 'vandaag gesloten'; return; }
                    const [u, m] = vak.dataset.tot.split(':').map(Number);
                    const sluit = new Date();
                    sluit.setHours(u, m, 0, 0);
                    const over = sluit - Date.now();
                    if (over <= 0) { vak.textContent = 'vandaag gesloten'; return; }
                    const uren = Math.floor(over / 3600000);
                    const minuten = Math.floor((over % 3600000) / 60000);
                    const seconden = Math.floor((over % 60000) / 1000);
                    vak.textContent = `${String(uren).padStart(2, '0')}:${String(minuten).padStart(2, '0')}:${String(seconden).padStart(2, '0')}`;
                });
            };
            tik();
            setInterval(tik, 1000);
        })();
    </script>

    @yield('scripts')
</body>
</html>
