@extends('site.layout')

{{-- De homepagina: de hero uit shop-and-eat.html, de leestekst met de collage en het deel waar
     het vel donker kleurt. De vaste kop en de voet van de andere pagina's blijven hier weg.
     Alle teksten komen uit lang/{nl,en}/site.php; de taalkeuze staat in de hero. --}}
@section('titel', __('site.titel'))
@section('omschrijving', __('site.omschrijving'))
@section('html-klasse', '')
@section('kop')@endsection
@section('voet')@endsection

@section('inhoud')
    <section class="vlak slide hero" id="top">
        <video class="bgv" id="heroVideo" autoplay muted loop playsinline preload="metadata" poster="{{ asset('assets/site/hero-poster.jpg') }}" aria-hidden="true">
            <source src="{{ asset('assets/site/hero.mp4') }}" type="video/mp4">
        </video>
        <div class="shade"></div>

        <div class="top">
            <a href="/" aria-label="Shop &amp; Eat">@include('deel.merk', ['licht' => true])</a>
            <nav class="pillnav" aria-label="{{ __('site.nav.home') }}">
                <a class="aan" href="/">{{ __('site.nav.home') }}</a>
                <a href="#wat">{{ __('site.nav.functies') }}</a>
                <a href="#hoe">{{ __('site.nav.oplevert') }}</a>
                <a href="#prijzen">{{ __('site.nav.prijzen') }}</a>
                <a href="/blog">{{ __('site.nav.blog') }}</a>
                <a href="/onboarding">{{ __('site.nav.starten') }}</a>
            </nav>
            <div class="top-r">
                <nav class="taal" aria-label="{{ __('site.nav.taal') }}">
                    @foreach(\App\Http\Middleware\ZetTaal::TALEN as $taal)
                        <a href="{{ route('taal', $taal) }}" hreflang="{{ $taal }}" class="{{ app()->getLocale() === $taal ? 'aan' : '' }}">{{ strtoupper($taal) }}</a>
                    @endforeach
                </nav>
                <a href="/login" class="btn glass inlog" aria-label="{{ __('site.nav.inloggen') }}"><span class="tekst">{{ __('site.nav.inloggen') }}</span><i class="ar fa-solid fa-chevron-right tekst" aria-hidden="true"></i><i class="fa-solid fa-user icoon" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="hero-b">
            <div class="hero-t">
                <h1 class="in">{!! __('site.hero.kop') !!}</h1>
                <p class="in d1">{{ __('site.hero.tekst') }}</p>
            </div>
            <form class="form op-donker in d2" action="/onboarding" method="get">
                <div class="fh"><span>{{ __('site.hero.chip1') }}</span><span>{{ __('site.hero.chip2') }}</span><span>{{ __('site.hero.chip3') }}</span></div>
                <h3>{{ __('site.hero.vraag') }}</h3>
                <p>{{ __('site.hero.uitleg') }}</p>
                <div class="fld">
                    <label for="heroNaam">{{ __('site.hero.label') }}</label>
                    <input id="heroNaam" name="naam" type="text" maxlength="50" autocomplete="organization" placeholder="{{ __('site.hero.voorbeeld') }}">
                </div>
                <button type="submit" class="btn dark">{{ __('site.hero.knop') }} <i class="ar fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                <div class="feiten"><span>{{ __('site.hero.feit1') }}</span><span>{{ __('site.hero.feit2') }}</span><span>{{ __('site.hero.feit3') }}</span></div>
            </form>
        </div>
    </section>

    {{-- Onder de hero: één grote zin die per letter oplicht terwijl je 'm leest. Het vlak is
         hoger dan het scherm en de zin plakt in beeld, zodat het scrollen het lezen wordt. --}}
    <section class="vlak lees-vlak" id="wat">
        <div class="lees">
            <div class="lees-l">
                <span class="tag">{{ __('site.lees.tag') }}</span>
                <p class="lees-tekst" id="leesTekst">{!! __('site.lees.zin') !!}</p>
            </div>
            {{-- Drie foto's, losjes over elkaar en elk een tikje gedraaid; ze schuiven in eigen tempo mee met het scrollen.
                 Stockfoto's van Unsplash (Unsplash-licentie): bezorger door Lucian Alexe (afDu-GuxjjM),
                 keuken door Louis Hansel (v3OlBE6-fhU), friet door Emmy Smith (LEjEst7lLfU). --}}
            <div class="lees-fotos" aria-hidden="true">
                <div class="photo f1" data-diepte="70"><img src="{{ asset('assets/site/keuken.jpg') }}" alt="" loading="lazy"></div>
                <div class="photo f2" data-diepte="-50"><img src="{{ asset('assets/site/friet.jpg') }}" alt="" loading="lazy"></div>
                <div class="photo f3" data-diepte="110"><img src="{{ asset('assets/site/bezorger.jpg') }}" alt="" loading="lazy"></div>
            </div>
        </div>
    </section>

    {{-- Daaronder kleurt het vel zelf van licht naar zwart terwijl je verder scrolt (het script zet
         --t en --tt op <html>, geen zwart vlak). Net zo rustig als de leestekst: een kop, een zin,
         vier cijfers die één voor één oplopen. --}}
    <section class="donker-vlak" id="hoe">
        <div class="donker">
            {{-- Links twee foto's over elkaar, anders dan de collage erboven: donkere beelden die in het zwart opgaan.
                 Stockfoto's van Unsplash (Unsplash-licentie): grill door Caramel (JKJmdRDfPfk),
                 nachtbezorger door Rowan Freeman (clYlmCaQbzY). --}}
            <div class="donker-fotos" aria-hidden="true">
                <div class="photo g1" data-diepte="60"><img src="{{ asset('assets/site/grill.jpg') }}" alt="" loading="lazy"></div>
                <div class="photo g2" data-diepte="-70"><img src="{{ asset('assets/site/nacht.jpg') }}" alt="" loading="lazy"></div>
            </div>
            <div class="cijfers">
                <div class="cijfer" data-vanaf="0"><span class="n"><span data-tel="100" data-dec="0">0</span>%</span><span class="c">{{ __('site.donker.c1') }}</span></div>
                <div class="cijfer" data-vanaf=".15"><span class="n">&euro; <span data-tel="24.95" data-dec="2">0</span></span><span class="c">{{ __('site.donker.c2') }}</span></div>
                <div class="cijfer" data-vanaf=".3"><span class="n"><span data-tel="10" data-dec="0">0</span> {{ __('site.donker.min') }}</span><span class="c">{{ __('site.donker.c3') }}</span></div>
                <div class="cijfer" data-vanaf=".45"><span class="n"><span data-tel="30" data-dec="0">0</span> {{ __('site.donker.dagen') }}</span><span class="c">{{ __('site.donker.c4') }}</span></div>
            </div>
        </div>
    </section>

    {{-- Voor elke keuken: twee linten met keukens die zijwaarts meeschuiven met het scrollen, de ene naar
         links, de andere naar rechts, met kleine ronde foto's ertussen. Het vel is hier al donker. --}}
    <section class="keukens-vlak" id="keukens">
        <div class="keukens-kop">
            <div>
                <span class="tag">{{ __('site.keukens.tag') }}</span>
                <h2>{!! __('site.keukens.kop') !!}</h2>
            </div>
            <p class="lead">{{ __('site.keukens.tekst') }}</p>
        </div>
        @php
            $keukens = __('site.keukens.lijst');
            $rondjes = ['pepperoni.jpg', 'drank.jpg', 'dessert.jpg', 'slice.jpg', 'ingredienten.jpg'];
        @endphp
        {{-- Twee rijen, de tweede in omgekeerde volgorde en twee keer herhaald zodat er altijd genoeg lint is om te schuiven --}}
        @foreach([$keukens, array_reverse($keukens)] as $rij => $woorden)
            <div class="lint" aria-hidden="{{ $rij ? 'true' : 'false' }}">
                <div class="lint-rij">
                    @for($herhaling = 0; $herhaling < 2; $herhaling++)
                        @foreach($woorden as $i => $keuken)
                            <span class="woord">{{ $keuken }}</span>
                            @if($i % 3 === $rij)
                                <span class="photo rond"><img src="{{ asset('assets/eten/' . $rondjes[($i + $rij * 2) % count($rondjes)]) }}" alt="" loading="lazy"></span>
                            @endif
                        @endforeach
                    @endfor
                </div>
            </div>
        @endforeach
    </section>

    {{-- Alles erin: links de tekst, rechts een losse wolk van USP-kaartjes en twee reviews, elk een tikje
         gedraaid en in eigen tempo meeschuivend met het scrollen, zoals de foto's eerder. Nog op het
         donkere vel. De reviews zijn voorbeelden (met een zichtbaar label) tot er echte quotes zijn. --}}
    <section class="usps-vlak" id="alles">
        @php
            $usps = __('site.usps.lijst');
            /* De kring: acht kaartjes op een ellips om de tekst heen, elke 45 graden één. Ze komen uit het
               midden gevlogen terwijl je scrolt (het scherm staat dan vast) en landen op hun plek. Per kaartje
               de plek (x, y in procenten van het podium), de draaiing en de vertraging van het zweven. */
            $kring = [
                [0, 50,  8,  '-2deg', '0s'],
                [1, 76, 19,  '2deg',  '1.2s'],
                [6, 87, 50,  '1deg',  '2.4s'],
                [2, 76, 81,  '-1deg', '0.6s'],
                [3, 50, 90,  '2deg',  '1.8s'],
                [4, 24, 81,  '-2deg', '3s'],
                [7, 13, 50,  '-1deg', '0.9s'],
                [5, 24, 19,  '1deg',  '2.1s'],
            ];
        @endphp
        <div class="kring-podium">
        <div class="kring" id="kring">
            <div class="kring-tekst">
                <span class="tag">{{ __('site.usps.tag') }}</span>
                <h2>{!! __('site.usps.kop') !!}</h2>
                <p class="lead">{{ __('site.usps.tekst') }}</p>
            </div>
            @foreach($kring as [$i, $x, $y, $rot, $wacht])
                <div class="kaartje usp" style="--x:{{ $x }}%;--y0:{{ $y }}%;--rot:{{ $rot }};--wacht:{{ $wacht }}">
                    <i class="fa-solid {{ $usps[$i][0] }}" aria-hidden="true"></i>
                    <div><b>{{ $usps[$i][1] }}</b><span>{{ $usps[$i][2] }}</span></div>
                </div>
            @endforeach
        </div>
        </div>
    </section>

    {{-- Prijs en reviews: weer een witte kaart die plakt, zoals de leestekst. Terwijl de kaart in beeld
         schuift kleurt het vel erachter van zwart terug naar licht (het script zet --t en --tt via deze
         kaart). Links de prijs met wat erin zit (de vinkjes gaan één voor één aan), rechts drie reviews
         die één voor één binnenkomen. De reviews zijn voorbeelden met een zichtbaar label tot er echte
         quotes zijn. --}}
    <section class="vlak prijs-vlak" id="prijzen">
        <div class="prijs">
            <div class="prijs-l">
                <span class="tag">{{ __('site.prijs.tag') }}</span>
                <h2>{!! __('site.prijs.kop') !!}</h2>
                <div class="bedrag"><span class="n">&euro; {{ __('site.prijs.bedrag') }}</span><span class="c">{{ __('site.prijs.per') }}</span></div>
                <p class="status live"><i></i>{{ __('site.prijs.proef') }}</p>
                <ul class="vinkjes" id="vinkjes">
                    @foreach(__('site.prijs.punten') as $punt)
                        <li><i><span class="fa-solid fa-check" aria-hidden="true"></span></i>{{ $punt }}</li>
                    @endforeach
                </ul>
                <a href="/onboarding" class="btn dark">{{ __('site.prijs.knop') }} <i class="ar fa-solid fa-chevron-right" aria-hidden="true"></i></a>
            </div>
            <div class="reviews" id="reviews">
                @foreach(__('site.prijs.reviews') as $i => [$quote, $naam, $zaak])
                    <figure class="review" style="--rot:{{ ['-1.5deg', '1deg', '-0.5deg'][$i % 3] }}">
                        <span class="sterren" aria-label="5/5"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                        <blockquote>{{ $quote }}</blockquote>
                        <figcaption><b>{{ $naam }}</b><span>{{ $zaak }}</span><span class="tag">{{ __('site.prijs.voorbeeld') }}</span></figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Slot: op het inmiddels lichte vel veegt de kop van links naar rechts in beeld en staat het
         startformulier er nog één keer, nu op een witte kaart. Daaronder een kleine voet. --}}
    <section class="slot-vlak" id="slot">
        <div class="slot">
            <div class="slot-l">
                <span class="tag">{{ __('site.slot.tag') }}</span>
                <h2 class="veeg">{!! __('site.slot.kop') !!}</h2>
                <p class="lead">{{ __('site.slot.tekst') }}</p>
            </div>
            <form class="vlak slot-form" action="/onboarding" method="get">
                <div class="fh"><span class="tag">{{ __('site.hero.chip1') }}</span><span class="tag">{{ __('site.hero.chip2') }}</span><span class="tag">{{ __('site.hero.chip3') }}</span></div>
                <h3>{{ __('site.hero.vraag') }}</h3>
                <div class="fld">
                    <label for="slotNaam">{{ __('site.hero.label') }}</label>
                    <input id="slotNaam" name="naam" type="text" maxlength="50" autocomplete="organization" placeholder="{{ __('site.hero.voorbeeld') }}">
                </div>
                <button type="submit" class="btn dark breed">{{ __('site.hero.knop') }} <i class="ar fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                <p class="small">{{ __('site.hero.feit1') }} &middot; {{ __('site.hero.feit2') }} &middot; {{ __('site.hero.feit3') }}</p>
            </form>
        </div>
        {{-- De voet: merk en belofte, drie kolommen met links, de betaalmethoden, en onderin de regel met het jaar --}}
        <footer class="slot-voet">
            <div class="voet-merk">
                <a href="/" aria-label="Shop &amp; Eat">@include('deel.merk')</a>
                <p>{{ __('site.voet.tagline') }}</p>
                <nav class="taal licht" aria-label="{{ __('site.nav.taal') }}">
                    @foreach(\App\Http\Middleware\ZetTaal::TALEN as $taal)
                        <a href="{{ route('taal', $taal) }}" hreflang="{{ $taal }}" class="{{ app()->getLocale() === $taal ? 'aan' : '' }}">{{ strtoupper($taal) }}</a>
                    @endforeach
                </nav>
            </div>
            <nav class="voet-kolom" aria-label="{{ __('site.voet.platform') }}">
                <span class="label">{{ __('site.voet.platform') }}</span>
                <a href="#wat">{{ __('site.nav.functies') }}</a>
                <a href="#hoe">{{ __('site.nav.oplevert') }}</a>
                <a href="#keukens">{{ __('site.keukens.tag') }}</a>
                <a href="#alles">{{ __('site.usps.tag') }}</a>
                <a href="#prijzen">{{ __('site.nav.prijzen') }}</a>
            </nav>
            <nav class="voet-kolom" aria-label="{{ __('site.voet.zaak') }}">
                <span class="label">{{ __('site.voet.zaak') }}</span>
                <a href="/onboarding">{{ __('site.nav.starten') }}</a>
                <a href="/login">{{ __('site.nav.inloggen') }}</a>
                <a href="/blog">{{ __('site.nav.blog') }}</a>
            </nav>
            <div class="voet-kolom">
                <span class="label">{{ __('site.voet.betalen') }}</span>
                <span class="betaal" title="{{ __('site.slot.betaal') }}">
                    <i class="fa-brands fa-ideal" aria-hidden="true"></i>
                    <i class="fa-brands fa-cc-apple-pay" aria-hidden="true"></i>
                    <i class="fa-brands fa-google-pay" aria-hidden="true"></i>
                    <i class="fa-brands fa-cc-visa" aria-hidden="true"></i>
                    <i class="fa-brands fa-cc-mastercard" aria-hidden="true"></i>
                </span>
                <p>{{ __('site.voet.methodes') }}</p>
                <p class="status live"><i></i>{{ __('site.slot.betaal') }}</p>
            </div>
            <div class="voet-onder">
                <span>&copy; {{ date('Y') }} Shop &amp; Eat. {{ __('site.voet.rechten') }}</span>
                <span>{{ __('site.slot.gemaakt') }}</span>
            </div>
        </footer>
    </section>

