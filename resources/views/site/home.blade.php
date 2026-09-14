@extends('site.layout')

@section('inhoud')
    {{-- HERO: een reuzenpizza over de volle breedte, de tekst staat in de saus --}}
    <section class="relative overflow-hidden" style="background:var(--color-crema)">
        {{-- Een echte pepperoni pizza als reuzenbeeld; de donkere waas zit in de afbeelding zelf
             gebakken (alleen op de pizza-pixels), zodat de niet-perfect-ronde korstrand vrij blijft --}}
        <div class="pizza-schijf w-[320vw] sm:w-[220vw] lg:w-[170vw]" aria-hidden="true">
            <img id="pizzaDraai" src="{{ asset('assets/eten/pizza-heel.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
        </div>

        <div class="relative max-w-3xl mx-auto px-5 text-center pt-32 sm:pt-40" style="padding-bottom:clamp(22rem, 30vw, 30rem)">
            <h1 class="font-display text-5xl sm:text-6xl lg:text-[4.2rem] leading-[1.04] text-white" data-intro>Jouw pizzeria online, zonder commissie.</h1>
            <p class="mt-7 text-xl font-semibold text-white/85 max-w-2xl mx-auto" data-intro>Eigen bestelpagina, iDEAL en een spaarprogramma dat klanten laat terugkomen. 24,95 euro per maand, verder niets.</p>
            <div class="mt-9 flex flex-wrap items-center justify-center gap-4" data-intro>
                <a href="/onboarding" class="inline-block rounded-lg bg-white text-tomato text-lg font-bold px-8 py-4 shadow-lg hover:bg-crema transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Gratis starten</a>
                <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="btn-tweede-donker !text-lg !px-8 !py-[0.95rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Bekijk een echte bestelpagina</a>
            </div>
            <div class="mt-11 flex flex-wrap items-center justify-center gap-x-10 gap-y-3 text-sm font-semibold text-white/80" data-intro>
                <span class="flex items-center gap-2"><i class="fa-solid fa-check" aria-hidden="true"></i> 30 dagen gratis, zonder betaalgegevens</span>
                <span class="flex items-center gap-2"><i class="fa-solid fa-check" aria-hidden="true"></i> 0% commissie</span>
                <span class="flex items-center gap-2"><i class="fa-solid fa-check" aria-hidden="true"></i> Maandelijks opzegbaar</span>
            </div>
        </div>
    </section>

    {{-- HET PRODUCT: carrousel op de korstrand; de voorste groot, de zijkanten kleiner, de vierde verstopt erachter (flow-root voorkomt dat de negatieve marge de hele sectie optrekt) --}}
    <section class="relative flow-root" style="background:var(--color-crema)">
        <div class="max-w-6xl mx-auto px-5">
            <div class="-mt-40 sm:-mt-64 lg:-mt-[23rem] relative z-10 pb-10" data-intro>
                <div id="carrousel" class="relative w-full max-w-[62rem] mx-auto aspect-[16/10]">
                    @foreach([
                        ['bestel-groot.png', 'pizzeriasole.mijnpizzeria.nl', 'De bestelpagina van Pizzeria Sole met menukaart en winkelmand', 'voor'],
                        ['dashboard-groot.png', 'mijnpizzeria.nl/dashboard', 'Het dashboard met openstaande bestellingen en openingstijden', 'rechts'],
                        ['afrekenen-mini.png', 'pizzeriasole.mijnpizzeria.nl/afrekenen', 'De afrekenpagina met spaarpunten', 'achter'],
                        ['status-mini.png', 'pizzeriasole.mijnpizzeria.nl/bestelling', 'De live bezorgstatus die klanten volgen', 'links'],
                    ] as [$beeld, $url, $alt, $positie])
                        <div class="car-dia" data-pos="{{ $positie }}" role="button" tabindex="0" aria-label="Toon voorbeeld: {{ $alt }}">
                            <div class="flex items-center gap-2 px-5 py-3 border-b border-cacao/10 bg-white shrink-0">
                                <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                <span class="mx-auto w-full max-w-sm rounded-full border border-cacao/10 px-4 py-1 text-xs font-semibold text-cacao/45 text-center truncate">{{ $url }}</span>
                                <span class="hidden sm:block w-16"></span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <img src="{{ asset('assets/site/' . $beeld) }}" alt="{{ $alt }}" class="w-full h-full object-cover object-top" @if($positie !== 'voor') loading="lazy" @endif>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-7 flex items-center justify-center gap-2.5" role="group" aria-label="Kies een voorbeeldscherm">
                    @foreach(['Bestelpagina', 'Dashboard', 'Afrekenen met punten', 'Live bezorgstatus'] as $i => $naam)
                        <button type="button" class="car-stip {{ $i === 0 ? 'aan' : '' }}" data-dia="{{ $i }}" aria-label="{{ $naam }}"></button>
                    @endforeach
                </div>
                <p id="carrouselNaam" class="mt-3 text-center text-sm font-semibold text-cacao/50" aria-live="polite">Bestelpagina</p>
            </div>
        </div>
    </section>

    {{-- FUNCTIES: crema band, zes redenen --}}
    <section id="functies" class="scroll-mt-24 relative" style="background:var(--color-crema)">
        <div class="gloed w-[30rem] h-[30rem] bg-gold/10 -top-60 -right-48" data-gloed aria-hidden="true"></div>
        <div class="max-w-6xl mx-auto px-5 py-24 sm:py-28 relative">
            <h2 class="font-display text-4xl sm:text-5xl text-cacao max-w-2xl" data-reveal>Alles wat je zaak nodig heeft</h2>
            <div class="mt-14 sm:mt-16 grid sm:grid-cols-2 md:grid-cols-3 gap-x-12 gap-y-14" data-reveal-groep>
                @foreach([
                    ['fa-palette', 'Eigen bestelpagina', 'Vier templates, jouw kleur en logo. Klaar in tien minuten, zonder technische kennis.'],
                    ['fa-credit-card', 'Betalingen via Stripe', 'iDEAL, creditcard, Apple Pay en Google Pay. Het geld gaat rechtstreeks naar jouw rekening.'],
                    ['fa-star', 'Spaarprogramma', 'Klanten sparen automatisch punten en wisselen ze in voor korting. Zo komen ze terug.'],
                    ['fa-moped', 'Live bezorgstatus', 'Klanten volgen hun bestelling van oven tot voordeur, met de route live op de kaart.'],
                    ['fa-pizza-slice', 'Pizza Coach', 'Ziet slapende klanten en rustige momenten, en stelt acties voor die omzet opleveren.'],
                    ['fa-box-open', 'Dozen met jouw opdruk', 'Pizzadozen met je logo en een spaar-QR die klanten terugbrengt naar jouw pagina.'],
                ] as [$icoon, $titel, $tekst])
                    <div>
                        <i class="fa-solid {{ $icoon }} text-tomato text-xl" aria-hidden="true"></i>
                        <p class="font-display text-2xl mt-4">{{ $titel }}</p>
                        <p class="mt-2 text-[15px] font-semibold text-cacao/55 leading-relaxed">{{ $tekst }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-14 flex flex-wrap items-center gap-4" data-reveal>
                <a href="/onboarding" class="btn-primary !px-7 !py-3.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Gratis starten</a>
                <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="btn-tweede !px-7 !py-[0.82rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Bekijk een demozaak live</a>
            </div>
        </div>
    </section>

    {{-- DE CIJFERS: feiten die optellen zodra je ze ziet --}}
    <section class="bg-white border-b border-cacao/10">
        <div class="max-w-6xl mx-auto px-5 py-14 grid grid-cols-2 md:grid-cols-4 gap-y-10 md:divide-x md:divide-cacao/10" data-reveal-groep>
            <div class="md:px-10 md:pl-0">
                <p class="font-display text-4xl sm:text-5xl text-cacao"><span data-tel="0" data-dec="0">0</span>%</p>
                <p class="mt-2 text-sm font-semibold text-cacao/50">commissie per bestelling</p>
            </div>
            <div class="md:px-10">
                <p class="font-display text-4xl sm:text-5xl text-cacao">&euro; <span data-tel="24.95" data-dec="2">24,95</span></p>
                <p class="mt-2 text-sm font-semibold text-cacao/50">per maand, alles erin</p>
            </div>
            <div class="md:px-10">
                <p class="font-display text-4xl sm:text-5xl text-cacao"><span data-tel="10" data-dec="0">10</span> min</p>
                <p class="mt-2 text-sm font-semibold text-cacao/50">van menukaart tot live</p>
            </div>
            <div class="md:px-10">
                <p class="font-display text-4xl sm:text-5xl text-cacao"><span data-tel="100" data-dec="0">100</span> pt</p>
                <p class="mt-2 text-sm font-semibold text-cacao/50">is vijf euro korting</p>
            </div>
        </div>
    </section>

    {{-- PRODUCT: de live status, wit --}}
    <section class="bg-white overflow-x-clip">
        <div class="max-w-6xl mx-auto px-5 py-24 sm:py-28">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div data-reveal>
                    <h2 class="font-display text-3xl sm:text-4xl text-cacao">Klanten volgen hun bestelling live</h2>
                    <p class="mt-4 text-base font-semibold text-cacao/55 leading-relaxed max-w-lg">Van bevestigd tot bezorgd: elke stap staat live op de statuspagina, inclusief de route van de bezorger op de kaart. Dat scheelt jou de telefoontjes "waar blijft mijn pizza".</p>
                    <ul class="mt-6 space-y-2.5 text-[15px] font-semibold text-cacao/60" data-reveal-groep>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Statusstappen met een tik vanuit je dashboard</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Werkt op telefoon, tablet en computer</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Bezorgen en afhalen, met eigen bezorgkosten per afstand</li>
                    </ul>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="/onboarding" class="btn-primary !px-7 !py-3.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Gratis starten</a>
                        <a href="/bestellen/pizzeriasole/bestelling?voorbeeld=1" target="_blank" rel="noopener" class="btn-tweede !px-7 !py-[0.82rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Bekijk de bezorgstatus</a>
                    </div>
                </div>
                <div class="rounded-xl border border-cacao/10 shadow-[0_30px_60px_-25px_rgb(36_23_18/.3)] overflow-hidden" data-reveal data-zweef>
                    <img src="{{ asset('assets/site/status-mini.png') }}" alt="De live bezorgstatus die klanten volgen" loading="lazy" class="w-full">
                </div>
            </div>
        </div>
    </section>

    {{-- PRODUCT: sparen en afrekenen, crema band --}}
    <section id="spaarprogramma" class="scroll-mt-24 relative overflow-hidden" style="background:var(--color-crema)">
        <div class="gloed w-[30rem] h-[30rem] bg-tomato/10 -bottom-40 -left-40" data-gloed aria-hidden="true"></div>
        <div class="max-w-6xl mx-auto px-5 py-24 sm:py-28 relative">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="order-1 lg:order-2" data-reveal>
                    <h2 class="font-display text-3xl sm:text-4xl text-cacao">Sparen en afrekenen in een beweging</h2>
                    <p class="mt-4 text-base font-semibold text-cacao/55 leading-relaxed max-w-lg">Klanten rekenen af met iDEAL, Apple Pay of Google Pay en sparen automatisch 1 punt per euro. Elke 100 punten zijn 5 euro korting, te verzilveren bij het afrekenen. Jouw klantenbestand, niet dat van een platform.</p>
                    <ul class="mt-6 space-y-2.5 text-[15px] font-semibold text-cacao/60" data-reveal-groep>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Punten verzilveren met een tik</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Accounts en bestelgeschiedenis horen bij jouw zaak</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Spaar-QR op de doos brengt klanten terug</li>
                    </ul>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="/onboarding" class="btn-primary !px-7 !py-3.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Gratis starten</a>
                        <a href="/bestellen/pizzeriasole/afrekenen?voorbeeld=1" target="_blank" rel="noopener" class="btn-tweede !px-7 !py-[0.82rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Bekijk het afrekenen met punten</a>
                    </div>
                </div>
                <div class="order-2 lg:order-1 rounded-xl border border-cacao/10 shadow-[0_30px_60px_-25px_rgb(36_23_18/.3)] overflow-hidden" data-reveal data-zweef>
                    <img src="{{ asset('assets/site/afrekenen-mini.png') }}" alt="De afrekenpagina met spaarpunten" loading="lazy" class="w-full">
                </div>
            </div>
        </div>
    </section>

    {{-- TEMPLATES: horizontale rit langs de vier stijlen, ingekaderd in de donkere band --}}
    <section id="templates" class="scroll-mt-24 relative korrel" style="background:var(--color-cacao)">
        <div id="templatesScherm" class="flex flex-col justify-center py-20 lg:py-0 lg:h-screen">
            <div class="max-w-6xl mx-auto px-5 w-full">
                <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                    <h2 class="font-display text-4xl sm:text-5xl text-white">Vier stijlen, een motor</h2>
                    <p class="max-w-sm text-base font-semibold text-white/60 lg:pb-1.5">Zelfde functies, totaal ander gevoel. Scroll en zie dezelfde zaak in elke stijl.</p>
                </div>
            </div>
            <div id="templatesRit" class="mt-10 sm:mt-12 overflow-x-auto snap-x snap-mandatory">
                <div id="templatesRij" class="flex gap-6 sm:gap-10 w-max" style="padding-left:max(1.25rem, calc((100vw - 72rem) / 2)); padding-right:max(1.25rem, calc((100vw - 72rem) / 2))">
                    @foreach([
                        ['template1.jpg', '%23E63946', 'Presto', 'Licht en modern, als een strakke bestel-app'],
                        ['template2.jpg', '%237D1D3F', 'Notte', 'Klassiek en verfijnd, als een editoriale menukaart'],
                        ['template3.jpg', '%231F2937', 'Forza', 'Bold en vol energie, als een menubord aan de muur'],
                        ['template4.jpg', '%23F5B301', 'Giro', 'Fris met grote foto\'s, als de grote bezorg-apps'],
                    ] as $i => [$beeld, $kleur, $naam, $sub])
                        <figure class="w-[84vw] max-w-[56rem] shrink-0 snap-center m-0">
                            <div class="rounded-xl overflow-hidden bg-white shadow-2xl">
                                <div class="flex items-center gap-2 px-5 py-3 border-b border-cacao/10 bg-white">
                                    <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                    <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                    <span class="w-3 h-3 rounded-full bg-cacao/15"></span>
                                    <span class="mx-auto w-full max-w-sm rounded-full border border-cacao/10 px-4 py-1 text-xs font-semibold text-cacao/45 text-center truncate">pizzeriasole.mijnpizzeria.nl</span>
                                    <span class="hidden sm:block w-16"></span>
                                </div>
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ asset('assets/site/' . $beeld) }}" alt="Template {{ $naam }}: {{ $sub }}" class="w-full h-full object-cover object-top" @if($i > 0) loading="lazy" @endif>
                                </div>
                            </div>
                            <figcaption class="mt-5 sm:mt-6 flex flex-wrap items-center justify-between gap-x-8 gap-y-4 text-white">
                                <div>
                                    <p class="font-display text-2xl sm:text-3xl">{{ $naam }} <span class="ml-2 align-middle text-sm font-body font-bold text-white/40">{{ $i + 1 }}/4</span></p>
                                    <p class="mt-1 text-sm font-semibold text-white/65">{{ $sub }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3.5">
                                    <a href="/onboarding" class="btn-primary !px-6 !py-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold">Gratis starten</a>
                                    <a href="/bestellen/pizzeriasole?thema={{ str_replace('.jpg', '', $beeld) }}&kleur={{ $kleur }}" target="_blank" rel="noopener" class="btn-tweede-donker !px-6 !py-[0.7rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold">Bekijk {{ $naam }} live</a>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- PRIJS EN VRAGEN --}}
    <section id="prijzen" class="scroll-mt-24 bg-white relative overflow-hidden">
        <div class="gloed w-[26rem] h-[26rem] bg-basil/10 top-20 -left-32" data-gloed aria-hidden="true"></div>
        <div class="max-w-6xl mx-auto px-5 py-24 sm:py-28 grid lg:grid-cols-2 gap-16 lg:gap-24 items-start relative">
            <div data-reveal>
                <h2 class="font-display text-4xl sm:text-5xl text-cacao">Een prijs, geen verrassingen</h2>
                <p class="mt-8"><span class="font-display text-6xl sm:text-7xl text-cacao">&euro; 24,95</span> <span class="text-lg font-semibold text-cacao/45">per maand</span></p>
                <p class="mt-3 text-base font-semibold text-basil">De eerste 30 dagen gratis, zonder betaalgegevens.</p>
                <ul class="mt-8 grid sm:grid-cols-2 gap-x-10 gap-y-3 text-[15px] font-semibold text-cacao/65" data-reveal-groep>
                    @foreach(['Eigen bestelpagina met jouw stijl', 'Onbeperkt bestellingen, 0% commissie', 'Spaarprogramma en klantaccounts', 'Pizza Coach met omzet-inzichten', 'Live bezorgstatus voor je klanten', 'Eigen domein koppelen', 'Maandelijks opzegbaar', 'Hulp van een echt mens'] as $punt)
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-basil shrink-0" aria-hidden="true"></i> {{ $punt }}</li>
                    @endforeach
                </ul>
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <a href="/onboarding" class="btn-primary !text-lg !px-8 !py-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Gratis starten</a>
                    <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="btn-tweede !text-lg !px-8 !py-[0.95rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Eerst even rondkijken</a>
                </div>
            </div>

            <div class="divide-y divide-cacao/10 border-t border-b border-cacao/10" data-reveal>
                @foreach([
                    ['Hoe werkt de gratis proefperiode?', 'Je krijgt 30 dagen om alles te proberen, zonder betaalgegevens vooraf. Bevalt het, dan start je het abonnement vanuit je dashboard. Bevalt het niet, dan verloopt je proef vanzelf en zit je nergens aan vast.'],
                    ['Rekenen jullie echt geen commissie?', 'Nee. Je betaalt alleen het vaste maandbedrag. Elke bestelling en elke euro omzet is volledig van jou. Alleen de gebruikelijke transactiekosten van de betaalprovider (Stripe) gelden, zoals bij elke online betaling.'],
                    ['Hoe ontvang ik het geld van bestellingen?', 'Klanten betalen online via Stripe met iDEAL, creditcard, Apple Pay of Google Pay. Het geld gaat rechtstreeks naar jouw zaak, niet eerst langs een platform.'],
                    ['Kan ik mijn eigen domeinnaam gebruiken?', 'Ja. Je start op zaaknaam.mijnpizzeria.nl en met een actief abonnement koppel je je eigen domein, bijvoorbeeld bestellen.jouwzaak.nl.'],
                    ['Heb ik technische kennis nodig?', 'Nee. Je stelt je menukaart, openingstijden en stijl zelf in via een overzichtelijk dashboard. Kom je er niet uit, dan helpen we je op weg.'],
                ] as [$vraag, $antwoord])
                    <details class="group py-5">
                        <summary class="flex items-center justify-between gap-6 cursor-pointer select-none font-display text-xl text-cacao list-none [&::-webkit-details-marker]:hidden focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-tomato rounded-sm">
                            {{ $vraag }}
                            <i class="fa-solid fa-plus text-sm text-cacao/40 shrink-0 transition-transform group-open:rotate-45" aria-hidden="true"></i>
                        </summary>
                        <p class="mt-3 pr-10 text-[15px] font-semibold text-cacao/55 leading-relaxed">{{ $antwoord }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SLOT: warme band met de laatste duw --}}
    <section class="korrel relative overflow-hidden" style="background:var(--color-tomato)">
        <div class="gloed w-[32rem] h-[32rem] bg-gold/30 -top-40 -right-32" data-gloed aria-hidden="true"></div>
        <div class="max-w-6xl mx-auto px-5 py-24 sm:py-28 flex flex-col lg:flex-row lg:items-center justify-between gap-10 relative">
            <div data-reveal>
                <h2 class="font-display text-4xl sm:text-5xl text-white">Vanavond nog online staan?</h2>
                <p class="mt-4 text-base font-semibold text-white/80 max-w-lg">Binnen tien minuten heb je je eigen bestelpagina. De eerste 30 dagen zijn gratis en je zit nergens aan vast.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 shrink-0" data-reveal>
                <a href="/onboarding" class="inline-block rounded-lg bg-white text-tomato text-lg font-bold px-8 py-4 shadow-lg hover:bg-crema transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Gratis starten</a>
                <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="btn-tweede-donker !text-lg !px-8 !py-[0.95rem] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Eerst even rondkijken</a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
    <script>
        /* Bij 'verminderde beweging' staat alles direct stil op zijn plek */
        const bewegingOk = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (bewegingOk) {
            gsap.registerPlugin(ScrollTrigger);

            /* Binnenkomer: de hero bouwt zich rustig op */
            gsap.from('[data-intro]', { y: 26, autoAlpha: 0, duration: .7, ease: 'power2.out', stagger: .09 });

            /* De pizza draait bijna onmerkbaar door */
            gsap.to('#pizzaDraai', { rotation: 360, duration: 240, repeat: -1, ease: 'none' });

            /* De pizza draait bijna onmerkbaar door: het beleg schuift langzaam langs de rand */
            gsap.to('#pizzaDraai', { rotation: 360, duration: 240, repeat: -1, ease: 'none' });

            /* Losse blokken faden in zodra ze in beeld komen */
            document.querySelectorAll('[data-reveal]').forEach((el) => {
                gsap.from(el, {
                    y: 24, autoAlpha: 0, duration: .6, ease: 'power2.out',
                    scrollTrigger: { trigger: el, start: 'top 90%', once: true },
                });
            });

            /* Groepen (grids, lijstjes) komen een voor een binnen */
            document.querySelectorAll('[data-reveal-groep]').forEach((groep) => {
                gsap.from(groep.children, {
                    y: 22, autoAlpha: 0, duration: .55, ease: 'power2.out', stagger: .08,
                    scrollTrigger: { trigger: groep, start: 'top 88%', once: true },
                });
            });

            /* De kleurgloed drijft langzaam mee met het scrollen */
            document.querySelectorAll('[data-gloed]').forEach((gloed) => {
                gsap.to(gloed, {
                    yPercent: 24, ease: 'none',
                    scrollTrigger: { trigger: gloed.parentElement, start: 'top bottom', end: 'bottom top', scrub: 1.4 },
                });
            });

            /* Productschermen zweven subtiel omhoog terwijl je scrolt */
            document.querySelectorAll('[data-zweef]').forEach((el) => {
                gsap.fromTo(el, { y: 34 }, {
                    y: -34, ease: 'none',
                    scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: 1.2 },
                });
            });

            /* De cijfers tellen op zodra ze in beeld komen */
            document.querySelectorAll('[data-tel]').forEach((el) => {
                const doel = parseFloat(el.dataset.tel);
                const dec = Number(el.dataset.dec ?? 0);
                const stand = { n: 0 };
                gsap.to(stand, {
                    n: doel, duration: 1.3, ease: 'power1.out',
                    onUpdate: () => {
                        el.textContent = stand.n.toLocaleString('nl-NL', { minimumFractionDigits: dec, maximumFractionDigits: dec });
                    },
                    scrollTrigger: { trigger: el, start: 'top 92%', once: true },
                });
            });
        }

        /* Carrousel: de schermen draaien als een cirkel rond, de voorste wisselt */
        const diaLijst = [...document.querySelectorAll('.car-dia')];
        if (diaLijst.length) {
            const posities = ['voor', 'rechts', 'achter', 'links'];
            const namen = ['Bestelpagina', 'Dashboard', 'Afrekenen met punten', 'Live bezorgstatus'];
            const stippen = [...document.querySelectorAll('.car-stip')];
            let voorste = 0;
            let timer = null;

            const zet = () => {
                diaLijst.forEach((dia, i) => { dia.dataset.pos = posities[(i - voorste + 4) % 4]; });
                stippen.forEach((stip, i) => stip.classList.toggle('aan', i === voorste));
                document.querySelector('#carrouselNaam').textContent = namen[voorste];
            };
            const kies = (i) => { voorste = (i + 4) % 4; zet(); };
            const start = () => { if (bewegingOk && !timer) timer = setInterval(() => kies(voorste + 1), 4200); };
            const stop = () => { clearInterval(timer); timer = null; };
            const herstart = () => { stop(); start(); };

            diaLijst.forEach((dia, i) => {
                dia.addEventListener('click', () => { if (dia.dataset.pos !== 'voor') { kies(i); herstart(); } });
                dia.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); kies(i); herstart(); }
                });
            });
            stippen.forEach((stip, i) => stip.addEventListener('click', () => { kies(i); herstart(); }));

            const kader = document.querySelector('#carrousel');
            kader.addEventListener('mouseenter', stop);
            kader.addEventListener('mouseleave', start);
            zet();
            start();
        }

        /* Templates: op grotere schermen wordt het verticale scrollen een horizontale rit
           langs de vier stijlen. Op mobiel en zonder beweging blijft het een swipe met snappunten. */
        const templatesRij = document.querySelector('#templatesRij');
        if (bewegingOk && templatesRij && window.matchMedia('(min-width: 64rem)').matches) {
            const rit = document.querySelector('#templatesRit');
            rit.classList.remove('overflow-x-auto', 'snap-x', 'snap-mandatory');
            rit.classList.add('overflow-hidden');
            gsap.to(templatesRij, {
                x: () => -(templatesRij.scrollWidth - window.innerWidth),
                ease: 'none',
                scrollTrigger: {
                    trigger: '#templates',
                    start: 'top top',
                    end: () => '+=' + (templatesRij.scrollWidth - window.innerWidth),
                    scrub: 1,
                    pin: true,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                },
            });
        }
    </script>
@endsection
