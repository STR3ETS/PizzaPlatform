<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Je bestelling bij {{ $naam }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --font-sans: "DM Sans", sans-serif;
            --color-primair: {{ $palet['primair'] }};
            --color-primair-donker: {{ $palet['primairDonker'] }};
            --color-secundair: {{ $palet['secundair'] }};
            --color-secundair-licht: {{ $palet['secundairLicht'] }};
            --color-wit-warm: {{ $palet['witWarm'] }};
        }
        body { font-family: var(--font-sans); } h1, h2 { font-family: "Fraunces", Georgia, serif; font-weight: 500; }
        .route-pin {
            width: 2.75rem; height: 2.75rem; border-radius: 9999px;
            background: #fff; border: 3px solid var(--color-primair);
            display: grid; place-items: center; color: var(--color-primair);
            font-size: 1.05rem; box-shadow: 0 6px 16px rgb(0 0 0 / .25);
        }
    </style>
</head>
<body class="bg-wit-warm min-h-screen">
    {{-- Geen balk: een rustige terug-link met de paginatitel in serif --}}
    <div class="max-w-5xl mx-auto px-6 pt-10">
        <a href="{{ $basis }}" class="inline-flex items-center gap-2 text-sm font-semibold text-secundair/60 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors">
            <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i> Terug naar het menu
        </a>
        <h1 class="text-4xl text-secundair mt-4">Je bestelling</h1>
    </div>

    {{-- Geen bestelling bekend --}}
    <main id="leegScherm" class="hidden max-w-5xl mx-auto px-6 py-16 text-center">
        <i class="fa-solid fa-receipt text-3xl text-secundair/25" aria-hidden="true"></i>
        <p class="mt-3 font-bold text-secundair">We konden geen bestelling vinden</p>
        <a href="{{ $basis }}" class="mt-4 inline-block px-6 py-3 rounded-sm bg-primair font-bold text-white hover:bg-primair-donker transition-colors">Naar het menu</a>
    </main>

    <main id="status" class="max-w-5xl mx-auto px-6 py-8 grid lg:grid-cols-[1fr_24rem] gap-6 items-start">
        <div class="space-y-6 min-w-0">
            {{-- Bevestiging en geschatte tijd --}}
            <section class="bg-white rounded-sm border border-black/10 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 shrink-0 rounded-full bg-primair grid place-items-center">
                        <i class="fa-solid fa-check text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 id="stTitel" class="text-xl font-extrabold text-secundair">Bedankt, je bestelling is geplaatst!</h1>
                        <p id="stNummer" class="mt-0.5 text-sm font-semibold text-secundair/55"></p>
                    </div>
                </div>
                <div class="mt-5 rounded-sm bg-wit-warm px-5 py-4 flex items-center gap-4">
                    <i class="fa-regular fa-clock text-2xl text-primair" aria-hidden="true"></i>
                    <div>
                        <p id="stEtaLabel" class="text-xs font-bold text-secundair/50 uppercase tracking-wide">Geschatte bezorgtijd</p>
                        <p id="stEta" class="text-2xl font-extrabold text-secundair"></p>
                        <p id="stEtaHint" class="mt-0.5 text-xs font-semibold text-secundair/50">De zaak bevestigt zo de definitieve tijd.</p>
                    </div>
                </div>

                {{-- Statusstappen: betaald is binnen, de zaak moet nog bevestigen --}}
                <div id="stStappen" class="mt-6 grid grid-cols-5 gap-2"></div>

                {{-- Vriendelijk bericht bij de huidige status --}}
                <div id="stBericht" class="mt-6 flex items-start gap-4 rounded-sm bg-wit-warm px-5 py-4">
                    <div class="w-10 h-10 shrink-0 rounded-full bg-primair/15 grid place-items-center">
                        <i id="stBerichtIcoon" class="fa-solid fa-paper-plane text-primair" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0">
                        <p id="stBerichtTitel" class="font-extrabold text-secundair"></p>
                        <p id="stBerichtTekst" class="mt-0.5 text-sm font-semibold text-secundair/60"></p>
                    </div>
                </div>
            </section>

            {{-- Route van de zaak naar het bezorgadres --}}
            <section id="routeKaart" class="bg-white rounded-sm border border-black/10 p-6">
                <h2 id="routeTitel" class="text-lg font-extrabold text-secundair">De route van je bezorger</h2>
                <div id="routeMap" class="mt-4 h-96 rounded-sm border border-black/10 z-0" style="background:var(--color-wit-warm)"></div>
                <div class="mt-4 grid sm:grid-cols-2 gap-3">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-store text-primair mt-0.5" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-secundair/45 uppercase tracking-wide">Vanaf</p>
                            <p class="text-sm font-bold text-secundair">{{ $naam }}</p>
                            <p class="text-xs font-semibold text-secundair/55">{{ $adresZaak }}</p>
                        </div>
                    </div>
                    <div id="routeNaar" class="flex items-start gap-3">
                        <i class="fa-solid fa-house text-primair mt-0.5" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-secundair/45 uppercase tracking-wide">Naar</p>
                            <p id="stNaarNaam" class="text-sm font-bold text-secundair"></p>
                            <p id="stAdres" class="text-xs font-semibold text-secundair/55"></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Wat je besteld hebt --}}
        <aside class="lg:sticky lg:top-8 space-y-6">
            <div class="bg-white rounded-sm border border-black/10 p-6">
            <img src="{{ asset('assets/eten/oven.jpg') }}" alt="" class="-mx-6 -mt-6 mb-5 w-[calc(100%+3rem)] max-w-none h-28 object-cover rounded-t-sm">
            <h2 class="text-lg font-extrabold text-secundair">Je bestelling</h2>
            <div class="mt-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-sm overflow-hidden border border-black/10 bg-wit-warm grid place-items-center shrink-0">
                    @if($logo)
                        <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-pizza-slice text-primair" aria-hidden="true"></i>
                    @endif
                </div>
                <p class="flex-1 min-w-0 text-sm font-bold text-secundair truncate">{{ $naam }}</p>
            </div>
            <div id="stItems" class="mt-4 space-y-3"></div>
            <div class="mt-5 pt-4 border-t border-black/10 space-y-1.5 text-sm font-semibold text-secundair/70">
                <div class="flex items-center justify-between gap-3"><span>Subtotaal</span><span id="stSubtotaal"></span></div>
                <div id="stBezorgRij" class="flex items-center justify-between gap-3"><span>Bezorgkosten</span><span id="stBezorg"></span></div>
                <div id="stFooiRij" class="flex items-center justify-between gap-3" style="display:none"><span>Fooi</span><span id="stFooi"></span></div>
                <div id="stKortingRij" class="flex items-center justify-between gap-3" style="display:none"><span>Spaarpunten korting</span><span id="stKorting"></span></div>
            </div>
            <div class="mt-3 flex items-center justify-between font-extrabold text-secundair">
                <span>Totaal</span>
                <span id="stTotaal"></span>
            </div>
            <p id="stPunten" style="display:none" class="mt-3 flex items-center gap-2 text-sm font-bold text-secundair">
                <i class="fa-solid fa-star text-primair" aria-hidden="true"></i>
                <span id="stPuntenTekst"></span>
            </p>
            <div id="stOpmWrap" class="hidden mt-4 rounded-sm bg-wit-warm px-4 py-3">
                <p class="text-xs font-bold text-secundair/45 uppercase tracking-wide">Opmerking</p>
                <p id="stOpm" class="mt-0.5 text-sm font-semibold text-secundair/70 italic"></p>
            </div>
            </div>

            {{-- Hulp nodig: rechtstreeks contact met de zaak --}}
            <div class="bg-white rounded-sm border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Heb je een probleem?</h2>
                <p class="mt-1 text-sm font-semibold text-secundair/60">Neem gerust contact op met {{ $naam }}, ze helpen je graag verder.</p>
                <div class="mt-4 space-y-3">
                    @if($telefoonZaak)
                        <a href="tel:{{ preg_replace('/\s+/', '', $telefoonZaak) }}" class="flex items-center gap-3 rounded-sm border border-black/10 px-4 py-3 hover:border-primair/50 transition-colors">
                            <i class="fa-solid fa-phone text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0 text-sm font-bold text-secundair">{{ $telefoonZaak }}</span>
                            <i class="fa-solid fa-chevron-right text-secundair/30" aria-hidden="true"></i>
                        </a>
                    @endif
                    @if($mailZaak)
                        <a href="mailto:{{ $mailZaak }}" class="flex items-center gap-3 rounded-sm border border-black/10 px-4 py-3 hover:border-primair/50 transition-colors">
                            <i class="fa-solid fa-envelope text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0 text-sm font-bold text-secundair truncate">{{ $mailZaak }}</span>
                            <i class="fa-solid fa-chevron-right text-secundair/30" aria-hidden="true"></i>
                        </a>
                    @endif
                    @if($adresZaak)
                        <div class="flex items-center gap-3 rounded-sm border border-black/10 px-4 py-3">
                            <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0 text-sm font-bold text-secundair">{{ $adresZaak }}</span>
                        </div>
                    @endif
                </div>
                <p class="mt-3 text-xs font-semibold text-secundair/45">Houd je bestelnummer bij de hand, dan is het zo geregeld.</p>
            </div>
        </aside>
    </main>

    <script>
        const T1_BEZORG = @json($bezorg);
        const T1_BASIS = @json($basis);
        const OPSLAG = @json('t1_' . $slug);
        /* Je bent hier omdat de bestelling rond is: het mandje kan leeg */
        localStorage.removeItem(`${OPSLAG}_mand`);
        const FOTOS = @json($fotos ?? []);
        const PALET = @json($palet);
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const euro = (centen) => '€ ' + (centen / 100).toFixed(2).replace('.', ',');

        const TOKEN = @json($token);

        let bestelling = null;
        try { bestelling = JSON.parse(localStorage.getItem(`${OPSLAG}_bestelling`)); } catch { bestelling = null; }

        /* Voorbeeld-modus voor het live voorbeeld in het dashboard: demo-bestelling tonen */
        const VOORBEELD = @json($voorbeeldBestelling ?? null);
        if (VOORBEELD) bestelling = VOORBEELD;

        (async () => {
        /* Met een token in de URL kun je altijd terug naar deze bestelling, ook zonder lokale opslag */
        if (TOKEN && (!bestelling || bestelling.token !== TOKEN)) {
            try {
                const res = await fetch(`${T1_BASIS}/bestelling/data?token=${encodeURIComponent(TOKEN)}`, { headers: { 'Accept': 'application/json' } });
                bestelling = res.ok ? await res.json() : null;
            } catch { bestelling = null; }
        }

        if (!bestelling || !Array.isArray(bestelling.items) || !bestelling.items.length) {
            document.querySelector('#leegScherm').classList.remove('hidden');
            document.querySelector('#status').classList.add('hidden');
        } else {
            const bezorgen = bestelling.type !== 'afhalen';
            const regelPrijs = (regel) => (regel.prijsCenten + regel.opties.reduce((som, o) => som + o.prijs, 0)) * regel.aantal;

            /* Kop en geschatte tijd */
            const voornaam = String(bestelling.klant?.naam || '').split(' ')[0];
            document.querySelector('#stTitel').textContent = voornaam
                ? `Bedankt ${voornaam}, je bestelling is geplaatst!`
                : 'Bedankt, je bestelling is geplaatst!';
            document.querySelector('#stNummer').textContent = `Bestelnummer #${bestelling.nummer}`;
            document.querySelector('#stEtaLabel').textContent = bezorgen ? 'Geschatte bezorgtijd' : 'Af te halen rond';
            if (bestelling.tijd && bestelling.tijd !== 'Zo snel mogelijk') {
                document.querySelector('#stEta').textContent = bestelling.tijd;
            } else {
                const eta = new Date((bestelling.geplaatst || Date.now()) + 35 * 60000);
                document.querySelector('#stEta').textContent = `Rond ${String(eta.getHours()).padStart(2, '0')}:${String(eta.getMinutes()).padStart(2, '0')}`;
            }

            /* Statusstappen, zelfde volgorde als het dashboard: betaald is klaar, bevestigen doet de zaak */
            const stappen = bezorgen
                ? [['fa-euro-sign', 'Betaald'], ['fa-thumbs-up', 'Bevestigd'], ['fa-utensils', 'Wordt bereid'], ['fa-motorcycle', 'Onderweg'], ['fa-flag-checkered', 'Bezorgd']]
                : [['fa-euro-sign', 'Betaald'], ['fa-thumbs-up', 'Bevestigd'], ['fa-utensils', 'Wordt bereid'], ['fa-bag-shopping', 'Af te halen'], ['fa-flag-checkered', 'Afgehaald']];

            function renderStappen(idx) {
                document.querySelector('#stStappen').innerHTML = stappen.map(([icoon, label], i) => `
                    <div class="text-center">
                        <div class="mx-auto w-10 h-10 rounded-full grid place-items-center ${i <= idx ? 'bg-primair text-white' : 'bg-black/5 text-secundair/35'}">
                            <i class="fa-solid ${i < idx ? 'fa-check' : icoon}" aria-hidden="true"></i>
                        </div>
                        <p class="mt-1.5 text-[11px] font-bold ${i <= idx ? 'text-secundair' : 'text-secundair/40'}">${label}</p>
                        <div class="mt-2 h-1 rounded-full ${i <= idx ? 'bg-primair' : 'bg-black/10'}"></div>
                    </div>`).join('');
            }
            renderStappen(0);

            /* Live meebewegen met de status in het dashboard */
            const ZAAK = @json($naam);
            const STATUS_INDEX = { nieuw: 0, geaccepteerd: 1, bereiden: 2, oven: 2, onderweg: 3, bezorgd: 4 };
            const BERICHTEN = bezorgen
                ? {
                    nieuw: ['fa-paper-plane', 'Je betaling is binnen!', `We hebben je bestelling doorgestuurd naar ${ZAAK}. Zodra de zaak bevestigt, zie je hier de definitieve bezorgtijd.`],
                    geaccepteerd: ['fa-thumbs-up', `${ZAAK} gaat voor je aan de slag!`, 'Je bestelling is bevestigd. De keuken begint er zo aan.'],
                    bereiden: ['fa-fire-burner', 'Er wordt voor je gekookt!', 'De keuken is nu met je bestelling bezig. Nog even geduld, het komt eraan.'],
                    oven: ['fa-fire-burner', 'Er wordt voor je gekookt!', 'De keuken is nu met je bestelling bezig. Nog even geduld, het komt eraan.'],
                    onderweg: ['fa-motorcycle', 'Je eten komt eraan!', `De bezorger is onderweg naar ${bestelling.adres || 'je adres'}. Houd je telefoon in de buurt.`],
                    bezorgd: ['fa-face-smile', 'Eet smakelijk!', `Je bestelling is bezorgd. Bedankt voor je bestelling bij ${ZAAK}.`],
                }
                : {
                    nieuw: ['fa-paper-plane', 'Je betaling is binnen!', `We hebben je bestelling doorgestuurd naar ${ZAAK}. Zodra de zaak bevestigt, zie je hier hoe laat je kunt afhalen.`],
                    geaccepteerd: ['fa-thumbs-up', `${ZAAK} gaat voor je aan de slag!`, 'Je bestelling is bevestigd. De keuken begint er zo aan.'],
                    bereiden: ['fa-fire-burner', 'Er wordt voor je gekookt!', 'De keuken is nu met je bestelling bezig. Nog even geduld.'],
                    oven: ['fa-fire-burner', 'Er wordt voor je gekookt!', 'De keuken is nu met je bestelling bezig. Nog even geduld.'],
                    onderweg: ['fa-bag-shopping', 'Je bestelling staat klaar!', `Kom maar langs bij ${ZAAK}, je bestelling ligt klaar op de balie.`],
                    bezorgd: ['fa-face-smile', 'Eet smakelijk!', `Bedankt voor je bestelling bij ${ZAAK}. Tot de volgende keer!`],
                };

            function toonBericht(status) {
                const [icoon, titel, tekst] = BERICHTEN[status] || BERICHTEN.nieuw;
                document.querySelector('#stBerichtIcoon').className = `fa-solid ${icoon} text-primair`;
                document.querySelector('#stBerichtTitel').textContent = titel;
                document.querySelector('#stBerichtTekst').textContent = tekst;
                /* Het kleine hintje onder de tijd is alleen nodig zolang de zaak nog niet bevestigd heeft */
                document.querySelector('#stEtaHint').style.display = status === 'nieuw' ? '' : 'none';
            }
            toonBericht('nieuw');

            async function haalStatus() {
                try {
                    const vraag = bestelling.token ? `token=${encodeURIComponent(bestelling.token)}` : `nummer=${bestelling.nummer}`;
                    const res = await fetch(`${T1_BASIS}/bestelling/status?${vraag}`, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    renderStappen(STATUS_INDEX[data.status] ?? 0);
                    if (data.status === 'bezorgd') {
                        // Klaar: toon wanneer de bestelling bezorgd of afgehaald is
                        document.querySelector('#stEtaLabel').textContent = bezorgen ? 'Bezorgd om' : 'Afgehaald om';
                        if (data.bezorgdOm) document.querySelector('#stEta').textContent = data.bezorgdOm;
                    } else if (data.rond) {
                        document.querySelector('#stEta').textContent = `Rond ${data.rond}`;
                    }
                    toonBericht(data.status);
                    if (data.status === 'bezorgd') clearInterval(statusTimer);
                } catch { /* even geen verbinding: volgende poging pakt het op */ }
            }
            const statusTimer = VOORBEELD ? null : setInterval(haalStatus, 5000);
            if (VOORBEELD) { renderStappen(2); toonBericht('bereiden'); } else { haalStatus(); }

            /* Besteloverzicht */
            document.querySelector('#stItems').innerHTML = bestelling.items.map((regel) => {
                const foto = FOTOS[regel.naam];
                return `
                <div class="flex items-start gap-3 text-sm">
                    ${foto ? `<img src="${esc(foto)}" alt="" class="w-12 h-12 rounded-sm object-cover shrink-0">` : ''}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <p class="flex-1 min-w-0 font-bold text-secundair">${regel.aantal} x ${esc(regel.naam)}</p>
                            <p class="shrink-0 font-bold text-secundair">${euro(regelPrijs(regel))}</p>
                        </div>
                        ${regel.opties.map((o) => `<p class="text-xs font-semibold text-secundair/50">${esc(o.naam)}</p>`).join('')}
                        ${regel.opmerking ? `<p class="text-xs font-semibold italic text-secundair/40">"${esc(regel.opmerking)}"</p>` : ''}
                    </div>
                </div>`;
            }).join('');
            document.querySelector('#stSubtotaal').textContent = euro(bestelling.subtotaal || 0);
            document.querySelector('#stBezorgRij').style.display = bezorgen ? '' : 'none';
            document.querySelector('#stBezorg').textContent = euro(bestelling.kosten || 0);
            document.querySelector('#stFooiRij').style.display = bestelling.fooi > 0 ? '' : 'none';
            document.querySelector('#stFooi').textContent = euro(bestelling.fooi || 0);
            document.querySelector('#stKortingRij').style.display = bestelling.korting > 0 ? '' : 'none';
            document.querySelector('#stKorting').textContent = '- ' + euro(bestelling.korting || 0);
            if (bestelling.punten > 0) {
                document.querySelector('#stPunten').style.display = '';
                document.querySelector('#stPuntenTekst').textContent = `Je hebt ${bestelling.punten} ${bestelling.punten === 1 ? 'punt' : 'punten'} gespaard met deze bestelling`;
            }
            document.querySelector('#stTotaal').textContent = euro(bestelling.totaal || 0);
            if (bestelling.opmerking) {
                document.querySelector('#stOpmWrap').classList.remove('hidden');
                document.querySelector('#stOpm').textContent = `"${bestelling.opmerking}"`;
            }
            document.querySelector('#stNaarNaam').textContent = bestelling.klant?.naam || '';
            document.querySelector('#stAdres').textContent = bestelling.adres || '';

            /* De routekaart: pin bij de zaak en bij het bezorgadres, met de route ertussen */
            if (!bezorgen) {
                document.querySelector('#routeTitel').textContent = 'Hier haal je je bestelling af';
                document.querySelector('#routeNaar').classList.add('hidden');
            }

            const pin = (icoon) => L.divIcon({
                className: 'route-pin-wrap',
                html: `<div class="route-pin"><i class="fa-solid ${icoon}" aria-hidden="true"></i></div>`,
                iconSize: [44, 44],
                iconAnchor: [22, 22],
            });

            if (typeof L !== 'undefined' && T1_BEZORG.lat !== null) {
                const kaart = L.map('routeMap', { scrollWheelZoom: false, attributionControl: false });
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 }).addTo(kaart);
                const zaak = [T1_BEZORG.lat, T1_BEZORG.lng];
                L.marker(zaak, { icon: pin('fa-store') }).addTo(kaart);

                if (bezorgen && bestelling.lat !== null && bestelling.lng !== null) {
                    const klant = [bestelling.lat, bestelling.lng];
                    L.marker(klant, { icon: pin('fa-house') }).addTo(kaart);
                    kaart.fitBounds(L.latLngBounds([zaak, klant]).pad(0.25));

                    /* Echte rijroute via OSRM; lukt dat niet, dan een gestippelde lijn */
                    fetch(`https://router.project-osrm.org/route/v1/driving/${zaak[1]},${zaak[0]};${klant[1]},${klant[0]}?overview=full&geometries=geojson`)
                        .then((res) => (res.ok ? res.json() : Promise.reject()))
                        .then((data) => {
                            const punten = data.routes?.[0]?.geometry?.coordinates;
                            if (!punten) throw new Error('geen route');
                            L.polyline(punten.map(([lng, lat]) => [lat, lng]), { color: PALET.primair, weight: 5, opacity: .85 }).addTo(kaart);
                        })
                        .catch(() => {
                            L.polyline([zaak, klant], { color: PALET.primair, weight: 4, dashArray: '8 10', opacity: .8 }).addTo(kaart);
                        });
                } else {
                    kaart.setView(zaak, 15);
                }
            }
        }
        })();
    </script>
</body>
</html>