@endsection

{{-- Buiten de smooth-wrapper, want vast op het scherm: zodra de hero uit beeld is, schuiven onderin twee
     knoppen het scherm in, vanuit elke sectie direct naar de onboarding of het inloggen. --}}
@section('buiten')
    {{-- Komt als een ronde schijf (pizza) het beeld in gedraaid en groeit dan uit tot de pil; weg is andersom --}}
    <div class="zweef-cta" id="zweefCta">
        <div class="knoppen">
            <a href="/onboarding" class="btn dark">{{ __('site.nav.starten') }} <i class="ar fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            <a href="/login" class="btn line">{{ __('site.nav.inloggen') }}</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollSmoother.min.js"></script>
    <script>
        (function () {
            const stilstaan = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const taal = document.documentElement.lang === 'en' ? 'en-GB' : 'nl-NL';
            const html = document.documentElement;

            /* Wie minder beweging wil, krijgt het stilstaande beeld */
            const heroVideo = document.querySelector('#heroVideo');
            if (heroVideo && stilstaan) {
                heroVideo.removeAttribute('autoplay');
                heroVideo.pause();
            }

            /* GSAP ScrollSmoother: de pagina glijdt. Alleen op een breed scherm met een muis en zonder
               'minder beweging'; op telefoons en tablets blijft het gewone scrollen. Omdat de smoother de
               inhoud verschuift werkt position: sticky niet meer, dus de drie vastgezette delen worden
               gepind met ScrollTrigger (html.smooth-aan zet de sticky-regels uit). */
            const smooth = window.gsap && window.ScrollSmoother && ! stilstaan && window.matchMedia('(min-width: 900px) and (hover: hover)').matches;
            let smoother = null;
            if (smooth) {
                gsap.registerPlugin(ScrollTrigger, ScrollSmoother);
                html.classList.add('smooth-aan');
                smoother = ScrollSmoother.create({ smooth: 1.1, effects: false, normalizeScroll: false });
                [['.lees-vlak', '.lees'], ['.donker-vlak', '.donker'], ['.usps-vlak', '.kring-podium'], ['.prijs-vlak', '.prijs']].forEach(([vlak, deel]) => {
                    ScrollTrigger.create({
                        trigger: vlak,
                        start: 'top top',
                        end: () => 'bottom bottom',
                        pin: deel,
                        pinSpacing: false,
                    });
                });
                /* Ankers in de navigatie: de smoother scrolt er zelf heen */
                document.querySelectorAll('a[href^="#"]').forEach((a) => a.addEventListener('click', (e) => {
                    const doel = document.querySelector(a.getAttribute('href'));
                    if (! doel) return;
                    e.preventDefault();
                    smoother.scrollTo(doel, true, 'top top');
                }));
                if (location.hash && document.querySelector(location.hash)) {
                    setTimeout(() => smoother.scrollTo(location.hash, false, 'top top'), 50);
                }
            }
            /* Alles wat met het scrollen meebeweegt hangt aan één haak: de smoother als die er is, anders het scroll-event */
            const bijScroll = (fn) => {
                if (smoother) { ScrollTrigger.create({ onUpdate: fn }); return; }
                window.addEventListener('scroll', () => requestAnimationFrame(fn), { passive: true });
            };

            /* De zwevende knoppen onderin: in beeld zodra de hero grotendeels voorbij is, ook zonder beweging */
            const hero = document.querySelector('.hero');
            const slot = document.querySelector('.slot-vlak');
            const prijsVlak = document.querySelector('.prijs-vlak');
            const zweefCta = document.querySelector('#zweefCta');
            /* In twee stappen: eerst draait een ronde schijf het beeld in (.rond), dan groeit die uit tot de
               pil met de knoppen (.aan). Weg is andersom. De breedte van de pil wordt gemeten, zodat de
               schijf vanuit het midden kan opengaan. */
            const knoppen = zweefCta.querySelector('.knoppen');
            const meetCta = () => zweefCta.style.setProperty('--w', (knoppen.offsetWidth + 12) + 'px');
            meetCta();
            window.addEventListener('resize', meetCta);
            let ctaStaat = false;
            let ctaTimer = null;
            const toonCta = (aan) => {
                if (aan === ctaStaat) return;
                ctaStaat = aan;
                clearTimeout(ctaTimer);
                if (aan) {
                    zweefCta.classList.add('rond');
                    ctaTimer = setTimeout(() => zweefCta.classList.add('aan'), 420);
                } else {
                    zweefCta.classList.remove('aan');
                    ctaTimer = setTimeout(() => zweefCta.classList.remove('rond'), 420);
                }
            };
            /* Niet op de hero en niet op het slot: daar staat het formulier zelf al */
            const zetCta = () => toonCta(hero.getBoundingClientRect().bottom < window.innerHeight * .35 && slot.getBoundingClientRect().top > window.innerHeight * .6);
            bijScroll(zetCta);
            zetCta();

            if (stilstaan) return;
            const klem = (v) => Math.min(1, Math.max(0, v));

            /* Leestekst: elke letter in een eigen span (woorden bij elkaar, zodat ze netjes afbreken),
               en de letters lichten één voor één op naarmate je door het vlak scrolt */
            const tekst = document.querySelector('#leesTekst');

            const splits = (el) => {
                [...el.childNodes].forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) { splits(node); return; }
                    if (node.nodeType !== Node.TEXT_NODE) return;
                    const stuk = document.createDocumentFragment();
                    node.textContent.split(/(\s+)/).forEach((deel) => {
                        if (! deel) return;
                        if (/^\s+$/.test(deel)) { stuk.appendChild(document.createTextNode(' ')); return; }
                        const woord = document.createElement('span');
                        woord.className = 'w';
                        [...deel].forEach((letter) => {
                            const l = document.createElement('span');
                            l.className = 'l';
                            l.textContent = letter;
                            woord.appendChild(l);
                        });
                        stuk.appendChild(woord);
                    });
                    node.replaceWith(stuk);
                });
            };
            splits(tekst);

            const letters = [...tekst.querySelectorAll('.l')];
            const vlak = tekst.closest('.lees-vlak');
            const fotos = [...vlak.querySelectorAll('.lees-fotos .photo')];
            const venster = 6;
            let vorige = -1;
            const zet = () => {
                const r = vlak.getBoundingClientRect();
                const vh = window.innerHeight;
                /* 0 zodra de bovenkant van het vlak op 65% van het scherm staat, 1 als de onderkant onderin staat */
                const p = klem((vh * .65 - r.top) / (r.height - vh * .35));
                if (p === vorige) return;
                vorige = p;
                const front = p * (letters.length + venster);
                letters.forEach((l, i) => {
                    const o = klem((front - i) / venster);
                    l.style.opacity = (.16 + .84 * o).toFixed(3);
                });
                /* De foto's schuiven elk in eigen tempo: diepte in px, van +helft naar -helft */
                fotos.forEach((f) => f.style.setProperty('--y', ((.5 - p) * f.dataset.diepte).toFixed(1) + 'px'));
            };

            /* Het vel kleurt om: --t (achtergrond) en --tt (tekst) op <html> lopen van 0 naar 1 zodra het
               donkere deel in beeld komt; --q loopt door het plakkende deel en laat de cijfers oplopen */
            const donker = document.querySelector('.donker-vlak');
            const cijfers = [...donker.querySelectorAll('.cijfer')];
            const donkerFotos = [...donker.querySelectorAll('.donker-fotos .photo')];
            const zetDonker = () => {
                const r = donker.getBoundingClientRect();
                const vh = window.innerHeight;
                const ruw = klem((vh * .9 - r.top) / (vh * .6));
                let t = klem(ruw * 1.5);                /* de achtergrond is op 2/3 al zwart */
                let tt = klem((ruw - .3) / .5);         /* de tekst klapt daarna om naar wit */
                /* Bij het slot gaat het weer terug: het vel eerst licht, de tekst klapt daarna om naar inkt */
                const rs = slot.getBoundingClientRect();
                /* Terug naar licht zodra de witte prijskaart in beeld schuift: begint als de bovenkant
                   op 60% van het scherm staat, klaar als de kaart bovenaan staat */
                const rk = prijsVlak.getBoundingClientRect();
                const terug = klem((vh * .6 - rk.top) / (vh * .6));
                t = Math.min(t, 1 - klem(terug * 1.5));
                tt = Math.min(tt, 1 - klem((terug - .3) / .5));
                slot.style.setProperty('--w', klem((vh * .8 - rs.top) / (vh * .45)).toFixed(3));
                const q = klem((vh * .4 - r.top) / (r.height - vh * .3));
                html.style.setProperty('--t', t.toFixed(3));
                html.style.setProperty('--tt', tt.toFixed(3));
                donker.style.setProperty('--q', q.toFixed(3));
                donkerFotos.forEach((f) => f.style.setProperty('--y', ((.5 - q) * f.dataset.diepte).toFixed(1) + 'px'));
                /* Elk cijfer begint op zijn eigen moment (data-vanaf) en telt in een stuk van de scroll op */
                cijfers.forEach((cijfer) => {
                    const u = klem((q - parseFloat(cijfer.dataset.vanaf)) / .28);
                    cijfer.style.opacity = (.22 + .78 * u).toFixed(3);
                    const el = cijfer.querySelector('[data-tel]');
                    const dec = Number(el.dataset.dec || 0);
                    el.textContent = (parseFloat(el.dataset.tel) * u).toLocaleString(taal, { minimumFractionDigits: dec, maximumFractionDigits: dec });
                });
            };

            /* De linten schuiven zijwaarts mee met het scrollen: de bovenste naar links, de onderste naar rechts */
            const keukens = document.querySelector('.keukens-vlak');
            const linten = [...keukens.querySelectorAll('.lint-rij')];
            const zetLinten = () => {
                const r = keukens.getBoundingClientRect();
                const vh = window.innerHeight;
                const p = klem((vh - r.top) / (vh + r.height));
                linten.forEach((lint, i) => {
                    const x = i === 0 ? -p * 35 : -35 + p * 35;
                    lint.style.transform = `translate3d(${x.toFixed(2)}%, 0, 0)`;
                });
            };

            /* De kring: het scherm staat vast en terwijl je scrolt komen de kaartjes één voor één uit het
               midden gevlogen en landen op hun plek (--s loopt van 0 naar 1 met een klein doorschieten,
               --o is de doorzichtigheid). De tekst staat er meteen. */
            const kringVlak = document.querySelector('.usps-vlak');
            const kaartjes = [...kringVlak.querySelectorAll('.kaartje')];
            const landen = (x) => 1 + 2.3 * Math.pow(x - 1, 3) + 1.3 * Math.pow(x - 1, 2);
            const zetKring = () => {
                const r = kringVlak.getBoundingClientRect();
                const vh = window.innerHeight;
                const q = klem(-r.top / (r.height - vh));
                kaartjes.forEach((k, i) => {
                    const x = klem((q - .02 - i * .06) / .5);
                    k.style.setProperty('--s', landen(x).toFixed(3));
                    k.style.setProperty('--o', klem(x * 1.6).toFixed(3));
                });
            };

            /* Prijs en reviews: terwijl de kaart plakt gaan de vinkjes één voor één aan en komen de reviews één voor één binnen */
            const vinkjes = [...prijsVlak.querySelectorAll('.vinkjes li')];
            const reviews = [...prijsVlak.querySelectorAll('.review')];
            const zetPrijs = () => {
                const r = prijsVlak.getBoundingClientRect();
                const vh = window.innerHeight;
                const q = klem((vh * .65 - r.top) / (r.height - vh * .35));
                vinkjes.forEach((li, i) => li.classList.toggle('aan', q >= .04 + i * .07));
                reviews.forEach((el, i) => el.classList.toggle('aan', q >= .1 + i * .24));
            };

            const alles = () => { zet(); zetDonker(); zetLinten(); zetKring(); zetPrijs(); };
            bijScroll(alles);
            window.addEventListener('resize', alles);
            alles();
            if (smoother) requestAnimationFrame(alles);
        })();
    </script>
@endsection
