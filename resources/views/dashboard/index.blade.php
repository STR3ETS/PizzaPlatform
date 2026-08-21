<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard 🍕</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* "static" zodat ook kleuren die alleen in style.css gebruikt worden blijven bestaan */
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
<body class="bg-crema font-body text-cacao min-h-screen antialiased overflow-x-hidden">

    <!-- Zwevende deco-emoji's -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden select-none" aria-hidden="true">
        <span class="float-emoji" style="top:14%; left:30%; --delay:0s;   --size:2.2rem;">🍕</span>
        <span class="float-emoji" style="top:75%; left:35%; --delay:1.4s; --size:1.8rem;">🍅</span>
        <span class="float-emoji" style="top:20%; left:90%; --delay:.7s;  --size:2rem;">🧀</span>
        <span class="float-emoji" style="top:70%; left:88%; --delay:2.2s; --size:2rem;">🌿</span>
    </div>

    <!-- Zwevende navigatie-rail (desktop) -->
    <aside class="dash-rail">
        <div class="flex items-center gap-2.5 px-5 py-5">
            <span class="text-3xl">🍕</span>
            <span class="font-display text-lg tracking-wide">Dashboard</span>
        </div>

        <!-- Altijd zichtbare status -->
        <button type="button" id="railStatus" data-nav="overzicht" class="mx-3 mb-3 flex items-center gap-2.5 bg-crema rounded-xl px-3 py-2 text-left cursor-pointer hover:bg-crema-dark transition-colors" title="Naar online-instellingen">
            <span id="railDot" class="status-dot" style="width:.7rem; height:.7rem;"></span>
            <span class="min-w-0">
                <span id="railStatusTitel" class="block text-sm font-extrabold leading-tight">Offline</span>
                <span id="railStatusSub" class="block text-[11px] font-extrabold text-cacao/45 leading-tight">neemt geen bestellingen aan</span>
            </span>
        </button>

        <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
            <button type="button" data-nav="overzicht" class="rail-item actief"><span class="r-ico">🏠</span> Overzicht</button>
            <button type="button" data-nav="bestellingen" class="rail-item"><span class="r-ico">🧾</span> Bestellingen</button>
            <button type="button" data-nav="menukaart" class="rail-item"><span class="r-ico">📋</span> Menukaart</button>
            <button type="button" data-nav="tijden" class="rail-item"><span class="r-ico">🕐</span> Openingstijden</button>
            <button type="button" data-nav="bestelpagina" class="rail-item"><span class="r-ico">🎨</span> Bestelpagina</button>
            <button type="button" data-nav="dozen" class="rail-item"><span class="r-ico">📦</span> Dozen <span class="n-soon">Binnenkort</span></button>
            <button type="button" data-nav="instellingen" class="rail-item"><span class="r-ico">⚙️</span> Instellingen</button>
        </nav>
        <div class="p-4 border-t-4 border-crema-dark">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-full bg-crema grid place-items-center font-display text-tomato shrink-0">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-extrabold truncate">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-extrabold text-cacao/45 hover:text-tomato transition-colors cursor-pointer">Uitloggen</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Tabbalk (mobiel) -->
    <nav class="dash-tabbar">
        <button type="button" data-nav="overzicht" class="tab-item actief"><span class="t-ico" style="position:relative">🏠<span id="tabDot" class="status-dot" style="position:absolute; top:-2px; right:-8px; width:.55rem; height:.55rem;"></span></span>Home</button>
        <button type="button" data-nav="bestellingen" class="tab-item"><span class="t-ico">🧾</span>Orders</button>
        <button type="button" data-nav="menukaart" class="tab-item"><span class="t-ico">📋</span>Menu</button>
        <button type="button" data-nav="bestelpagina" class="tab-item"><span class="t-ico">🎨</span>Pagina</button>
        <button type="button" data-nav="instellingen" class="tab-item"><span class="t-ico">⚙️</span>Meer</button>
    </nav>

    <main class="relative z-10 md:ml-[16rem] px-5 md:px-8 pt-7 pb-28 md:pb-10">
        <div class="max-w-6xl mx-auto">

        <!-- Begroeting -->
        <div class="mb-7 rise" style="--d:0s">
            <h1 class="font-display text-3xl md:text-4xl">Ciao, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p id="dagregel" class="font-extrabold text-cacao/50 mt-1">Zo staat je zaak ervoor.</p>
        </div>

        <!-- PANEEL: Overzicht -->
        <section data-panel="overzicht" class="panel actief">
            <!-- Online-status -->
            <div class="dash-card rise mb-5 flex flex-col md:flex-row md:items-center gap-4" style="--d:.03s">
                <div class="flex items-center gap-3.5 flex-1">
                    <span id="statusDot" class="status-dot"></span>
                    <div>
                        <p id="statusTitel" class="font-display text-xl leading-tight">Je bent offline</p>
                        <p id="vandaagTijden" class="text-sm font-extrabold text-cacao/50 mt-0.5">🕐 …</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div id="modeSwitch" class="mode-switch hidden">
                        <button type="button" data-mode="bezorgen_afhalen" class="mode-opt">Bezorgen &amp; afhalen</button>
                        <button type="button" data-mode="alleen_afhalen" class="mode-opt">Alleen afhalen</button>
                    </div>
                    <button id="onlineToggle" type="button" class="btn-primary !text-lg !px-8 !py-3">Online gaan 🟢</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div class="dash-card rise" style="--d:.05s">
                    <p class="lbl !ml-0">Bestellingen vandaag</p>
                    <p class="font-display text-4xl mt-1" data-teller="0">0</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">Wachten op je eerste 🤞</p>
                </div>
                <div class="dash-card rise" style="--d:.1s">
                    <p class="lbl !ml-0">Omzet vandaag</p>
                    <p class="font-display text-4xl mt-1">€ <span data-teller="0">0</span>,00</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">De teller start bij je eerste bestelling</p>
                </div>
                <div class="dash-card rise" style="--d:.15s">
                    <div class="flex items-center justify-between">
                        <p class="lbl !ml-0">Schatting drukte</p>
                        <span id="drukteBadge" class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Vandaag</span>
                    </div>
                    <div id="drukteChart" class="flex items-end gap-[3px] h-20 mt-2"></div>
                    <p id="drukteSub" class="text-xs font-extrabold text-cacao/40 mt-2">…</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Checklist met voortgangsring -->
                <div class="dash-card rise" style="--d:.2s">
                    <div class="flex items-center gap-4 mb-3">
                        <svg viewBox="0 0 64 64" class="w-16 h-16 shrink-0">
                            <circle cx="32" cy="32" r="26" stroke-width="8" fill="none" class="ring-track"/>
                            <circle id="ringBar" cx="32" cy="32" r="26" stroke-width="8" fill="none" class="ring-bar"
                                stroke-linecap="round" stroke-dasharray="163.4" stroke-dashoffset="163.4"
                                transform="rotate(-90 32 32)"/>
                            <text id="ringPct" x="32" y="37" text-anchor="middle" class="font-display" style="font-size:14px; fill:var(--color-cacao)">0%</text>
                        </svg>
                        <div>
                            <p class="font-display text-xl leading-tight">Maak je zaak compleet</p>
                            <p class="text-xs font-extrabold text-cacao/45">Tik een punt aan om 'm meteen te regelen.</p>
                        </div>
                    </div>
                    <div id="checklist" class="space-y-1"></div>
                </div>

                <!-- Snel regelen -->
                <div class="dash-card rise" style="--d:.25s">
                    <p class="font-display text-xl mb-3">Snel regelen</p>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('onboarding') }}?stap=menu" class="qa-btn"><span class="q-ico">📋</span> Menu aanpassen</a>
                        <a href="{{ route('onboarding') }}?stap=hours" class="qa-btn"><span class="q-ico">🕐</span> Tijden wijzigen</a>
                        <a href="{{ route('onboarding') }}?stap=style" class="qa-btn"><span class="q-ico">🎨</span> Pagina stylen</a>
                        <span class="qa-btn uit"><span class="q-ico">📦</span> Dozen bestellen <span class="n-soon !absolute !-top-2 !-right-2">Binnenkort</span></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- PANEEL: Bestellingen -->
        <section data-panel="bestellingen" class="panel">
            <div class="dash-card text-center py-8">
                <img src="{{ asset('stickers/showing-pizza-order.png') }}" alt="" class="h-32 mx-auto mb-3 select-none pointer-events-none">
                <p class="font-display text-2xl mb-1">Hier rollen je bestellingen binnen</p>
                <p class="text-sm font-extrabold text-cacao/50 max-w-sm mx-auto mb-5">Zodra je bestelpagina live staat, zie je hier elke bestelling realtime binnenkomen. Zo gaat dat eruitzien:</p>
                <button id="demoOrderBtn" type="button" class="btn-primary !text-lg !px-8 !py-3">Speel een voorbeeld af ▶</button>
            </div>
            <div id="demoOrders" class="mt-4 space-y-3"></div>
        </section>

        <!-- PANEEL: Menukaart -->
        <section data-panel="menukaart" class="panel">
            <div class="dash-card flex flex-col sm:flex-row items-center gap-5">
                <img src="{{ asset('stickers/sprinkling-cheese.png') }}" alt="" class="h-32 select-none pointer-events-none">
                <div class="flex-1 text-center sm:text-left">
                    <p class="font-display text-2xl mb-1">Jouw menukaart</p>
                    <p id="menuSamenvatting" class="text-sm font-extrabold text-cacao/50 mb-4">Je hebt nog geen gerechten toegevoegd.</p>
                    <a href="{{ route('onboarding') }}?stap=menu" class="btn-primary inline-block !text-lg !px-8 !py-3">Menukaart bewerken</a>
                </div>
            </div>
        </section>

        <!-- PANEEL: Openingstijden -->
        <section data-panel="tijden" class="panel">
            <div class="dash-card flex flex-col sm:flex-row items-center gap-5">
                <img src="{{ asset('stickers/pizza-in-oven.png') }}" alt="" class="h-32 select-none pointer-events-none">
                <div class="flex-1 text-center sm:text-left">
                    <p class="font-display text-2xl mb-1">Openingstijden</p>
                    <p id="tijdenSamenvatting" class="text-sm font-extrabold text-cacao/50 mb-4">Nog niet ingesteld.</p>
                    <a href="{{ route('onboarding') }}?stap=hours" class="btn-primary inline-block !text-lg !px-8 !py-3">Tijden aanpassen</a>
                </div>
            </div>
        </section>

        <!-- PANEEL: Bestelpagina -->
        <section data-panel="bestelpagina" class="panel">
            <div class="dash-card flex flex-col sm:flex-row items-center gap-5">
                <img src="{{ asset('stickers/behind-laptop.png') }}" alt="" class="h-32 select-none pointer-events-none">
                <div class="flex-1 text-center sm:text-left">
                    <p class="font-display text-2xl mb-1">Jouw bestelpagina</p>
                    <p id="paginaSamenvatting" class="text-sm font-extrabold text-cacao/50 mb-4">Kies je template, kleur en logo.</p>
                    <a href="{{ route('onboarding') }}?stap=style" class="btn-primary inline-block !text-lg !px-8 !py-3">Pagina stylen</a>
                </div>
            </div>
        </section>

        <!-- PANEEL: Dozen -->
        <section data-panel="dozen" class="panel">
            <div class="dash-card text-center py-8">
                <img src="{{ asset('stickers/holding-3-pizzas.png') }}" alt="" class="h-32 mx-auto mb-3 select-none pointer-events-none">
                <p class="font-display text-2xl mb-1">Dozen bijbestellen</p>
                <p class="text-sm font-extrabold text-cacao/50 max-w-sm mx-auto">Straks bestel je hier met één tik nieuwe pizzadozen, inclusief jouw eigen opdruk. Nog even geduld! 📦</p>
            </div>
        </section>

        <!-- PANEEL: Instellingen -->
        <section data-panel="instellingen" class="panel">
            <div class="dash-card max-w-lg">
                <p class="font-display text-2xl mb-4">Instellingen</p>
                <div class="space-y-3">
                    <div>
                        <p class="lbl !ml-0">Naam</p>
                        <p class="font-extrabold">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="lbl !ml-0">E-mailadres</p>
                        <p class="font-extrabold">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="lbl !ml-0">Hulp</p>
                        <button id="introOpnieuw" type="button" class="font-extrabold text-tomato hover:underline cursor-pointer">🎬 Bekijk de uitlegvideo nog eens</button>
                    </div>
                    <p class="text-xs font-extrabold text-cacao/40">Meer instellingen volgen binnenkort.</p>
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="btn-primary btn-grey !text-lg !px-8 !py-3">Uitloggen</button>
                    </form>
                </div>
            </div>
        </section>
        </div>
    </main>

    <!-- Tip-mascotte -->
    <div id="tipMascotte" class="tip-mascotte" title="Klik voor een nieuwe tip">
        <div class="bubble" id="tipBubble">Ciao! Klik op mij voor tips 👋</div>
        <img src="{{ asset('stickers/waving-hello.png') }}" alt="">
    </div>

    <!-- Introvideo bij je eerste bezoek -->
    <div id="introModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-cacao/60 backdrop-blur-sm p-5">
        <div class="bg-white rounded-3xl border-4 border-crema-dark shadow-2xl max-w-2xl w-full p-6 sm:p-8 text-center animate-pop">
            <p class="step-kicker">Benvenuto! 🎉</p>
            <h2 class="font-display text-3xl mb-2">Zo werkt jouw dashboard</h2>
            <p class="text-sm font-extrabold text-cacao/50 max-w-md mx-auto mb-5">In een paar minuten leggen we je uit hoe je bestellingen ontvangt, je menukaart beheert en je pagina live zet.</p>

            <!-- Placeholder: hier komt straks de echte uitlegvideo -->
            <div class="relative aspect-video rounded-2xl overflow-hidden bg-cacao grid place-items-center mb-6">
                <div class="text-center">
                    <button type="button" class="w-16 h-16 rounded-full bg-tomato text-white text-2xl grid place-items-center mx-auto mb-3 shadow-[0_5px_0_0_var(--color-tomato-dark)] hover:translate-y-0.5 hover:shadow-[0_3px_0_0_var(--color-tomato-dark)] transition-all cursor-pointer">▶</button>
                    <p class="text-crema/70 text-sm font-extrabold">De uitlegvideo komt hier binnenkort 🎬</p>
                </div>
            </div>

            <button id="introSluiten" type="button" class="btn-primary !text-xl !px-10 !py-4">Aan de slag 🚀</button>
            <p class="mt-3 text-xs font-extrabold text-cacao/40">Later terugkijken? Je vindt 'm bij Instellingen.</p>
        </div>
    </div>

    <script>window.PP_DATA = @json(auth()->user()->onboarding ?? new stdClass);</script>
    <script>window.PP_INTRO = @json(! auth()->user()->intro_seen);</script>
    <script>window.PP_STATUS = @json(['online' => (bool) auth()->user()->is_online, 'mode' => auth()->user()->order_mode ?? 'bezorgen_afhalen']);</script>
    <script src="{{ asset('assets/dashboard.js') }}?v={{ filemtime(public_path('assets/dashboard.js')) }}"></script>
</body>
</html>
