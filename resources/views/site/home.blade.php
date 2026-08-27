@extends('site.layout')

@section('inhoud')
    {{-- Hero: de belofte links, het echte product rechts --}}
    <section class="relative overflow-hidden isolate">
        {{-- Zwevende deco --}}
        <div class="pointer-events-none absolute inset-0 select-none" aria-hidden="true">
            <span class="float-emoji" style="top:12%; left:4%;  --delay:0s;   --size:2rem;">🍕</span>
            <span class="float-emoji" style="top:70%; left:7%;  --delay:1.6s; --size:1.6rem;">🍅</span>
            <span class="float-emoji" style="top:18%; left:94%; --delay:.8s;  --size:1.9rem;">🧀</span>
            <span class="float-emoji" style="top:80%; left:90%; --delay:2.4s; --size:1.7rem;">🌿</span>
        </div>

        <div class="max-w-6xl mx-auto px-5 pb-24 text-center">
            <h1 class="sr-only">Jouw eigen bestelpagina. Zonder commissie.</h1>
            <div aria-hidden="true" class="relative mx-auto max-w-5xl mt-6 select-none">
                {{-- Mascottes met spraakwolk, zoals in de onboarding; alleen op brede schermen --}}
                <div class="mascot mascot-left !hidden min-[95rem]:!flex pt-48">
                    <div class="bubble">Ciao! Zet je zaak vandaag nog online 🍕</div>
                    <img src="{{ asset('stickers/tossing-dough.png') }}" alt="" loading="lazy">
                </div>
                <div class="mascot mascot-right !hidden min-[95rem]:!flex pt-48">
                    <div class="bubble">Elke bestelling is van jou 😎</div>
                    <img src="{{ asset('stickers/holding-3-pizzas.png') }}" alt="" loading="lazy">
                </div>
                {{-- Regel 1 rustig in cacao, met de gouden streep onder "online"; alleen de kernbelofte is een sticker --}}
                <p class="font-display text-4xl sm:text-6xl text-cacao pt-6">Jouw pizzeria <span class="relative inline-block">online<span class="kop-streep" aria-hidden="true"></span></span>,</p>
                <svg viewBox="0 0 1200 220" class="w-full -m-4">
                    <defs>
                        <path id="heroBoog2" d="M 10 170 Q 600 76 1190 170" fill="none"/>
                    </defs>
                    <g style="font-family:var(--font-display); font-size:110px; filter:drop-shadow(0 6px 10px rgb(56 34 26 / .18))" stroke-linejoin="round">
                        <text stroke="#FFFFFF" stroke-width="26" fill="#FFFFFF"><textPath href="#heroBoog2" startOffset="50%" text-anchor="middle">Zonder commissie.</textPath></text>
                        <text stroke="var(--color-cacao)" stroke-width="12" fill="var(--color-cacao)"><textPath href="#heroBoog2" startOffset="50%" text-anchor="middle">Zonder commissie.</textPath></text>
                        <text fill="var(--color-tomato)"><textPath href="#heroBoog2" startOffset="50%" text-anchor="middle">Zonder commissie.</textPath></text>
                    </g>
                </svg>
            </div>

            <p class="text-lg font-bold text-cacao/60 max-w-2xl mx-auto">
                Binnen tien minuten online: je menukaart, betalingen en een spaarprogramma dat klanten laat terugkomen. Elke bestelling is van jou, elke euro ook.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="/onboarding" class="btn-primary !text-lg !px-8 !py-3.5">Gratis starten 🍕</a>
                <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="text-base font-extrabold text-cacao/60 underline underline-offset-4 decoration-cacao/25 hover:text-cacao transition-colors">Bekijk een echte bestelpagina</a>
            </div>
            <ul class="mt-7 flex flex-wrap items-center justify-center gap-x-7 gap-y-2.5 text-sm font-extrabold text-cacao/60">
                <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/15 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Binnen 10 minuten online</li>
                <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/15 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Geen commissie per bestelling</li>
                <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/15 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Maandelijks opzegbaar</li>
            </ul>

            {{-- Het echte product op volle breedte: zien is geloven --}}
            <div class="relative mt-24 text-left">
                {{-- De witte band van de functies begint halverwege de preview, erachter --}}
                <div class="absolute top-1/2 -bottom-24 left-1/2 -translate-x-1/2 w-screen -z-10" aria-hidden="true">
                    <svg class="w-full h-10 sm:h-14 block" viewBox="0 0 1440 64" preserveAspectRatio="none"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 64 L 0 64 Z" fill="#FFFFFF"/></svg>
                    <div class="absolute left-0 right-0 top-[calc(2.5rem-1px)] sm:top-[calc(3.5rem-1px)] bottom-0 bg-white"></div>
                </div>
                <div class="rounded-3xl bg-white border-4 border-crema-dark shadow-2xl overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3.5 border-b-2 border-crema-dark" style="background:var(--color-crema)">
                        <span class="w-3 h-3 rounded-full bg-tomato/70"></span>
                        <span class="w-3 h-3 rounded-full bg-gold/80"></span>
                        <span class="w-3 h-3 rounded-full bg-basil/70"></span>
                        <span class="mx-auto w-full max-w-sm rounded-full bg-white border-2 border-crema-dark px-4 py-1 text-xs font-extrabold text-cacao/45 text-center truncate">pizzeriasole.mijnpizzeria.nl</span>
                        <span class="hidden sm:block w-16"></span>
                    </div>
                    <div class="relative aspect-[4/3] sm:aspect-[2/1] overflow-hidden">
                        <img src="{{ asset('assets/site/bestel-groot.png') }}" alt="Voorbeeld van de bestelpagina van Pizzeria Sole" class="absolute inset-0 w-full h-full object-cover object-top">
                    </div>
                </div>

                {{-- Zwevende mini-schermen: afrekenen linksonder, live bezorgstatus rechtsboven --}}
                <div class="hidden lg:block absolute -left-10 -bottom-10 w-80 -rotate-2 rounded-2xl bg-white border-[3px] border-crema-dark shadow-2xl overflow-hidden">
                    <div class="flex items-center gap-1.5 px-3 py-2 border-b-2 border-crema-dark" style="background:var(--color-crema)">
                        <span class="w-2 h-2 rounded-full bg-tomato/70"></span>
                        <span class="w-2 h-2 rounded-full bg-gold/80"></span>
                        <span class="w-2 h-2 rounded-full bg-basil/70"></span>
                        <span class="ml-1.5 text-[11px] font-extrabold text-cacao/45">Afrekenen met spaarpunten</span>
                    </div>
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ asset('assets/site/afrekenen-mini.png') }}" alt="Voorbeeld van de afrekenpagina" loading="lazy" class="absolute inset-0 w-full h-full object-cover object-top">
                    </div>
                </div>
                <div class="hidden lg:block absolute -right-8 -top-12 w-80 rotate-2 rounded-2xl bg-white border-[3px] border-crema-dark shadow-2xl overflow-hidden">
                    <div class="flex items-center gap-1.5 px-3 py-2 border-b-2 border-crema-dark" style="background:var(--color-crema)">
                        <span class="w-2 h-2 rounded-full bg-tomato/70"></span>
                        <span class="w-2 h-2 rounded-full bg-gold/80"></span>
                        <span class="w-2 h-2 rounded-full bg-basil/70"></span>
                        <span class="ml-1.5 text-[11px] font-extrabold text-cacao/45">Live bezorgstatus</span>
                    </div>
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ asset('assets/site/status-mini.png') }}" alt="Voorbeeld van de statuspagina" loading="lazy" class="absolute inset-0 w-full h-full object-cover object-top">
                    </div>
                </div>
                <img src="{{ asset('stickers/running-with-pizza.png') }}" alt="" class="absolute -bottom-10 -right-3 h-32 sm:h-36 select-none pointer-events-none drop-shadow-lg">
            </div>
        </div>
    </section>

    <style>
        @keyframes wiebel { 0%, 100% { transform: rotate(0); } 30% { transform: rotate(-9deg); } 70% { transform: rotate(9deg); } }
        .functie-kaart:hover .f-icoon { animation: wiebel .5s ease-in-out; }
        .kop-streep { position: absolute; left: 0; right: 0; bottom: -.12em; height: .14em; border-radius: 9999px; background: var(--color-gold); }
        html { overflow-x: clip; }
        .template-knop { width: 100%; text-align: left; border: 2px solid rgba(255,255,255,.16); border-radius: 1.1rem; background: rgba(255,255,255,.06); color: #fff; cursor: pointer; transition: background .15s ease, border-color .15s ease; }
        .template-knop:hover { background: rgba(255,255,255,.13); }
        .template-knop.aan { background: var(--color-gold); border-color: var(--color-gold); color: var(--color-cacao); }
        .template-knop.aan .t-sub { color: rgba(56,34,26,.6); }
        .template-knop .t-sub { color: rgba(255,255,255,.5); }
    </style>

    {{-- FUNCTIES: witte band, loopt door vanachter de hero-preview --}}
    <section id="functies" class="scroll-mt-16">
        <div class="bg-white pt-10 sm:pt-14 pb-20 sm:pb-24 overflow-x-clip -mt-px">
            <div class="max-w-6xl mx-auto px-5">
                <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                    <div class="max-w-xl" data-reveal="links">
                        <p class="step-kicker">Alles erin 🧰</p>
                        <h2 class="font-display text-4xl sm:text-5xl text-cacao">Alles wat je zaak <span class="relative inline-block">nodig heeft<span class="kop-streep" aria-hidden="true"></span></span></h2>
                    </div>
                    <p class="max-w-sm text-base font-bold text-cacao/55 lg:pb-2" data-reveal="rechts">Geen losse tools en koppelingen: een pakket dat samen werkt, van bestelling tot vaste klant.</p>
                </div>

                {{-- Bento: een featured kaart met het echte product, de rest verspringt eromheen --}}
                <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="functie-kaart sm:col-span-2 lg:row-span-2 rounded-3xl border-2 border-crema-dark p-6 flex flex-col transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" style="background:var(--color-crema)" data-reveal="links">
                        <div class="flex items-start gap-4">
                            <span class="f-icoon inline-grid w-12 h-12 shrink-0 rounded-2xl bg-tomato/10 place-items-center text-2xl">🎨</span>
                            <div>
                                <p class="font-display text-2xl">Eigen bestelpagina</p>
                                <p class="mt-1 text-sm font-bold text-cacao/55">Vier templates, jouw kleur en logo. Klaar in tien minuten, zonder technische kennis. Zo ziet hij eruit:</p>
                            </div>
                        </div>
                        <div class="mt-5 flex-1 min-h-44 rounded-2xl border-2 border-crema-dark bg-white overflow-hidden relative">
                            <img src="{{ asset('assets/site/bestel-kaart.png') }}" alt="De bestelpagina van Pizzeria Sole" loading="lazy" class="absolute inset-0 w-full h-full object-cover object-top">
                        </div>
                    </div>
                    <div class="functie-kaart sm:col-span-2 rounded-3xl border-2 border-crema-dark bg-gold/15 p-6 transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" data-reveal="rechts">
                        <div class="flex items-start gap-4">
                            <span class="f-icoon inline-grid w-12 h-12 shrink-0 rounded-2xl bg-gold/25 place-items-center text-2xl">🌟</span>
                            <div>
                                <p class="font-display text-xl">Spaarprogramma</p>
                                <p class="mt-1 text-sm font-bold text-cacao/55">Klanten sparen automatisch punten en wisselen ze in voor korting. Zo komen ze terug.</p>
                            </div>
                        </div>
                    </div>
                    <div class="functie-kaart rounded-3xl border-2 border-crema-dark bg-basil/10 p-6 transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" data-reveal>
                        <span class="f-icoon inline-grid w-12 h-12 rounded-2xl bg-basil/15 place-items-center text-2xl">🛵</span>
                        <p class="font-display text-xl mt-4">Live bezorgstatus</p>
                        <p class="mt-1.5 text-sm font-bold text-cacao/55">Statusstappen en de route van de bezorger, live op de kaart.</p>
                    </div>
                    <div class="functie-kaart rounded-3xl border-2 border-crema-dark bg-tomato/10 p-6 transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" data-reveal="rechts">
                        <span class="f-icoon inline-grid w-12 h-12 rounded-2xl bg-tomato/15 place-items-center text-2xl">🍕</span>
                        <p class="font-display text-xl mt-4">Pizza Coach</p>
                        <p class="mt-1.5 text-sm font-bold text-cacao/55">Ziet slapende klanten, toppers en rustige momenten, en stelt acties voor die omzet opleveren.</p>
                    </div>
                    <div class="functie-kaart sm:col-span-2 lg:col-span-2 rounded-3xl border-2 border-crema-dark p-6 transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" style="background:var(--color-crema)" data-reveal="links">
                        <div class="flex items-start gap-4">
                            <span class="f-icoon inline-grid w-12 h-12 shrink-0 rounded-2xl bg-basil/10 place-items-center text-2xl">📦</span>
                            <div>
                                <p class="font-display text-xl">Dozen met jouw opdruk</p>
                                <p class="mt-1 text-sm font-bold text-cacao/55">Pizzadozen met je logo en een spaar-QR die klanten terugbrengt naar jouw pagina.</p>
                            </div>
                        </div>
                    </div>
                    <div class="functie-kaart sm:col-span-2 lg:col-span-2 rounded-3xl border-2 border-crema-dark bg-gold/15 p-6 transition-all duration-200 hover:-translate-y-1.5 hover:shadow-xl" data-reveal="rechts">
                        <div class="flex items-start gap-4">
                            <span class="f-icoon inline-grid w-12 h-12 shrink-0 rounded-2xl bg-gold/25 place-items-center text-2xl">💳</span>
                            <div>
                                <p class="font-display text-xl">Betalingen via Stripe</p>
                                <p class="mt-1 text-sm font-bold text-cacao/55">iDEAL, creditcard, Apple Pay en Google Pay. Het geld gaat rechtstreeks naar jou.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-10 flex flex-wrap items-center gap-4" data-reveal="links">
                    <a href="/onboarding" class="btn-primary !px-7 !py-3">Gratis starten 🍕</a>
                    <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="text-base font-extrabold text-cacao/60 underline underline-offset-4 decoration-cacao/25 hover:text-cacao transition-colors">Bekijk een echte bestelpagina</a>
                </div>
            </div>
        </div>
    </section>

    {{-- TEMPLATES: donkere band, direct vanuit het wit --}}
    <section id="templates" class="scroll-mt-16">
        <svg class="w-full h-10 sm:h-14 block bg-white -mb-px" viewBox="0 0 1440 64" preserveAspectRatio="none" aria-hidden="true"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 64 L 0 64 Z" fill="#38221A"/></svg>
        <div class="py-20 sm:py-24 relative overflow-hidden" style="background:var(--color-cacao)">
            <span class="float-emoji !opacity-20" style="top:12%; left:5%; --delay:.6s; --size:2rem;" aria-hidden="true">🍕</span>
            <span class="float-emoji !opacity-20" style="top:75%; left:93%; --delay:1.8s; --size:1.8rem;" aria-hidden="true">🧀</span>
            <div class="max-w-6xl mx-auto px-5 relative">
                <div class="grid lg:grid-cols-5 gap-10 lg:gap-12 items-center">
                    <div class="lg:col-span-2">
                        <div data-reveal="links">
                            <p class="step-kicker" style="color:var(--color-gold)">Kies je stijl 🎨</p>
                            <h2 class="font-display text-4xl sm:text-5xl text-white">Vier stijlen, <span class="relative inline-block">een motor<span class="kop-streep" aria-hidden="true"></span></span></h2>
                            <p class="mt-3 text-base font-bold text-white/55">Zelfde functies, totaal ander gevoel. Klik en zie dezelfde zaak in elke stijl, live.</p>
                        </div>
                        <div class="mt-7 space-y-2.5">
                            <button type="button" data-template="template1" data-kleur="%23E63946" data-tekst="Presto: licht en modern, als een strakke bestel-app." class="template-chip template-knop aan flex items-center gap-3.5 px-4 py-3" data-reveal="links">
                                <span class="text-2xl shrink-0">🛵</span>
                                <span class="min-w-0"><span class="block font-display text-lg leading-tight">Presto</span><span class="t-sub block text-xs font-extrabold">Licht en modern, als een strakke bestel-app</span></span>
                            </button>
                            <button type="button" data-template="template2" data-kleur="%237D1D3F" data-tekst="Notte: klassiek en verfijnd, als een editoriale menukaart." class="template-chip template-knop flex items-center gap-3.5 px-4 py-3" data-reveal="links">
                                <span class="text-2xl shrink-0">🍷</span>
                                <span class="min-w-0"><span class="block font-display text-lg leading-tight">Notte</span><span class="t-sub block text-xs font-extrabold">Klassiek en verfijnd, als een editoriale menukaart</span></span>
                            </button>
                            <button type="button" data-template="template3" data-kleur="%231F2937" data-tekst="Forza: bold en vol energie, als een menubord aan de muur." class="template-chip template-knop flex items-center gap-3.5 px-4 py-3" data-reveal="links">
                                <span class="text-2xl shrink-0">⚡</span>
                                <span class="min-w-0"><span class="block font-display text-lg leading-tight">Forza</span><span class="t-sub block text-xs font-extrabold">Bold en vol energie, als een menubord aan de muur</span></span>
                            </button>
                            <button type="button" data-template="template4" data-kleur="%23F5B301" data-tekst="Giro: fris met grote foto's, als de grote bezorg-apps." class="template-chip template-knop flex items-center gap-3.5 px-4 py-3" data-reveal="links">
                                <span class="text-2xl shrink-0">📸</span>
                                <span class="min-w-0"><span class="block font-display text-lg leading-tight">Giro</span><span class="t-sub block text-xs font-extrabold">Fris met grote foto's, als de grote bezorg-apps</span></span>
                            </button>
                        </div>
                        <p id="templateTekst" class="sr-only" aria-live="polite">Presto: licht en modern, als een strakke bestel-app.</p>
                        <div class="mt-8 flex flex-wrap items-center gap-4" data-reveal="links">
                            <a href="/onboarding" class="btn-primary !px-7 !py-3">Gratis starten 🍕</a>
                            <a id="templateOpen" href="/bestellen/pizzeriasole?thema=template1&kleur=%23E63946" target="_blank" rel="noopener" class="text-base font-extrabold text-white/70 underline underline-offset-4 decoration-white/30 hover:text-white transition-colors">Open dit voorbeeld groot</a>
                        </div>
                    </div>
                    <div class="lg:col-span-3 rounded-3xl bg-white border-4 border-white/15 shadow-2xl overflow-hidden lg:rotate-1" data-reveal="rechts">
                        <div class="flex items-center gap-2 px-5 py-3.5 border-b-2 border-crema-dark" style="background:var(--color-crema)">
                            <span class="w-3 h-3 rounded-full bg-tomato/70"></span>
                            <span class="w-3 h-3 rounded-full bg-gold/80"></span>
                            <span class="w-3 h-3 rounded-full bg-basil/70"></span>
                            <span class="mx-auto w-full max-w-sm rounded-full bg-white border-2 border-crema-dark px-4 py-1 text-xs font-extrabold text-cacao/45 text-center truncate">pizzeriasole.mijnpizzeria.nl</span>
                            <span class="hidden sm:block w-16"></span>
                        </div>
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <iframe id="templateFrame" src="/bestellen/pizzeriasole?thema=template1&kleur=%23E63946" title="Live voorbeeld van een template" loading="lazy" class="pointer-events-none"
                                style="width:200%; height:200%; transform:scale(.5); transform-origin:top left; border:0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <svg class="w-full h-10 sm:h-14 block rotate-180 -mt-px" viewBox="0 0 1440 64" preserveAspectRatio="none" aria-hidden="true"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 64 L 0 64 Z" fill="#38221A"/></svg>
    </section>

    {{-- SPAARPROGRAMMA: crema, met zwevende doos --}}
    <section id="spaarprogramma" class="scroll-mt-24 max-w-6xl mx-auto px-5 py-20 sm:py-24 overflow-x-clip">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div data-reveal="links">
                    <p class="step-kicker">Vaste klanten 🌟</p>
                    <h2 class="font-display text-4xl sm:text-5xl text-cacao">Klanten die <span class="relative inline-block">terugkomen<span class="kop-streep" aria-hidden="true"></span></span></h2>
                    <p class="mt-3 text-base font-bold text-cacao/55">Bezorgplatforms houden jouw klanten voor zichzelf. Bij jou sparen ze automatisch, en de doos brengt ze terug naar jouw pagina.</p>
                </div>
                <div class="mt-8 space-y-4">
                    @foreach([
                        ['1', 'Klant bestelt bij jou', 'Met een eigen account: gegevens staan de volgende keer al klaar.', '', '-rotate-1'],
                        ['2', 'Punten sparen vanzelf', '1 punt per euro, zonder stempelkaart of gedoe.', 'sm:ml-8 lg:ml-10', 'rotate-1'],
                        ['3', 'Punten worden korting', 'Elke 100 punten zijn 5 euro korting bij het afrekenen.', 'sm:ml-16 lg:ml-20', '-rotate-1'],
                    ] as [$nr, $titel, $tekst, $inspring, $draai])
                        <div class="spaar-stap flex items-start gap-4 bg-white rounded-2xl border-2 border-crema-dark p-4 {{ $inspring }} {{ $draai }} hover:rotate-0 transition-transform duration-200" data-reveal="links">
                            <span class="w-10 h-10 shrink-0 rounded-full bg-gold/20 grid place-items-center font-display text-lg text-cacao">{{ $nr }}</span>
                            <div>
                                <p class="font-display text-lg leading-tight">{{ $titel }}</p>
                                <p class="mt-1 text-sm font-bold text-cacao/55">{{ $tekst }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-6 inline-flex items-center gap-2.5 rounded-full bg-white border-2 border-crema-dark px-5 py-2.5 font-extrabold text-cacao" data-reveal>
                    🌟 <span><span id="spaarTeller" data-doel="127">0</span> punten gespaard</span>
                    <span class="text-cacao/40">= &euro; 5 korting verdiend</span>
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4" data-reveal="links">
                    <a href="/onboarding" class="btn-primary !px-7 !py-3">Gratis starten 🍕</a>
                    <a href="/bestellen/pizzeriasole/afrekenen?voorbeeld=1" target="_blank" rel="noopener" class="text-base font-extrabold text-cacao/60 underline underline-offset-4 decoration-cacao/25 hover:text-cacao transition-colors">Bekijk het afrekenen met punten</a>
                </div>
            </div>
            {{-- De doos met spaar-QR, zoals hij echt gedrukt wordt --}}
            <div id="spaarDoos" class="justify-self-center" data-reveal>
                <img src="{{ asset('assets/pizza-doos.jpeg') }}" alt="Pizzadoos met eigen opdruk en een spaar-QR die klanten terugbrengt naar de bestelpagina"
                    class="w-80 sm:w-[26rem] rounded-3xl rotate-2 shadow-2xl border-4 border-white" loading="lazy">
            </div>
        </div>
    </section>

    {{-- PRIJZEN: witte band --}}
    <section id="prijzen" class="scroll-mt-16">
        <svg class="w-full h-10 sm:h-14 block -mb-px" viewBox="0 0 1440 64" preserveAspectRatio="none" aria-hidden="true"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 64 L 0 64 Z" fill="#FFFFFF"/></svg>
        <div class="bg-white py-20 sm:py-24 overflow-x-clip">
        <div class="max-w-6xl mx-auto px-5">
        <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
            <div class="max-w-xl" data-reveal="links">
                <p class="step-kicker">Eerlijk rekenen 💶</p>
                <h2 class="font-display text-4xl sm:text-5xl text-cacao">Een prijs, geen <span class="relative inline-block">verrassingen<span class="kop-streep" aria-hidden="true"></span></span></h2>
            </div>
            <p class="max-w-sm text-base font-bold text-cacao/55 lg:pb-2" data-reveal="rechts">Geen commissie per bestelling. Hoe meer je verkoopt, hoe meer je bespaart.</p>
        </div>
        <div class="mt-12 grid lg:grid-cols-5 gap-6 items-stretch">
            <div class="lg:col-span-2 rounded-3xl border-2 border-crema-dark p-8 flex flex-col lg:-rotate-1 lg:hover:rotate-0 transition-transform duration-200" style="background:var(--color-crema)" data-reveal="links">
                <p class="font-display text-xl">Alles erin</p>
                <p class="mt-3"><span class="font-display text-6xl text-cacao">&euro; 24,95</span> <span class="font-extrabold text-cacao/45">per maand</span></p>
                <ul class="mt-6 space-y-2.5 text-sm font-extrabold text-cacao/65 flex-1">
                    @foreach(['Eigen bestelpagina met jouw stijl', 'Onbeperkt bestellingen, 0% commissie', 'Spaarprogramma en klantaccounts', 'Pizza Coach met omzet-inzichten', 'Live bezorgstatus voor je klanten', 'Maandelijks opzegbaar'] as $punt)
                        <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/15 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> {{ $punt }}</li>
                    @endforeach
                </ul>
                <a href="/onboarding" class="btn-primary text-center !py-3.5 mt-7">Gratis starten 🍕</a>
            </div>
            <div class="lg:col-span-3 rounded-3xl p-8 flex flex-col" style="background:var(--color-cacao)" data-reveal="rechts">
                <p class="font-display text-xl text-white">Wat bespaar jij?</p>
                <p class="mt-1.5 text-sm font-bold text-white/50">Bezorgplatforms rekenen al snel 13% commissie per bestelling.</p>
                <div class="mt-7">
                    <div class="flex items-baseline justify-between gap-3">
                        <label for="calcSlider" class="text-sm font-extrabold text-white/60">Bestellingen per maand</label>
                        <p class="font-display text-3xl text-white"><span id="calcAantal">300</span></p>
                    </div>
                    <input id="calcSlider" type="range" min="50" max="1500" step="25" value="300" class="w-full mt-3 accent-[#F5B301] cursor-pointer">
                </div>
                <div class="mt-7 space-y-3 text-sm font-extrabold flex-1">
                    <div class="flex justify-between gap-3 text-white/55"><span>13% commissie bij een platform</span><span>&euro; <span id="calcCommissie">0</span></span></div>
                    <div class="flex justify-between gap-3 text-white/55"><span>MijnPizzeria, vast per maand</span><span>&euro; 24,95</span></div>
                    <div class="pt-3 border-t border-white/15 flex justify-between items-baseline gap-3 text-white">
                        <span class="font-display text-lg">Jij bespaart</span>
                        <span class="font-display text-4xl" style="color:var(--color-gold)">&euro; <span id="calcBesparing">0</span> <span class="text-base text-white/45">p/m</span></span>
                    </div>
                </div>
                <p class="mt-5 text-[11px] font-extrabold text-white/35">Rekenvoorbeeld bij een gemiddelde bestelling van &euro; 25.</p>
            </div>
        </div>
        </div>
        </div>
    </section>

    {{-- SLOT-CTA: tomaatrode band, direct vanuit het wit --}}
    <section>
        <svg class="w-full h-10 sm:h-14 block bg-white -mb-px" viewBox="0 0 1440 64" preserveAspectRatio="none" aria-hidden="true"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 64 L 0 64 Z" fill="#E63946"/></svg>
        <div class="relative overflow-hidden py-16 sm:py-20" style="background:var(--color-tomato)">
            <span class="float-emoji !opacity-25" style="top:18%; left:6%; --delay:.4s; --size:1.9rem;" aria-hidden="true">🍕</span>
            <span class="float-emoji !opacity-25" style="top:12%; left:55%; --delay:1.6s; --size:1.7rem;" aria-hidden="true">🧀</span>
            <div class="max-w-6xl mx-auto px-5 flex flex-col-reverse sm:flex-row items-center gap-10 sm:gap-14">
                <div class="flex-1 text-center sm:text-left" data-reveal="links">
                    <h2 class="font-display text-4xl sm:text-5xl text-white">Proef het zelf</h2>
                    <p class="mt-3 text-base font-bold text-white/75 max-w-xl">Klik door een echte bestelpagina of zet je eigen zaak online. Binnen tien minuten sta je erop, en je zit nergens aan vast.</p>
                    <div class="mt-8 flex flex-wrap items-center justify-center sm:justify-start gap-4">
                        <a href="/onboarding" class="inline-block rounded-full bg-white font-display text-lg text-tomato px-9 py-3.5 shadow-lg hover:bg-crema transition-colors">Gratis starten 🍕</a>
                        <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="text-base font-extrabold text-white/80 underline underline-offset-4 decoration-white/40 hover:text-white transition-colors">Eerst even rondkijken</a>
                    </div>
                </div>
                <img id="slotMascotte" src="{{ asset('stickers/pizza-in-oven.png') }}" alt="" class="h-40 sm:h-56 shrink-0 select-none pointer-events-none drop-shadow-xl sm:rotate-3" loading="lazy" data-reveal="rechts">
            </div>
        </div>
        <svg class="w-full h-10 sm:h-14 block rotate-180 -mt-px -mb-px bg-cacao" viewBox="0 0 1440 64" preserveAspectRatio="none" aria-hidden="true"><path d="M0 34 C 240 64 480 6 720 34 C 960 62 1200 8 1440 34 L 1440 66 L 0 66 Z" fill="#E63946"/></svg>
    </section>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        /* Zonder dit vecht het scrollherstel van de browser bij een reload met de scroll-animaties en verspringt de pagina een stukje */
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
        /* Chrome schuift bij een reload alsnog een paar pixels op tijdens de animatie-init; als we bijna bovenaan staan, snappen we strak naar 0 */
        const snapNaarTop = () => { if (!location.hash && window.scrollY > 0 && window.scrollY < 48) window.scrollTo(0, 0); };
        requestAnimationFrame(() => requestAnimationFrame(snapNaarTop));
        window.addEventListener('load', () => { snapNaarTop(); setTimeout(snapNaarTop, 250); });

        const bewegingOk = !window.matchMedia('(prefers-reduced-motion: reduce)').matches && window.gsap;

        /* Reveals met richting: links en rechts schuiven in met een veertje, de rest komt van onder */
        if (bewegingOk) {
            gsap.registerPlugin(ScrollTrigger);
            gsap.utils.toArray('[data-reveal]').forEach((el) => {
                const richting = el.getAttribute('data-reveal');
                const van = richting === 'links' ? { x: -56, y: 0 } : richting === 'rechts' ? { x: 56, y: 0 } : { x: 0, y: 30 };
                gsap.from(el, {
                    opacity: 0, ...van, duration: .75, ease: 'back.out(1.4)',
                    scrollTrigger: { trigger: el, start: 'top 88%', once: true },
                });
            });

            /* Gouden strepen onder kernwoorden tekenen zichzelf */
            gsap.utils.toArray('.kop-streep').forEach((el) => {
                gsap.from(el, {
                    scaleX: 0, transformOrigin: 'left center', duration: .55, delay: .35, ease: 'power3.out',
                    scrollTrigger: { trigger: el, start: 'top 88%', once: true },
                });
            });

            /* De mascotte in het slot wandelt mee met het scrollen */
            if (document.querySelector('#slotMascotte')) {
                gsap.to('#slotMascotte', {
                    y: -22, rotate: -4, ease: 'none',
                    scrollTrigger: { trigger: '#slotMascotte', start: 'top bottom', end: 'bottom top', scrub: 1 },
                });
            }
        }

        /* De pizzadoos komt binnen met een zwiep en zweeft mee met het scrollen */
        if (bewegingOk && document.querySelector('#spaarDoos')) {
            gsap.from('#spaarDoos', {
                rotate: 8, scale: .94, duration: .8, ease: 'back.out(1.6)',
                scrollTrigger: { trigger: '#spaarDoos', start: 'top 85%', once: true },
            });
            gsap.to('#spaarDoos', {
                y: -36, ease: 'none',
                scrollTrigger: { trigger: '#spaarprogramma', start: 'top bottom', end: 'bottom top', scrub: 1.2 },
            });
        }

        /* Templates: live wisselen van stijl in het frame */
        const templateFrame = document.querySelector('#templateFrame');
        document.querySelectorAll('.template-chip').forEach((chip) => chip.addEventListener('click', () => {
            document.querySelectorAll('.template-chip').forEach((c) => c.classList.toggle('aan', c === chip));
            document.querySelector('#templateTekst').textContent = chip.dataset.tekst;
            const openLink = document.querySelector('#templateOpen');
            if (openLink) openLink.href = `/bestellen/pizzeriasole?thema=${chip.dataset.template}&kleur=${chip.dataset.kleur}`;
            if (bewegingOk) gsap.fromTo(chip, { scale: .93 }, { scale: 1, duration: .45, ease: 'back.out(2.5)', clearProps: 'scale' });
            const wissel = () => { templateFrame.src = `/bestellen/pizzeriasole?thema=${chip.dataset.template}&kleur=${chip.dataset.kleur}`; };
            if (bewegingOk) {
                gsap.to(templateFrame, { opacity: 0, duration: .18, onComplete: wissel });
                templateFrame.addEventListener('load', () => gsap.to(templateFrame, { opacity: 1, duration: .3 }), { once: true });
            } else {
                wissel();
            }
        }));

        /* Spaarteller telt op zodra hij in beeld komt */
        const spaarTeller = document.querySelector('#spaarTeller');
        if (spaarTeller) {
            const doel = Number(spaarTeller.dataset.doel);
            if (bewegingOk) {
                const stand = { n: 0 };
                gsap.to(stand, {
                    n: doel, duration: 1.4, ease: 'power1.out',
                    snap: { n: 1 },
                    onUpdate: () => { spaarTeller.textContent = stand.n; },
                    scrollTrigger: { trigger: spaarTeller, start: 'top 90%', once: true },
                });
            } else {
                spaarTeller.textContent = doel;
            }
        }

        /* Besparingscalculator: eerlijk rekenvoorbeeld, live */
        const calcSlider = document.querySelector('#calcSlider');
        if (calcSlider) {
            const GEM_BESTELLING = 25;
            const COMMISSIE = 0.13;
            const VAST = 24.95;
            const euroTekst = (n) => Math.round(n).toLocaleString('nl-NL');
            const standen = { commissie: 0, besparing: 0 };
            const rekenUit = () => {
                const aantal = Number(calcSlider.value);
                const commissie = aantal * GEM_BESTELLING * COMMISSIE;
                const besparing = Math.max(0, commissie - VAST);
                document.querySelector('#calcAantal').textContent = aantal;
                if (bewegingOk) {
                    gsap.to(standen, {
                        commissie, besparing, duration: .45, ease: 'power2.out',
                        onUpdate: () => {
                            document.querySelector('#calcCommissie').textContent = euroTekst(standen.commissie);
                            document.querySelector('#calcBesparing').textContent = euroTekst(standen.besparing);
                        },
                        overwrite: true,
                    });
                } else {
                    document.querySelector('#calcCommissie').textContent = euroTekst(commissie);
                    document.querySelector('#calcBesparing').textContent = euroTekst(besparing);
                }
            };
            calcSlider.addEventListener('input', rekenUit);
            rekenUit();
        }
    </script>
@endsection
