<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Afrekenen bij {{ $naam }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --font-sans: "Plus Jakarta Sans", sans-serif;
            --color-primair: {{ $palet['primair'] }};
            --color-primair-donker: {{ $palet['primairDonker'] }};
            --color-secundair: {{ $palet['secundair'] }};
            --color-secundair-licht: {{ $palet['secundairLicht'] }};
            --color-wit-warm: {{ $palet['witWarm'] }};
        }
        body { font-family: var(--font-sans); }
    </style>
</head>
<body class="bg-wit-warm min-h-screen">
    {{-- Witte app-balk: ronde terugknop, logo en naam zoals in de shop --}}
    <header class="sticky top-0 z-40 bg-white border-b border-black/5">
        <div class="max-w-5xl mx-auto px-6 py-3 flex items-center gap-3">
            <a href="{{ $basis }}" aria-label="Terug naar het menu"
                class="inline-grid w-10 h-10 rounded-full place-items-center bg-wit-warm text-secundair hover:bg-primair hover:text-white transition-colors shrink-0">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </a>
            <div class="w-9 h-9 rounded-full overflow-hidden bg-wit-warm grid place-items-center shrink-0">
                @if($logo)
                    <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-pizza-slice text-primair text-sm" aria-hidden="true"></i>
                @endif
            </div>
            <p class="flex-1 min-w-0 text-sm font-bold text-secundair truncate">{{ $naam }}</p>
            <p class="font-extrabold text-secundair shrink-0">Afrekenen</p>
        </div>
    </header>

    {{-- Lege winkelmand: terug naar het menu --}}
    <main id="leegScherm" class="hidden max-w-5xl mx-auto px-6 py-16 text-center">
        <i class="fa-solid fa-basket-shopping text-3xl text-secundair/25" aria-hidden="true"></i>
        <p class="mt-3 font-bold text-secundair">Je winkelmand is leeg</p>
        <a href="{{ $basis }}" class="mt-4 inline-block px-6 py-3 rounded-full bg-primair font-bold text-white hover:bg-primair-donker transition-colors">Terug naar het menu</a>
    </main>

    <main id="afreken" class="max-w-5xl mx-auto px-6 py-8 grid lg:grid-cols-[1fr_24rem] gap-6 items-start">
        <div class="space-y-6 min-w-0">
            {{-- Bestelgegevens --}}
            <section class="bg-white rounded-3xl border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Bestelgegevens</h2>
                <div id="klantRegel" style="display:none" class="mt-3 flex items-center justify-between gap-3 rounded-xl bg-wit-warm px-4 py-2.5">
                    <p id="klantRegelNaam" class="text-sm font-bold text-secundair truncate"></p>
                    <form method="POST" action="{{ $basis }}/klant-uitloggen">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-secundair/50 underline underline-offset-2 hover:text-secundair transition-colors cursor-pointer">Uitloggen</button>
                    </form>
                </div>
                <div class="mt-2 divide-y divide-black/5">
                    <div class="flex items-start gap-4 py-4">
                        <i class="fa-regular fa-user text-primair mt-2.5" aria-hidden="true"></i>
                        <div class="flex-1 min-w-0 space-y-2">
                            <input id="afNaam" type="text" placeholder="Je naam" autocomplete="name"
                                class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                            <input id="afMail" type="email" placeholder="E-mailadres" autocomplete="email"
                                class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                            <input id="afTel" type="tel" placeholder="Telefoonnummer" autocomplete="tel"
                                class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                        </div>
                    </div>
                    <div class="flex items-start gap-4 py-4">
                        <i class="fa-solid fa-location-dot text-primair mt-0.5" aria-hidden="true"></i>
                        <div class="flex-1 min-w-0">
                            <p id="afAdres" class="text-sm font-bold text-secundair"></p>
                            <p id="afAdresWaarschuwing" class="hidden mt-1 text-xs font-bold text-primair-donker">Dit adres ligt buiten het bezorggebied.</p>
                            <button type="button" id="adresWijzig" class="text-xs font-bold text-secundair/50 underline underline-offset-2 hover:text-secundair transition-colors cursor-pointer">Wijzigen</button>
                        </div>
                    </div>
                    <div class="py-4">
                        <div class="flex items-center gap-4">
                            <i class="fa-regular fa-clock text-primair" aria-hidden="true"></i>
                            <div class="flex-1 min-w-0">
                                <p id="afTijdTitel" class="text-sm font-bold text-secundair">Bezorgtijd</p>
                                <p class="text-xs font-semibold text-secundair/50">Gewenste tijd <span id="afTijdVerplicht" class="ml-1 px-2 py-0.5 rounded-full bg-secundair text-[10px] font-bold text-white">Verplicht</span></p>
                            </div>
                        </div>
                        <div class="relative mt-3 ml-8">
                            <button type="button" id="afTijdKnop"
                                class="w-full flex items-center justify-between gap-3 rounded-xl border border-black/10 px-3.5 py-2.5 text-sm font-semibold text-secundair hover:border-primair/50 transition-colors cursor-pointer">
                                <span id="afTijdLabel" class="text-secundair/45">Kies een moment</span>
                                <i id="afTijdPijl" class="fa-solid fa-chevron-down text-xs text-secundair/40 transition-transform duration-200" aria-hidden="true"></i>
                            </button>
                            <div id="afTijdMenu" class="hidden absolute left-0 right-0 top-full mt-2 max-h-56 overflow-y-auto rounded-xl bg-white border border-black/10 shadow-xl z-30 divide-y divide-black/5">
                                @if($direct)
                                    <button type="button" data-tijd="Zo snel mogelijk" class="af-tijd-optie w-full flex items-center justify-between gap-3 px-3.5 py-2.5 text-left text-sm font-semibold text-secundair hover:bg-wit-warm transition-colors cursor-pointer">
                                        <span>Zo snel mogelijk</span>
                                        <i class="fa-solid fa-check text-primair" style="display:none" aria-hidden="true"></i>
                                    </button>
                                @endif
                                @foreach($slots as $slot)
                                    <button type="button" data-tijd="{{ $slot }}" class="af-tijd-optie w-full flex items-center justify-between gap-3 px-3.5 py-2.5 text-left text-sm font-semibold text-secundair hover:bg-wit-warm transition-colors cursor-pointer">
                                        <span>{{ $slot }}</span>
                                        <i class="fa-solid fa-check text-primair" style="display:none" aria-hidden="true"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 py-4">
                        <i class="fa-regular fa-note-sticky text-primair mt-2.5" aria-hidden="true"></i>
                        <textarea id="afOpm" rows="2" maxlength="200" placeholder="Opmerkingen over de bezorging (optioneel)"
                            class="flex-1 min-w-0 rounded-xl border border-black/10 px-3.5 py-2.5 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors resize-none"></textarea>
                    </div>
                </div>
            </section>

            {{-- Veilig betalen: de betaalkeuze zelf volgt in de beveiligde checkout --}}
            <section class="bg-white rounded-3xl border border-black/10 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 shrink-0 rounded-2xl bg-primair/10 grid place-items-center">
                        <i class="fa-solid fa-lock text-primair" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-extrabold text-secundair">Veilig afrekenen</h2>
                        <p class="mt-1 text-sm font-semibold text-secundair/60">Na het plaatsen van je bestelling word je doorgestuurd naar onze beveiligde betaalomgeving. Daar kies je hoe je wilt betalen.</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1.5 rounded-full bg-wit-warm text-xs font-bold text-secundair/70">iDEAL</span>
                            <span class="px-3 py-1.5 rounded-full bg-wit-warm text-xs font-bold text-secundair/70">Creditcard</span>
                            <span class="px-3 py-1.5 rounded-full bg-wit-warm text-xs font-bold text-secundair/70">Apple Pay</span>
                            <span class="px-3 py-1.5 rounded-full bg-wit-warm text-xs font-bold text-secundair/70">Bancontact</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Fooi voor de bezorger (alleen bij bezorgen) --}}
            <section id="fooiKaart" class="bg-white rounded-3xl border border-black/10 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-extrabold text-secundair">Je bezorger een fooi geven?</h2>
                        <p class="mt-1 text-sm font-semibold text-secundair/60">Het hoeft niet, maar een fooi maakt je bezorger heel blij.</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <button type="button" data-fooi="100" class="fooi-chip px-4 py-2 rounded-full border border-black/10 text-sm font-bold text-secundair [&.aan]:bg-secundair [&.aan]:text-white [&.aan]:border-secundair transition-colors cursor-pointer">&euro; 1,00</button>
                            <button type="button" data-fooi="200" class="fooi-chip px-4 py-2 rounded-full border border-black/10 text-sm font-bold text-secundair [&.aan]:bg-secundair [&.aan]:text-white [&.aan]:border-secundair transition-colors cursor-pointer">&euro; 2,00</button>
                            <button type="button" data-fooi="300" class="fooi-chip px-4 py-2 rounded-full border border-black/10 text-sm font-bold text-secundair [&.aan]:bg-secundair [&.aan]:text-white [&.aan]:border-secundair transition-colors cursor-pointer">&euro; 3,00</button>
                            <button type="button" data-fooi="anders" class="fooi-chip px-4 py-2 rounded-full border border-black/10 text-sm font-bold text-secundair [&.aan]:bg-secundair [&.aan]:text-white [&.aan]:border-secundair transition-colors cursor-pointer">Overig</button>
                        </div>
                        <div id="fooiAndersWrap" class="hidden mt-3 max-w-40">
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-secundair/40">&euro;</span>
                                <input id="fooiAnders" inputmode="decimal" placeholder="0,00"
                                    class="w-full rounded-xl border border-black/10 pl-8 pr-3.5 py-2.5 text-sm font-semibold text-secundair outline-none focus:border-primair/50 transition-colors">
                            </div>
                        </div>
                    </div>
                    <i class="fa-solid fa-motorcycle text-4xl text-primair/25 shrink-0 mt-2" aria-hidden="true"></i>
                </div>
            </section>
        </div>

        {{-- Bestelsamenvatting --}}
        <aside class="bg-white rounded-3xl border border-black/10 p-6 lg:sticky lg:top-24">
            <img src="{{ asset('assets/eten/topdown.jpg') }}" alt="" class="-mx-6 -mt-6 mb-5 w-[calc(100%+3rem)] max-w-none h-28 object-cover rounded-t-3xl">
            <h2 class="text-lg font-extrabold text-secundair">Bestelsamenvatting</h2>
            <div class="mt-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl overflow-hidden border border-black/10 bg-wit-warm grid place-items-center shrink-0">
                    @if($logo)
                        <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-pizza-slice text-primair" aria-hidden="true"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-secundair truncate">{{ $naam }}</p>
                    <button type="button" id="toonProducten" class="text-xs font-bold text-secundair/50 underline underline-offset-2 hover:text-secundair transition-colors cursor-pointer"></button>
                </div>
            </div>
            <div id="prodLijst" class="mt-4 space-y-3"></div>
            {{-- Spaarpunten inwisselen --}}
            <button type="button" id="afPunten" style="display:none" class="mt-4 w-full flex items-center gap-2.5 rounded-xl border border-black/10 px-3.5 py-2.5 text-left text-sm font-bold text-secundair hover:border-primair/50 transition-colors cursor-pointer">
                <i class="fa-solid fa-star text-primair" aria-hidden="true"></i>
                <span id="afPuntenTekst" class="flex-1 min-w-0"></span>
                <span id="afPuntenVink" class="w-5 h-5 shrink-0 rounded-full border border-black/20 grid place-items-center text-[10px]"><i class="fa-solid fa-check" style="display:none" aria-hidden="true"></i></span>
            </button>
            <div class="mt-5 pt-4 border-t border-black/10 space-y-1.5 text-sm font-semibold text-secundair/70">
                <div class="flex items-center justify-between gap-3"><span>Subtotaal</span><span id="afSubtotaal"></span></div>
                <div id="afBezorgRij" class="flex items-center justify-between gap-3"><span>Bezorgkosten</span><span id="afBezorg"></span></div>
                <div id="afFooiRij" class="flex items-center justify-between gap-3" style="display:none"><span>Fooi</span><span id="afFooi"></span></div>
                <div id="afKortingRij" class="flex items-center justify-between gap-3" style="display:none"><span>Spaarpunten korting</span><span id="afKorting"></span></div>
            </div>
            <div class="mt-3 flex items-center justify-between font-extrabold text-secundair">
                <span>Totaal</span>
                <span id="afTotaal"></span>
            </div>
            <button type="button" id="afPlaats" class="mt-5 w-full py-3.5 rounded-full font-bold transition-colors">Bestelling plaatsen</button>
            <p id="afFout" class="hidden mt-3 text-xs font-bold text-primair-donker text-center"></p>
            <p class="mt-3 text-[11px] font-semibold text-secundair/45 text-center">Door te bestellen ga je akkoord met de algemene voorwaarden.</p>
        </aside>
    </main>

    {{-- Adresboek: bezorgadres kiezen, toevoegen of bewerken --}}
    <div id="adresboekModal" class="hidden fixed inset-0 z-50">
        <div id="adresboekBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 grid place-items-center p-4 sm:p-8 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-md max-h-[85vh] flex flex-col rounded-3xl bg-white shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 px-6 pt-5 pb-4">
                    <h2 class="flex-1 min-w-0 text-xl font-extrabold text-secundair">Adresboek</h2>
                    <button type="button" id="adresboekSluit" class="w-9 h-9 -mr-2 -mt-1 shrink-0 rounded-full grid place-items-center text-secundair hover:bg-black/5 transition-colors cursor-pointer" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div class="px-6 pb-2.5">
                        <button type="button" id="adresNieuwKnop" class="w-full flex items-center gap-3 rounded-2xl border border-black/10 px-4 py-3.5 hover:border-primair/50 transition-colors cursor-pointer">
                            <i class="fa-solid fa-house text-secundair/60" aria-hidden="true"></i>
                            <span class="flex-1 text-left text-sm font-bold text-secundair">Voeg nieuw adres toe</span>
                            <i class="fa-solid fa-plus text-secundair/40" aria-hidden="true"></i>
                        </button>
                        <div id="adresNieuwWrap" class="hidden relative mt-2">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-2xl border border-black/10 focus-within:border-primair/50 transition-colors">
                                <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                                <input id="adresNieuwInput" type="text" placeholder="Straat, huisnummer en plaats" autocomplete="off"
                                    class="flex-1 min-w-0 text-sm bg-transparent outline-none font-semibold text-secundair placeholder:text-secundair/40">
                            </div>
                            <div id="adresNieuwMenu" class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border border-black/10 shadow-xl overflow-hidden divide-y divide-black/5 z-30"></div>
                        </div>
                    </div>
                    <div id="adresboekLijst" class="px-6 pb-4 space-y-2.5"></div>
                </div>
                <div class="px-6 pb-6 pt-2">
                    <button type="button" id="adresboekKlaar" class="w-full py-3.5 rounded-full bg-primair font-bold text-white hover:bg-primair-donker transition-colors cursor-pointer">Klaar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const T1_BEZORG = @json($bezorg);
        const T1_BASIS = @json($basis);
        const OPSLAG = @json('t1_' . $slug);
        const FOTOS = @json($fotos ?? []);
        const SPAARPUNTEN = @json($spaarpunten ?? false);
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const euro = (centen) => '€ ' + (centen / 100).toFixed(2).replace('.', ',');

        let mand = [];
        let staat = { type: 'bezorgen', adres: '', tier: null, tijd: '' };
        try { mand = JSON.parse(localStorage.getItem(`${OPSLAG}_mand`)) || []; } catch { mand = []; }
        try { staat = { ...staat, ...(JSON.parse(localStorage.getItem(`${OPSLAG}_afrekenen`)) || {}) }; } catch { /* standaardwaarden */ }

        /* Voorbeeld-modus voor het live voorbeeld in het dashboard: demo-mand tonen */
        const VOORBEELD = @json($voorbeeldMand ?? null);
        if (VOORBEELD && VOORBEELD.length) {
            mand = VOORBEELD;
            staat = { type: 'bezorgen', adres: 'Voorbeeldstraat 12', kosten: 250, tier: null, tijd: '' };
        }

        /* Ingelogde klant: gegevens staan alvast klaar */
        const KLANT = @json($klant ?? null);
        if (KLANT) {
            const zet = (kiezer, waarde) => {
                const veld = document.querySelector(kiezer);
                if (veld && !veld.value.trim() && waarde) veld.value = waarde;
            };
            zet('#afNaam', KLANT.naam);
            zet('#afMail', KLANT.email);
            zet('#afTel', KLANT.telefoon);
            const regel = document.querySelector('#klantRegel');
            if (regel) {
                regel.style.display = '';
                document.querySelector('#klantRegelNaam').textContent = `Ingelogd als ${KLANT.naam}`;
            }
        }

        let gekozenTijd = '';
        let fooi = 0;

        const bezorgen = staat.type !== 'afhalen';
        const regelPrijs = (regel) => (regel.prijsCenten + regel.opties.reduce((som, o) => som + o.prijs, 0)) * regel.aantal;

        if (!mand.length) {
            document.querySelector('#leegScherm').classList.remove('hidden');
            document.querySelector('#afreken').classList.add('hidden');
        }

        /* Bestelgegevens invullen vanuit de meegegeven staat */
        document.querySelector('#afAdres').textContent = bezorgen
            ? (staat.adres || 'Geen adres ingevuld')
            : 'Afhalen bij {{ $naam }}';
        if (!bezorgen) {
            document.querySelector('#afTijdTitel').textContent = 'Afhaaltijd';
            document.querySelector('#fooiKaart').classList.add('hidden');
            document.querySelector('#adresWijzig').classList.add('hidden');
        }
        const directMogelijk = @json($direct);
        if (staat.tijd) kiesTijd(staat.tijd);
        else if (directMogelijk) kiesTijd('Zo snel mogelijk');   // zaak is open: standaard zo snel mogelijk

        /* Tijdkiezer */
        function sluitTijdMenu() {
            document.querySelector('#afTijdMenu').classList.add('hidden');
            document.querySelector('#afTijdPijl').classList.remove('rotate-180');
        }
        function kiesTijd(tijd) {
            gekozenTijd = tijd;
            const label = document.querySelector('#afTijdLabel');
            label.textContent = tijd;
            label.classList.remove('text-secundair/45');
            document.querySelector('#afTijdVerplicht').style.display = 'none';
            document.querySelectorAll('.af-tijd-optie .fa-check').forEach((vink) => {
                vink.style.display = vink.closest('.af-tijd-optie').dataset.tijd === tijd ? '' : 'none';
            });
            werkBij();
        }
        document.querySelector('#afTijdKnop').addEventListener('click', () => {
            const menu = document.querySelector('#afTijdMenu');
            menu.classList.toggle('hidden');
            document.querySelector('#afTijdPijl').classList.toggle('rotate-180', !menu.classList.contains('hidden'));
        });
        document.querySelector('#afTijdMenu').addEventListener('click', (e) => {
            const optie = e.target.closest('.af-tijd-optie');
            if (!optie) return;
            kiesTijd(optie.dataset.tijd);
            sluitTijdMenu();
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#afTijdKnop') && !e.target.closest('#afTijdMenu')) sluitTijdMenu();
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') sluitTijdMenu(); });

        /* Producten staan standaard uitgeklapt en zijn te verbergen */
        document.querySelector('#toonProducten').textContent = 'Verberg producten';
        document.querySelector('#toonProducten').addEventListener('click', () => {
            const lijst = document.querySelector('#prodLijst');
            lijst.classList.toggle('hidden');
            document.querySelector('#toonProducten').textContent = lijst.classList.contains('hidden')
                ? `Toon ${mand.length} ${mand.length === 1 ? 'product' : 'producten'}`
                : 'Verberg producten';
        });
        document.querySelector('#prodLijst').innerHTML = mand.map((regel) => {
            const foto = FOTOS[regel.naam];
            return `
            <div class="flex items-start gap-3 text-sm">
                ${foto ? `<img src="${esc(foto)}" alt="" class="w-12 h-12 rounded-xl object-cover shrink-0">` : ''}
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

        /* Fooi */
        document.querySelectorAll('.fooi-chip').forEach((chip) => chip.addEventListener('click', () => {
            const aan = chip.classList.contains('aan');
            document.querySelectorAll('.fooi-chip').forEach((c) => c.classList.toggle('aan', c === chip && !aan));
            document.querySelector('#fooiAndersWrap').classList.toggle('hidden', aan || chip.dataset.fooi !== 'anders');
            fooi = aan ? 0 : (chip.dataset.fooi === 'anders' ? fooiAndersBedrag() : Number(chip.dataset.fooi));
            if (!aan && chip.dataset.fooi === 'anders') document.querySelector('#fooiAnders').focus();
            werkBij();
        }));
        function fooiAndersBedrag() {
            const n = parseFloat(String(document.querySelector('#fooiAnders').value).replace(',', '.'));
            return isNaN(n) || n < 0 ? 0 : Math.round(n * 100);
        }
        document.querySelector('#fooiAnders').addEventListener('input', () => { fooi = fooiAndersBedrag(); werkBij(); });

        /* Totalen en de plaatsknop */
        /* Spaarpunten: per 100 punten 5 euro korting, nooit meer dan het gerechtenbedrag */
        var puntenAan = false;   /* var: werkBij draait bij het laden al, voor deze regel */
        function puntenBlokken(subtotaal) {
            if (!KLANT || !SPAARPUNTEN) return 0;
            return Math.min(Math.floor((KLANT.punten || 0) / 100), Math.floor(subtotaal / 500));
        }
        document.querySelector('#afPunten')?.addEventListener('click', () => {
            puntenAan = !puntenAan;
            werkBij();
        });

        function werkBij() {
            const subtotaal = mand.reduce((som, regel) => som + regelPrijs(regel), 0);
            const bezorg = bezorgen ? Number(staat.kosten ?? staat.tier?.kosten ?? 0) : 0;
            const blokken = puntenBlokken(subtotaal);
            const korting = puntenAan && blokken > 0 ? blokken * 500 : 0;
            const puntenKnop = document.querySelector('#afPunten');
            if (puntenKnop) {
                puntenKnop.style.display = blokken > 0 ? '' : 'none';
                document.querySelector('#afPuntenTekst').textContent = `Wissel ${blokken * 100} punten in voor ${euro(blokken * 500)} korting`;
                const vink = document.querySelector('#afPuntenVink');
                vink.className = 'w-5 h-5 shrink-0 rounded-full grid place-items-center text-[10px] ' + (puntenAan && korting > 0 ? 'bg-primair text-white' : 'border border-black/20 text-transparent');
                vink.querySelector('i').style.display = puntenAan && korting > 0 ? '' : 'none';
            }
            document.querySelector('#afKortingRij').style.display = korting > 0 ? '' : 'none';
            document.querySelector('#afKorting').textContent = '- ' + euro(korting);
            document.querySelector('#afSubtotaal').textContent = euro(subtotaal);
            document.querySelector('#afBezorgRij').style.display = bezorgen ? '' : 'none';
            document.querySelector('#afBezorg').textContent = euro(bezorg);
            document.querySelector('#afFooiRij').style.display = fooi > 0 ? '' : 'none';
            document.querySelector('#afFooi').textContent = euro(fooi);
            document.querySelector('#afTotaal').textContent = euro(subtotaal + bezorg + fooi - korting);

            document.querySelector('#afAdresWaarschuwing').classList.toggle('hidden', !(bezorgen && staat.buitenGebied));
            const mail = document.querySelector('#afMail').value.trim();
            const klaar = !!(document.querySelector('#afNaam').value.trim()
                && mail.includes('@') && mail.includes('.')
                && document.querySelector('#afTel').value.trim()
                && gekozenTijd
                && mand.length
                && !(bezorgen && staat.buitenGebied));
            const knop = document.querySelector('#afPlaats');
            knop.disabled = !klaar;
            knop.className = 'mt-5 w-full py-3.5 rounded-full font-bold transition-colors ' + (klaar
                ? 'bg-primair text-white hover:bg-primair-donker cursor-pointer'
                : 'bg-black/10 text-secundair/40 cursor-not-allowed');
        }
        document.querySelector('#afNaam').addEventListener('input', werkBij);
        document.querySelector('#afMail').addEventListener('input', werkBij);
        document.querySelector('#afTel').addEventListener('input', werkBij);
        werkBij();

        /* Bestelling plaatsen: nog zonder betaalstap, de order komt direct in het dashboard binnen */
        document.querySelector('#afPlaats').addEventListener('click', async () => {
            const knop = document.querySelector('#afPlaats');
            if (knop.disabled) return;
            knop.disabled = true;
            knop.textContent = 'Bezig met plaatsen';
            document.querySelector('#afFout').classList.add('hidden');
            const subtotaal = mand.reduce((som, regel) => som + regelPrijs(regel), 0);
            const bezorg = bezorgen ? Number(staat.kosten ?? staat.tier?.kosten ?? 0) : 0;
            try {
                const res = await fetch(`${T1_BASIS}/afrekenen`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        type: staat.type || 'bezorgen',
                        klant: document.querySelector('#afNaam').value.trim(),
                        telefoon: document.querySelector('#afTel').value.trim(),
                        punten: puntenAan && puntenBlokken(subtotaal) > 0,
                        adres: bezorgen ? staat.adres : null,
                        tijd: gekozenTijd,
                        opmerking: document.querySelector('#afOpm').value.trim() || null,
                        bezorgkosten: bezorg,
                        fooi,
                        items: mand.map((regel) => ({
                            id: Number(String(regel.sleutel).split('|')[0]),
                            aantal: regel.aantal,
                            opties: regel.opties,
                            opmerking: regel.opmerking || null,
                        })),
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.ok) throw new Error(data.melding || 'Bestellen is niet gelukt, probeer het opnieuw.');
                localStorage.setItem(`${OPSLAG}_bestelling`, JSON.stringify({
                    nummer: data.nummer,
                    token: data.token,
                    geplaatst: Date.now(),
                    type: staat.type,
                    adres: staat.adres,
                    lat: staat.lat ?? null,
                    lng: staat.lng ?? null,
                    tijd: gekozenTijd,
                    klant: {
                        naam: document.querySelector('#afNaam').value.trim(),
                        mail: document.querySelector('#afMail').value.trim(),
                        tel: document.querySelector('#afTel').value.trim(),
                    },
                    opmerking: document.querySelector('#afOpm').value.trim(),
                    items: mand,
                    subtotaal,
                    kosten: bezorg,
                    fooi,
                    korting: data.korting || 0,
                    punten: data.punten || 0,
                    totaal: subtotaal + bezorg + fooi - (data.korting || 0),
                }));
                localStorage.removeItem(`${OPSLAG}_mand`);
                location.href = data.token ? `${T1_BASIS}/bestelling/${data.token}` : `${T1_BASIS}/bestelling`;
            } catch (fout) {
                const melding = document.querySelector('#afFout');
                melding.textContent = fout.message;
                melding.classList.remove('hidden');
                knop.textContent = 'Bestelling plaatsen';
                werkBij();
            }
        });

        /* ── Adresboek ───────────────────────────────────────────── */

        let adressen = [];
        try { adressen = JSON.parse(localStorage.getItem(`${OPSLAG}_adressen`)) || []; } catch { adressen = []; }
        /* Ingelogd: het adresboek van je account is leidend, ook op een ander apparaat */
        if (KLANT && Array.isArray(KLANT.adressen) && KLANT.adressen.length) {
            adressen = KLANT.adressen;
        }
        if (!adressen.length && staat.adres) {
            adressen = [{ label: staat.adres, lat: null, lng: null }];
        }
        let gekozenAdres = Math.max(0, adressen.findIndex((a) => a.label === staat.adres));
        let adresBewerk = null;   // index die via "Bewerk adres" wordt vervangen
        const bewaarAdressen = () => {
            localStorage.setItem(`${OPSLAG}_adressen`, JSON.stringify(adressen));
            /* Ingelogd: het adresboek hoort bij je account, dus ook op de server bewaren */
            if (KLANT) {
                fetch(`${T1_BASIS}/klant/adressen`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ adressen }),
                }).catch(() => { /* lokaal is het al bewaard; volgende wijziging probeert opnieuw */ });
            }
        };

        const afstandKm = (aLat, aLng, bLat, bLng) => {
            const r = Math.PI / 180;
            const h = Math.sin((bLat - aLat) * r / 2) ** 2
                + Math.cos(aLat * r) * Math.cos(bLat * r) * Math.sin((bLng - aLng) * r / 2) ** 2;
            return 6371 * 2 * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
        };

        function renderAdresboek() {
            document.querySelector('#adresboekLijst').innerHTML = adressen.map((adres, i) => {
                const aan = i === gekozenAdres;
                const [hoofd, ...rest] = String(adres.label).split(', ');
                return `
                <div data-adres-kies="${i}" class="flex items-start gap-3 rounded-2xl border-2 ${aan ? 'border-secundair' : 'border-black/10'} px-4 py-3.5 cursor-pointer transition-colors">
                    <i class="fa-solid fa-location-dot ${aan ? 'text-primair' : 'text-secundair/35'} mt-0.5" aria-hidden="true"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-secundair">${esc(hoofd)}</p>
                        ${rest.length ? `<p class="text-xs font-semibold text-secundair/55">${esc(rest.join(', '))}</p>` : ''}
                        <button type="button" data-adres-bewerk="${i}" class="mt-1 text-xs font-bold text-secundair underline underline-offset-2 cursor-pointer">Bewerk adres</button>
                    </div>
                    <span class="mt-0.5 w-5 h-5 shrink-0 rounded-full border-2 ${aan ? 'border-primair bg-primair' : 'border-black/20'} grid place-items-center">
                        ${aan ? '<span class="w-1.5 h-1.5 rounded-full bg-white"></span>' : ''}
                    </span>
                </div>`;
            }).join('');
        }

        function sluitAdresboek() {
            document.querySelector('#adresboekModal').classList.add('hidden');
            document.querySelector('#adresNieuwWrap').classList.add('hidden');
            sluitAdresSuggesties();
            adresBewerk = null;
        }

        document.querySelector('#adresWijzig').addEventListener('click', () => {
            gekozenAdres = Math.max(0, adressen.findIndex((a) => a.label === staat.adres));
            renderAdresboek();
            document.querySelector('#adresboekModal').classList.remove('hidden');
        });
        document.querySelector('#adresboekSluit').addEventListener('click', sluitAdresboek);
        document.querySelector('#adresboekBackdrop').addEventListener('click', sluitAdresboek);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') sluitAdresboek(); });

        document.querySelector('#adresboekLijst').addEventListener('click', (e) => {
            const bewerk = e.target.closest('[data-adres-bewerk]');
            if (bewerk) {
                adresBewerk = Number(bewerk.dataset.adresBewerk);
                const wrap = document.querySelector('#adresNieuwWrap');
                wrap.classList.remove('hidden');
                const veld = document.querySelector('#adresNieuwInput');
                veld.value = adressen[adresBewerk].label;
                veld.focus();
                return;
            }
            const kies = e.target.closest('[data-adres-kies]');
            if (kies) { gekozenAdres = Number(kies.dataset.adresKies); renderAdresboek(); }
        });

        document.querySelector('#adresNieuwKnop').addEventListener('click', () => {
            adresBewerk = null;
            const wrap = document.querySelector('#adresNieuwWrap');
            wrap.classList.toggle('hidden');
            if (!wrap.classList.contains('hidden')) {
                document.querySelector('#adresNieuwInput').value = '';
                document.querySelector('#adresNieuwInput').focus();
            }
        });

        /* Adressuggesties via Nominatim, zelfde aanpak als op de menupagina */
        let adresTimer = null;
        let adresAbort = null;

        function sluitAdresSuggesties() {
            document.querySelector('#adresNieuwMenu').classList.add('hidden');
            document.querySelector('#adresNieuwMenu').innerHTML = '';
        }

        document.querySelector('#adresNieuwInput').addEventListener('input', (e) => {
            clearTimeout(adresTimer);
            const zoek = e.target.value.trim();
            if (zoek.length < 3) return sluitAdresSuggesties();
            adresTimer = setTimeout(async () => {
                adresAbort?.abort();
                adresAbort = new AbortController();
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=nl&limit=5&q=${encodeURIComponent(zoek)}`, {
                        signal: adresAbort.signal,
                        headers: { 'Accept-Language': 'nl' },
                    });
                    const data = res.ok ? await res.json() : [];
                    const menu = document.querySelector('#adresNieuwMenu');
                    if (!Array.isArray(data) || !data.length) {
                        menu.innerHTML = '<p class="px-4 py-3 text-sm font-semibold text-secundair/50">Geen adres gevonden</p>';
                        menu.classList.remove('hidden');
                        return;
                    }
                    menu.innerHTML = data.map((hit) => {
                        const a = hit.address || {};
                        const straat = [a.road || a.pedestrian || a.square, a.house_number].filter(Boolean).join(' ');
                        const plaats = [a.postcode, a.city || a.town || a.village || a.municipality].filter(Boolean).join(' ');
                        const label = [straat, plaats].filter(Boolean).join(', ') || hit.display_name.split(',').slice(0, 3).join(',');
                        return `<button type="button" class="adres-suggestie w-full flex items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-secundair hover:bg-wit-warm transition-colors cursor-pointer" data-adres="${esc(label)}" data-lat="${esc(hit.lat)}" data-lon="${esc(hit.lon)}">
                            <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0 truncate">${esc(label)}</span>
                        </button>`;
                    }).join('');
                    menu.classList.remove('hidden');
                } catch { /* afgebroken of offline */ }
            }, 350);
        });

        document.querySelector('#adresNieuwMenu').addEventListener('click', (e) => {
            const optie = e.target.closest('.adres-suggestie');
            if (!optie) return;
            const entry = { label: optie.dataset.adres, lat: parseFloat(optie.dataset.lat), lng: parseFloat(optie.dataset.lon) };
            if (adresBewerk !== null) {
                adressen[adresBewerk] = entry;
                gekozenAdres = adresBewerk;
            } else {
                adressen.push(entry);
                gekozenAdres = adressen.length - 1;
            }
            adresBewerk = null;
            bewaarAdressen();
            sluitAdresSuggesties();
            document.querySelector('#adresNieuwWrap').classList.add('hidden');
            renderAdresboek();
        });

        document.querySelector('#adresboekKlaar').addEventListener('click', () => {
            const sel = adressen[gekozenAdres];
            if (sel) {
                staat.adres = sel.label;
                document.querySelector('#afAdres').textContent = sel.label;
                // tarief herberekenen zodra we de plek van het adres kennen
                if (sel.lat !== null && sel.lng !== null) {
                    staat.lat = sel.lat;
                    staat.lng = sel.lng;
                }
                if (sel.lat !== null && sel.lng !== null && T1_BEZORG.lat !== null && (T1_BEZORG.tiers || []).length) {
                    const afstand = afstandKm(T1_BEZORG.lat, T1_BEZORG.lng, sel.lat, sel.lng);
                    const tier = T1_BEZORG.tiers.find((t) => afstand <= parseFloat(t.km)) || null;
                    staat.tier = tier;
                    staat.kosten = tier ? tier.kosten : 0;
                    staat.buitenGebied = !tier;
                } else {
                    staat.buitenGebied = false;
                }
                localStorage.setItem(`${OPSLAG}_afrekenen`, JSON.stringify(staat));
            }
            sluitAdresboek();
            werkBij();
        });
    </script>
</body>
</html>
