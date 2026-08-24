<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $naam }}</title>

    {{-- Alvast klaargezet om mee te ontwerpen: Tailwind, FontAwesome en de template-fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* "static" zodat de kleuren ook bestaan als ze (nog) niet in de HTML voorkomen */
        @theme static {
            --font-sans: "Inter Tight", sans-serif;
            /* Het palet wordt afgeleid van de hoofdkleur die de pizzeria koos */
            --color-primair: {{ $palet['primair'] }};
            --color-primair-donker: {{ $palet['primairDonker'] }};
            --color-secundair: {{ $palet['secundair'] }};
            --color-secundair-licht: {{ $palet['secundairLicht'] }};
            --color-wit-warm: {{ $palet['witWarm'] }};
        }
        body { font-family: var(--font-sans); }
        /* Eigen pin voor de kaart in de Over-popup */
        .over-pin-bol {
            width: 2.75rem; height: 2.75rem; border-radius: 9999px;
            background: #fff; border: 3px solid var(--color-primair);
            display: grid; place-items: center; color: var(--color-primair);
            font-size: 1.05rem; box-shadow: 0 6px 16px rgb(0 0 0 / .25);
        }
    </style>
</head>
<body>
    <div class="w-full flex">
        <div class="flex-1 min-w-0 bg-wit-warm">
            {{-- Hero-band met pizzafoto als achtergrond --}}
            <div class="p-3">
                <div class="relative h-[400px] overflow-hidden rounded-4xl">
                    <img src="{{ asset('assets/eten/pepperoni.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-b from-black/35 to-black/5"></div>
                    {{-- Logo van de demo-pizzeria, linksonder binnen de content-breedte --}}
                    <div class="absolute inset-0 max-w-7xl mx-auto px-6">
                        <div class="absolute left-6 bottom-6 w-24 h-24 rounded-2xl overflow-hidden border-2 border-white/40 bg-white grid place-items-center shadow-xl z-10">
                            @if($logo)
                                <img src="{{ $logo }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-pizza-slice text-3xl text-primair" aria-hidden="true"></i>
                            @endif
                        </div>
                    </div>
                    <div class="relative w-full py-4 px-6 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                    <div class="flex items-center gap-3">
                        <button type="button" aria-label="Terug"
                            class="w-10 h-10 rounded-full grid place-items-center border border-white/30 bg-white/20 backdrop-blur-md text-white hover:bg-white/30 transition-colors cursor-pointer">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="relative w-[600px]">
                        <div class="flex items-center gap-3 px-5 py-4 rounded-full border border-white/30 bg-white/20 backdrop-blur-md focus-within:border-white/60 transition-colors">
                            <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                            <input id="adresInput" type="text" value="Stationsplein 12, 6811 KG Arnhem" placeholder="Vul je adres in" autocomplete="off"
                                class="flex-1 min-w-0 text-sm bg-transparent outline-none font-semibold text-white placeholder:text-white/60">
                        </div>
                        {{-- Adressuggesties verschijnen hieronder --}}
                        <div id="adresMenu" class="hidden absolute left-3 right-3 top-full mt-2 bg-white rounded-2xl border border-black/10 shadow-xl overflow-hidden divide-y divide-black/5 z-20"></div>
                    </div>
                    <div></div>
                    </div>
                </div>

            </div>

            {{-- Restaurant-info onder de foto, gecentreerd op content-breedte --}}
            <div class="max-w-7xl mx-auto px-6 pt-5 pb-12">
                <div class="flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-3xl font-extrabold text-secundair">{{ $naam }}</h1>
                        <p class="mt-1.5 flex items-center gap-1.5 text-sm font-bold text-secundair">
                            <i class="fa-solid fa-star text-primair" aria-hidden="true"></i>
                            4,8 <a href="#" id="openBeoordelingen" class="text-secundair/55 underline underline-offset-2">(1.200+)</a>
                        </p>
                        <div class="mt-2 text-sm font-semibold text-secundair/75">
                            <p class="flex flex-wrap gap-x-6 gap-y-0.5">
                                <span id="infoMin">Minimum bestelbedrag &euro; {{ number_format(($laagsteTier['min'] ?? 1500) / 100, 2, ',', '.') }}</span>
                                <span id="infoBezorg">Bezorging vanaf &euro; {{ number_format(($laagsteTier['kosten'] ?? 250) / 100, 2, ',', '.') }}</span>
                            </p>
                        </div>
                        <p class="mt-2 text-xs font-medium text-secundair/50">Er zijn kosten van toepassing. Voeg toe aan je winkelmandje om de totaalprijs te zien.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 pt-1">
                        <button type="button" id="openInfoKnop" aria-label="Informatie" class="w-10 h-10 rounded-full grid place-items-center border border-black/10 bg-white text-secundair hover:bg-black/5 transition-colors cursor-pointer">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                {{-- Zoeken binnen het menu --}}
                <div class="mt-5 flex items-center gap-3 px-5 py-3.5 rounded-full border border-black/10 bg-white focus-within:border-primair/50 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-primair" aria-hidden="true"></i>
                    <input id="menuZoek" type="text" placeholder="Zoeken {{ $naam }}" class="flex-1 min-w-0 text-sm bg-transparent outline-none font-semibold text-secundair placeholder:text-secundair/40">
                </div>

                {{-- Categorie-chips --}}
                <div class="mt-4 flex items-center gap-2">
                    <div id="catRij" class="flex-1 min-w-0 flex items-center gap-2 overflow-x-auto [scrollbar-width:none]">
                        <button type="button" class="cat-chip aan shrink-0 px-4 py-2 rounded-full text-sm font-bold text-secundair/70 [&.aan]:bg-secundair [&.aan]:text-white transition-colors cursor-pointer">Uitgelicht</button>
                        @foreach($categorieen as $categorie)
                            <button type="button" class="cat-chip shrink-0 px-4 py-2 rounded-full text-sm font-bold text-secundair/70 [&.aan]:bg-secundair [&.aan]:text-white transition-colors cursor-pointer">{{ $categorie }}</button>
                        @endforeach
                    </div>
                    <button type="button" aria-label="Verder scrollen" class="w-9 h-9 shrink-0 rounded-full grid place-items-center border border-black/10 bg-white text-secundair hover:bg-black/5 transition-colors cursor-pointer">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>

                {{-- Menukaart per categorie --}}
                @foreach($menu as $categorie => $items)
                    <section class="mt-8" data-categorie="{{ $categorie }}">
                        <h2 class="text-xl font-extrabold text-secundair">{{ $categorie }}</h2>
                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($items as $item)
                                <article data-gerecht="{{ $item['id'] }}" class="flex justify-between gap-4 p-5 rounded-2xl border border-black/10 bg-white hover:border-primair/40 transition-colors cursor-pointer">
                                    <div class="flex-1 min-w-0 py-0.5">
                                        <p class="font-bold text-secundair">{{ $item['naam'] }}</p>
                                        <p class="mt-1 text-sm font-bold text-secundair">&euro; {{ $item['prijs'] }}</p>
                                        @if($item['beschrijving'])
                                            <p class="mt-2 text-sm font-medium text-secundair/60 line-clamp-3">{{ $item['beschrijving'] }}</p>
                                        @endif
                                        @if(!empty($item['allergenen']))
                                            <div class="mt-2.5 flex flex-wrap gap-1.5">
                                                @foreach($item['allergenen'] as $allergeen)
                                                    <span class="px-2.5 py-1 rounded-full bg-primair/10 text-[11px] font-bold text-primair-donker">{{ ucfirst($allergeen) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($item['foto'])
                                        <div class="relative shrink-0 w-28 h-28">
                                            <img src="{{ $item['foto'] }}" alt="" class="w-full h-full object-cover rounded-xl">
                                            <button type="button" aria-label="Toevoegen" class="absolute -top-2 -right-2 w-9 h-9 rounded-full grid place-items-center bg-white border border-black/10 shadow-md text-primair hover:bg-wit-warm transition-colors cursor-pointer">
                                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" aria-label="Toevoegen" class="self-start shrink-0 w-9 h-9 rounded-full grid place-items-center bg-white border border-black/10 shadow-md text-primair hover:bg-wit-warm transition-colors cursor-pointer">
                                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                {{-- Als de zoekopdracht niets oplevert --}}
                <div id="menuLeegMelding" class="hidden mt-10 text-center">
                    <i class="fa-solid fa-magnifying-glass text-2xl text-secundair/25" aria-hidden="true"></i>
                    <p class="mt-3 text-sm font-semibold text-secundair/50">Geen gerechten gevonden, probeer een andere zoekopdracht.</p>
                </div>
            </div>
        </div>
        <div class="w-[400px] shrink-0 sticky top-0 self-start h-screen bg-secundair p-5 flex flex-col">
            <p class="text-white font-bold text-lg mb-4 text-center">Winkelmand</p>
            <div id="typeToggle" class="relative grid grid-cols-2 p-1 rounded-full bg-white/10">
                {{-- Het schuifje dat achter de actieve keuze langs glijdt --}}
                <span id="typeSchuif" class="absolute top-1 bottom-1 left-1 w-[calc(50%-0.25rem)] rounded-full bg-primair transition-transform duration-300 ease-out"></span>
                <button type="button" data-type="bezorgen"
                    class="aan relative z-10 rounded-full py-2.5 text-sm font-bold text-white/60 [&.aan]:text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-motorcycle" aria-hidden="true"></i> Bezorgen
                </button>
                <button type="button" data-type="afhalen"
                    class="relative z-10 rounded-full py-2.5 text-sm font-bold text-white/60 [&.aan]:text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Afhalen
                </button>
            </div>

            {{-- Lege staat: gecentreerd in het resterende blok --}}
            <div id="mandLeeg" class="flex-1 min-h-0">
                <div class="h-full grid place-items-center">
                    <div class="text-center">
                        <i class="fa-solid fa-basket-shopping text-3xl text-white/25" aria-hidden="true"></i>
                        <p class="mt-3 text-sm font-bold text-white/45">Winkelmand leeg</p>
                    </div>
                </div>
            </div>

            {{-- Gevulde winkelmand: regels plus totaal --}}
            <div id="mandLijst" class="hidden flex-1 min-h-0 mt-3">
                <div class="h-full overflow-y-auto divide-y divide-white/10" id="mandRegels"></div>
            </div>
            <div id="mandOnder" class="hidden pt-4 mt-4 border-t border-white/10">
                <div class="space-y-1.5 text-sm font-semibold text-white/70">
                    <div class="flex items-start justify-between gap-3">
                        <span>Subtotaal</span>
                        <span id="mandSubtotaal" class="shrink-0"></span>
                    </div>
                    <div id="mandBezorgRij" class="flex items-start justify-between gap-3">
                        <span>Bezorgkosten <i class="fa-solid fa-circle-info text-white/35" aria-hidden="true"></i></span>
                        <span id="mandBezorg" class="shrink-0"></span>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between font-bold text-white">
                    <span>Totaal</span>
                    <span id="mandTotaal"></span>
                </div>
                <div id="mandMelding" class="hidden mt-3">
                    <div class="flex items-start gap-2.5 rounded-xl bg-primair/15 px-3.5 py-2.5">
                        <i class="fa-solid fa-circle-exclamation text-primair mt-0.5" aria-hidden="true"></i>
                        <p class="text-xs font-bold text-white/85">Dit adres ligt buiten ons bezorggebied. Kies een ander adres of ga voor afhalen.</p>
                    </div>
                </div>
                <div id="mandMinimum" class="hidden mt-3">
                    <div class="flex items-start gap-2.5 rounded-xl bg-primair/15 px-3.5 py-2.5">
                        <i class="fa-solid fa-circle-exclamation text-primair mt-0.5" aria-hidden="true"></i>
                        <p id="mandMinimumTekst" class="text-xs font-bold text-white/85"></p>
                    </div>
                </div>
                <div id="mandGesloten" class="hidden mt-3">
                    <div class="rounded-xl bg-primair/15 px-3.5 py-2.5">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-clock text-primair mt-0.5" aria-hidden="true"></i>
                            <p class="text-xs font-bold text-white/85">{{ ($openInfo['pauze'] ?? false) ? 'We nemen op dit moment even geen bestellingen aan. Probeer het straks opnieuw.' : 'We zijn op dit moment gesloten. Je kunt wel alvast bestellen voor een ander moment.' }}</p>
                        </div>
                        @if(count($openInfo['slots']))
                            <div id="tijdKiezer" class="relative mt-2.5">
                                <button type="button" id="tijdKnop" class="w-full flex items-center justify-between gap-3 rounded-xl bg-white/10 border border-white/10 px-3.5 py-2.5 text-sm font-semibold text-white hover:border-white/25 transition-colors cursor-pointer">
                                    <span id="tijdLabel">Kies een moment</span>
                                    <i id="tijdPijl" class="fa-solid fa-chevron-down text-xs text-white/50 transition-transform duration-200" aria-hidden="true"></i>
                                </button>
                                <div id="tijdMenu" class="hidden absolute left-0 right-0 bottom-full mb-2 max-h-56 overflow-y-auto rounded-xl bg-secundair-licht border border-white/15 shadow-2xl z-30 divide-y divide-white/5">
                                    @foreach($openInfo['slots'] as $slot)
                                        <button type="button" data-tijd="{{ $slot }}" class="tijd-optie w-full flex items-center justify-between gap-3 px-3.5 py-2.5 text-left text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-colors cursor-pointer">
                                            <span>{{ $slot }}</span>
                                            <i class="fa-solid fa-check text-primair" style="display:none" aria-hidden="true"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" id="mandAfrekenen" class="mt-4 w-full py-3.5 rounded-full bg-primair font-bold text-white hover:bg-primair-donker transition-colors cursor-pointer">Ga naar afrekenen</button>
            </div>
        </div>
    </div>

    {{-- Productmodal: opties kiezen, aantal en toevoegen aan de winkelmand --}}
    <div id="productModal" class="hidden fixed inset-0 z-50">
        <div id="productBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 grid place-items-center p-4 sm:p-8 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg max-h-[85vh] flex flex-col rounded-3xl bg-white shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 px-6 pt-5 pb-4">
                    <h2 id="pmNaam" class="flex-1 min-w-0 text-xl font-extrabold text-secundair"></h2>
                    <button type="button" id="productSluit" class="w-9 h-9 -mr-2 -mt-1 shrink-0 rounded-full grid place-items-center text-secundair hover:bg-black/5 transition-colors cursor-pointer" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div id="pmFotoWrap" class="hidden px-6 pb-4">
                        <img id="pmFoto" src="" alt="" class="w-full h-48 object-cover rounded-2xl">
                    </div>
                    <div class="px-6 pb-5">
                        <p id="pmPrijs" class="font-extrabold text-secundair"></p>
                        <p id="pmBeschrijving" class="mt-1 text-sm font-medium text-secundair/60"></p>
                        <div id="pmAllergenen" class="mt-2.5 flex flex-wrap gap-1.5"></div>
                    </div>
                    <div id="pmOpties"></div>
                </div>
                <div class="flex items-center gap-3 px-6 py-4 border-t border-black/10">
                    <div class="flex items-center gap-1 rounded-full border border-black/10 p-1">
                        <button type="button" id="pmMin" class="w-9 h-9 rounded-full grid place-items-center text-secundair hover:bg-black/5 transition-colors cursor-pointer" aria-label="Minder">
                            <i class="fa-solid fa-minus" aria-hidden="true"></i>
                        </button>
                        <span id="pmAantal" class="w-8 text-center font-extrabold text-secundair">1</span>
                        <button type="button" id="pmPlus" class="w-9 h-9 rounded-full grid place-items-center text-secundair hover:bg-black/5 transition-colors cursor-pointer" aria-label="Meer">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button type="button" id="pmToevoegen" class="flex-1 py-3.5 rounded-full font-bold transition-colors cursor-pointer"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Over-popup: beoordelingen en info over de zaak --}}
    <div id="overModal" class="hidden fixed inset-0 z-50">
        <div id="overBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 grid place-items-center p-4 sm:p-8 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg max-h-[85vh] flex flex-col rounded-3xl bg-white shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 px-6 pt-5 pb-3">
                    <h2 class="flex-1 min-w-0 text-xl font-extrabold text-secundair">Over {{ $naam }}</h2>
                    <button type="button" id="overSluit" class="w-9 h-9 -mr-2 -mt-1 shrink-0 rounded-full grid place-items-center text-secundair hover:bg-black/5 transition-colors cursor-pointer" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex gap-6 px-6 border-b border-black/10">
                    <button type="button" data-over-tab="beoordelingen" class="pb-3 text-sm font-bold text-secundair/50 border-b-2 border-transparent [&.aan]:text-secundair [&.aan]:border-primair transition-colors cursor-pointer">Beoordelingen</button>
                    <button type="button" data-over-tab="info" class="pb-3 text-sm font-bold text-secundair/50 border-b-2 border-transparent [&.aan]:text-secundair [&.aan]:border-primair transition-colors cursor-pointer">Info</button>
                </div>
                <div class="flex-1 overflow-y-auto pt-5">
                    <div id="overTabBeoordelingen">
                        <div class="px-6 pb-4 flex items-center gap-4">
                            <p class="text-4xl font-extrabold text-secundair">4,8</p>
                            <div>
                                <p class="flex items-center gap-1 text-primair">
                                    @for($i = 0; $i < 5; $i++) <i class="fa-solid fa-star text-sm" aria-hidden="true"></i> @endfor
                                </p>
                                <p class="mt-1 text-xs font-semibold text-secundair/55">1.200+ beoordelingen</p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 space-y-3">
                            @foreach([
                                ['naam' => 'Sanne', 'sterren' => 5, 'tijd' => '3 dagen geleden', 'tekst' => 'Beste kapsalon van Arnhem, lekker krokante friet en de saus is top.'],
                                ['naam' => 'Mehmet', 'sterren' => 5, 'tijd' => '1 week geleden', 'tekst' => 'Snelle bezorging en alles was nog goed warm. Aanrader!'],
                                ['naam' => 'Kim', 'sterren' => 4, 'tijd' => '2 weken geleden', 'tekst' => 'Ruime porties, volgende keer graag iets meer saus.'],
                                ['naam' => 'Jeroen', 'sterren' => 5, 'tijd' => '3 weken geleden', 'tekst' => 'Bestellen gaat makkelijk en het is altijd op tijd.'],
                            ] as $review)
                                <div class="rounded-2xl border border-black/10 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-bold text-secundair">{{ $review['naam'] }}</p>
                                        <p class="text-xs font-semibold text-secundair/45">{{ $review['tijd'] }}</p>
                                    </div>
                                    <p class="mt-1 flex items-center gap-0.5 text-primair">
                                        @for($i = 0; $i < $review['sterren']; $i++) <i class="fa-solid fa-star text-xs" aria-hidden="true"></i> @endfor
                                    </p>
                                    <p class="mt-1.5 text-sm font-medium text-secundair/70">{{ $review['tekst'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="overTabInfo" class="hidden">
                        <div class="px-6 pb-5">
                            <div id="overMap" class="h-56 rounded-2xl border border-black/10 z-0" style="background:var(--color-wit-warm)"></div>
                            <div class="mt-3 flex items-start gap-3">
                                <i class="fa-solid fa-location-dot text-primair mt-1" aria-hidden="true"></i>
                                <div>
                                    <p class="text-sm font-bold text-secundair">{{ $overInfo['straat'] }}</p>
                                    <p class="text-sm font-semibold text-secundair/60">{{ trim($overInfo['postcode'] . ' ' . $overInfo['plaats']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-5">
                            <p class="flex items-center gap-2 font-extrabold text-secundair"><i class="fa-regular fa-clock text-primair" aria-hidden="true"></i> Openingstijden</p>
                            <div class="mt-2.5 space-y-1.5">
                                @foreach($overInfo['dagen'] as $dag)
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="font-semibold text-secundair/60">{{ $dag['naam'] }}</span>
                                        <span class="font-bold {{ $dag['open'] ? 'text-secundair' : 'text-secundair/40' }}">{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            <p class="flex items-center gap-2 font-extrabold text-secundair"><i class="fa-solid fa-motorcycle text-primair" aria-hidden="true"></i> Bezorgkosten</p>
                            <div class="mt-2.5 space-y-1.5">
                                @forelse($bezorg['tiers'] as $tier)
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="font-semibold text-secundair/60">tot {{ rtrim(rtrim(number_format((float) $tier['km'], 1, ',', '.'), '0'), ',') }} km</span>
                                        <span class="font-bold text-secundair">
                                            &euro; {{ number_format($tier['kosten'] / 100, 2, ',', '.') }}
                                            @if(($tier['min'] ?? 0) > 0)
                                                <span class="font-semibold text-secundair/50">(min. &euro; {{ number_format($tier['min'] / 100, 2, ',', '.') }})</span>
                                            @endif
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm font-semibold text-secundair/50">Geen bezorgkosten ingesteld.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const T1_MENU = @json($menuJs);
        const T1_BEZORG = @json($bezorg);
        const T1_OPEN = @json($openInfo);
        const T1_BASIS = @json($basis);
        const OPSLAG = @json('t1_' . $slug);
    </script>
    <script>
        /* Adres-autocomplete via OpenStreetMap Nominatim */
        const adresInput = document.querySelector('#adresInput');
        const adresMenu = document.querySelector('#adresMenu');
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        let adresTimer = null;
        let adresAbort = null;

        function sluitAdresMenu() {
            adresMenu.classList.add('hidden');
            adresMenu.innerHTML = '';
        }

        adresInput.addEventListener('input', () => {
            clearTimeout(adresTimer);
            const zoek = adresInput.value.trim();
            if (zoek.length < 3) return sluitAdresMenu();
            adresTimer = setTimeout(async () => {
                adresAbort?.abort();
                adresAbort = new AbortController();
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=nl&limit=5&q=${encodeURIComponent(zoek)}`, {
                        signal: adresAbort.signal,
                        headers: { 'Accept-Language': 'nl' },
                    });
                    const data = res.ok ? await res.json() : [];
                    if (!Array.isArray(data) || !data.length) {
                        adresMenu.innerHTML = '<p class="px-5 py-3.5 text-sm font-semibold text-secundair/50">Geen adres gevonden</p>';
                        adresMenu.classList.remove('hidden');
                        return;
                    }
                    adresMenu.innerHTML = data.map((hit) => {
                        const a = hit.address || {};
                        const straat = [a.road || a.pedestrian || a.square, a.house_number].filter(Boolean).join(' ');
                        const plaats = [a.postcode, a.city || a.town || a.village || a.municipality].filter(Boolean).join(' ');
                        const label = [straat, plaats].filter(Boolean).join(', ') || hit.display_name.split(',').slice(0, 3).join(',');
                        return `<button type="button" class="adres-optie w-full flex items-center gap-3 px-5 py-3.5 text-left text-sm font-semibold text-secundair hover:bg-wit-warm transition-colors cursor-pointer" data-adres="${esc(label)}" data-lat="${esc(hit.lat)}" data-lon="${esc(hit.lon)}">
                            <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0">
                                <span class="block truncate">${esc(label)}</span>
                                <span class="block text-xs font-medium text-secundair/45 truncate">${esc(hit.display_name)}</span>
                            </span>
                        </button>`;
                    }).join('');
                    adresMenu.classList.remove('hidden');
                } catch { /* afgebroken of offline: menu gewoon dicht laten */ }
            }, 350);
        });

        adresMenu.addEventListener('click', (e) => {
            const optie = e.target.closest('.adres-optie');
            if (!optie) return;
            adresInput.value = optie.dataset.adres;
            sluitAdresMenu();
            const lat = parseFloat(optie.dataset.lat);
            const lon = parseFloat(optie.dataset.lon);
            if (Number.isFinite(lat) && Number.isFinite(lon)) zetKlantLocatie(lat, lon);
        });

        /* Bezorgtarief en minimum horen bij de straal waarin het ingevoerde adres valt */
        let klantTier = null;
        let buitenGebied = false;
        let klantLat = null;
        let klantLng = null;

        const afstandKm = (aLat, aLng, bLat, bLng) => {
            const r = Math.PI / 180;
            const h = Math.sin((bLat - aLat) * r / 2) ** 2
                + Math.cos(aLat * r) * Math.cos(bLat * r) * Math.sin((bLng - aLng) * r / 2) ** 2;
            return 6371 * 2 * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
        };

        function zetKlantLocatie(lat, lon) {
            const tiers = T1_BEZORG.tiers || [];
            if (!tiers.length || T1_BEZORG.lat === null || T1_BEZORG.lng === null) return;
            klantLat = lat;
            klantLng = lon;
            const afstand = afstandKm(T1_BEZORG.lat, T1_BEZORG.lng, lat, lon);
            klantTier = tiers.find((t) => afstand <= parseFloat(t.km)) || null;
            buitenGebied = !klantTier;
            const infoMin = document.querySelector('#infoMin');
            const infoBezorg = document.querySelector('#infoBezorg');
            infoMin.classList.toggle('hidden', buitenGebied);
            if (klantTier) {
                infoMin.textContent = (klantTier.min || 0) > 0
                    ? `Minimum bestelbedrag ${euro(klantTier.min)}`
                    : 'Geen minimum bestelbedrag';
                infoBezorg.textContent = `Bezorging ${euro(klantTier.kosten)}`;
            } else {
                infoBezorg.textContent = `Buiten bezorggebied (${afstand.toFixed(1).replace('.', ',')} km)`;
            }
            renderMand();
        }

        /* Het vooringevulde adres direct omzetten naar een tarief; postcode weglaten als de zoekopdracht niets oplevert */
        (async () => {
            const adres = adresInput.value.trim();
            if (!adres) return;
            const zoek = async (q) => {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=nl&limit=1&q=${encodeURIComponent(q)}`, { headers: { 'Accept-Language': 'nl' } });
                return res.ok ? res.json() : [];
            };
            try {
                let data = await zoek(adres);
                if (!data.length) data = await zoek(adres.replace(/\b\d{4}\s?[A-Za-z]{2}\b/, '').replace(/\s{2,}/g, ' '));
                if (data[0]) zetKlantLocatie(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } catch { /* geen verbinding: de vanaf-prijzen blijven staan */ }
        })();
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#adresInput') && !e.target.closest('#adresMenu')) sluitAdresMenu();
            if (!e.target.closest('#tijdKiezer')) sluitTijdMenu();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { sluitAdresMenu(); sluitProduct(); sluitTijdMenu(); sluitOver(); }
        });

        /* Over-popup: beoordelingen en info, met kaartje van de zaak */
        let overMap = null;

        function openOver(tab) {
            document.querySelector('#overModal').classList.remove('hidden');
            kiesOverTab(tab);
        }

        function sluitOver() {
            document.querySelector('#overModal').classList.add('hidden');
        }

        function kiesOverTab(tab) {
            document.querySelectorAll('[data-over-tab]').forEach((knop) =>
                knop.classList.toggle('aan', knop.dataset.overTab === tab));
            document.querySelector('#overTabBeoordelingen').classList.toggle('hidden', tab !== 'beoordelingen');
            document.querySelector('#overTabInfo').classList.toggle('hidden', tab !== 'info');
            if (tab === 'info') setTimeout(initOverMap, 60);   // Leaflet heeft een zichtbare container nodig
        }

        function initOverMap() {
            if (typeof L === 'undefined' || T1_BEZORG.lat === null || T1_BEZORG.lng === null) return;
            if (overMap) { overMap.invalidateSize(); return; }
            overMap = L.map('overMap', { scrollWheelZoom: false, attributionControl: false })
                .setView([T1_BEZORG.lat, T1_BEZORG.lng], 15);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 19 }).addTo(overMap);
            L.marker([T1_BEZORG.lat, T1_BEZORG.lng], {
                icon: L.divIcon({
                    className: 'over-pin',
                    html: '<div class="over-pin-bol"><i class="fa-solid fa-store" aria-hidden="true"></i></div>',
                    iconSize: [44, 44],
                    iconAnchor: [22, 22],
                }),
            }).addTo(overMap);
        }

        document.querySelectorAll('[data-over-tab]').forEach((knop) =>
            knop.addEventListener('click', () => kiesOverTab(knop.dataset.overTab)));
        document.querySelector('#openBeoordelingen').addEventListener('click', (e) => { e.preventDefault(); openOver('beoordelingen'); });
        document.querySelector('#openInfoKnop').addEventListener('click', () => openOver('info'));
        document.querySelector('#overSluit').addEventListener('click', sluitOver);
        document.querySelector('#overBackdrop').addEventListener('click', sluitOver);

        /* Productmodal: openen, opties kiezen, aantal en totaalprijs */
        const euro = (centen) => '€ ' + (centen / 100).toFixed(2).replace('.', ',');
        let pmItem = null;
        let pmItemId = null;
        let pmCount = 1;
        let pmBewerkIndex = null;   // welke winkelmandregel er in de modal wordt bewerkt

        function openProduct(id) {
            pmItem = T1_MENU[id];
            if (!pmItem) return;
            pmItemId = id;
            pmCount = 1;
            pmBewerkIndex = null;
            document.querySelector('#pmNaam').textContent = pmItem.naam;
            document.querySelector('#pmPrijs').textContent = euro(pmItem.prijsCenten);
            document.querySelector('#pmBeschrijving').textContent = pmItem.beschrijving || '';
            document.querySelector('#pmBeschrijving').classList.toggle('hidden', !pmItem.beschrijving);
            document.querySelector('#pmFotoWrap').classList.toggle('hidden', !pmItem.foto);
            if (pmItem.foto) document.querySelector('#pmFoto').src = pmItem.foto;
            document.querySelector('#pmAllergenen').innerHTML = (pmItem.allergenen || []).map((a) =>
                `<span class="px-2.5 py-1 rounded-full bg-primair/10 text-[11px] font-bold text-primair-donker">${esc(a[0].toUpperCase() + a.slice(1))}</span>`).join('');
            document.querySelector('#pmOpties').innerHTML = (pmItem.opties || []).map((groep, gi) => `
                <div>
                    <div class="flex items-center justify-between gap-3 px-6 py-3 bg-wit-warm">
                        <p class="font-extrabold text-secundair">${esc(groep.naam)}</p>
                        ${groep.type === 'een'
                            ? '<span class="shrink-0 px-2.5 py-1 rounded-full bg-secundair text-[11px] font-bold text-white">1 Verplicht</span>'
                            : '<span class="shrink-0 px-2.5 py-1 rounded-full bg-black/5 text-[11px] font-bold text-secundair/55">Optioneel</span>'}
                    </div>
                    ${groep.keuzes.map((keuze, ki) => `
                        <label class="flex items-center gap-3 px-6 py-3 cursor-pointer hover:bg-wit-warm transition-colors">
                            <input type="${groep.type === 'een' ? 'radio' : 'checkbox'}" name="pmGroep${gi}" value="${ki}" class="w-5 h-5 accent-primair shrink-0">
                            <span class="flex-1 min-w-0 text-sm font-semibold text-secundair">${esc(keuze.naam)}</span>
                            ${keuze.prijs > 0 ? `<span class="shrink-0 text-sm font-semibold text-secundair/50">+ ${euro(keuze.prijs)}</span>` : ''}
                        </label>`).join('')}
                </div>`).join('');
            werkProductBij();
            document.querySelector('#productModal').classList.remove('hidden');
        }

        function sluitProduct() {
            document.querySelector('#productModal').classList.add('hidden');
            pmItem = null;
            pmBewerkIndex = null;
        }

        /* Een winkelmandregel opnieuw openen in de modal, met de gekozen opties aangevinkt */
        function bewerkRegel(i) {
            const regel = mand[i];
            const id = String(regel.sleutel).split('|')[0];
            if (!T1_MENU[id]) return;
            openProduct(id);
            pmBewerkIndex = i;
            pmCount = regel.aantal;
            const rest = [...regel.opties];
            (pmItem.opties || []).forEach((groep, gi) => {
                groep.keuzes.forEach((keuze, ki) => {
                    const idx = rest.findIndex((o) => o.naam === keuze.naam && o.prijs === keuze.prijs);
                    if (idx === -1) return;
                    rest.splice(idx, 1);
                    document.querySelector(`input[name="pmGroep${gi}"][value="${ki}"]`).checked = true;
                });
            });
            werkProductBij();
        }

        function pmKeuzes() {
            return [...document.querySelectorAll('#pmOpties input:checked')].map((inp) => {
                const gi = Number(inp.name.replace('pmGroep', ''));
                return pmItem.opties[gi].keuzes[Number(inp.value)];
            });
        }

        function werkProductBij() {
            const totaal = (pmItem.prijsCenten + pmKeuzes().reduce((som, keuze) => som + keuze.prijs, 0)) * pmCount;
            const verplichtOk = (pmItem.opties || []).every((groep, gi) =>
                groep.type !== 'een' || document.querySelector(`input[name="pmGroep${gi}"]:checked`));
            const knop = document.querySelector('#pmToevoegen');
            knop.textContent = `${pmBewerkIndex !== null ? 'Opslaan' : 'Toevoegen'} ${euro(totaal)}`;
            knop.disabled = !verplichtOk;
            knop.className = 'flex-1 py-3.5 rounded-full font-bold transition-colors ' + (verplichtOk
                ? 'bg-primair text-white hover:bg-primair-donker cursor-pointer'
                : 'bg-black/10 text-secundair/40 cursor-not-allowed');
            document.querySelector('#pmAantal').textContent = pmCount;
        }

        document.querySelectorAll('[data-gerecht]').forEach((kaart) =>
            kaart.addEventListener('click', () => openProduct(kaart.dataset.gerecht)));
        document.querySelector('#productSluit').addEventListener('click', sluitProduct);
        document.querySelector('#productBackdrop').addEventListener('click', sluitProduct);
        document.querySelector('#pmOpties').addEventListener('change', werkProductBij);
        document.querySelector('#pmMin').addEventListener('click', () => { if (pmCount > 1) { pmCount--; werkProductBij(); } });
        document.querySelector('#pmPlus').addEventListener('click', () => { pmCount++; werkProductBij(); });
        document.querySelector('#pmToevoegen').addEventListener('click', () => {
            if (document.querySelector('#pmToevoegen').disabled || !pmItem) return;
            const opties = pmKeuzes().map((keuze) => ({ naam: keuze.naam, prijs: keuze.prijs }));
            const sleutel = pmItemId + '|' + opties.map((o) => o.naam).sort().join(',');
            if (pmBewerkIndex !== null) {
                const oud = mand[pmBewerkIndex];
                mand[pmBewerkIndex] = { sleutel, naam: pmItem.naam, prijsCenten: pmItem.prijsCenten, opties, aantal: pmCount, opmerking: oud.opmerking };
            } else {
                const bestaand = mand.find((regel) => regel.sleutel === sleutel);
                if (bestaand) bestaand.aantal += pmCount;
                else mand.push({ sleutel, naam: pmItem.naam, prijsCenten: pmItem.prijsCenten, opties, aantal: pmCount });
            }
            bewaarMand();
            renderMand();
            sluitProduct();
        });

        /* De winkelmand rechts: regels, aantallen en totaal (bewaard in localStorage) */
        let mand = [];
        let opmOpen = null;      // index van de regel waar het opmerkingveld open staat
        let gekozenTijd = '';    // gekozen moment om vooruit te bestellen als de zaak dicht is
        try { mand = JSON.parse(localStorage.getItem(`${OPSLAG}_mand`)) || []; } catch { mand = []; }

        /* Custom dropdown voor het bezorgmoment: klapt boven de knop open */
        function sluitTijdMenu() {
            document.querySelector('#tijdMenu')?.classList.add('hidden');
            document.querySelector('#tijdPijl')?.classList.remove('rotate-180');
        }

        document.querySelector('#tijdKnop')?.addEventListener('click', () => {
            const menu = document.querySelector('#tijdMenu');
            menu.classList.toggle('hidden');
            document.querySelector('#tijdPijl').classList.toggle('rotate-180', !menu.classList.contains('hidden'));
        });

        document.querySelector('#tijdMenu')?.addEventListener('click', (e) => {
            const optie = e.target.closest('.tijd-optie');
            if (!optie) return;
            gekozenTijd = optie.dataset.tijd;
            document.querySelector('#tijdLabel').textContent = gekozenTijd;
            document.querySelectorAll('.tijd-optie .fa-check').forEach((vink) => {
                vink.style.display = vink.closest('.tijd-optie') === optie ? '' : 'none';
            });
            sluitTijdMenu();
            renderMand();
        });
        const bewaarMand = () => localStorage.setItem(`${OPSLAG}_mand`, JSON.stringify(mand));
        const regelPrijs = (regel) => (regel.prijsCenten + regel.opties.reduce((som, o) => som + o.prijs, 0)) * regel.aantal;

        function renderMand() {
            const leeg = !mand.length;
            document.querySelector('#mandLeeg').classList.toggle('hidden', !leeg);
            document.querySelector('#mandLijst').classList.toggle('hidden', leeg);
            document.querySelector('#mandOnder').classList.toggle('hidden', leeg);
            document.querySelector('#mandRegels').innerHTML = mand.map((regel, i) => `
                <div class="py-4">
                    <div data-mand-open="${i}" class="cursor-pointer group">
                        <div class="flex items-start justify-between gap-3">
                            <p class="flex-1 min-w-0 text-sm font-bold text-white group-hover:underline underline-offset-4 decoration-white/40">${esc(regel.naam)}</p>
                            <p class="shrink-0 text-sm font-bold text-white">${euro(regelPrijs(regel))}</p>
                        </div>
                        ${regel.opties.map((o) => `<p class="mt-1 text-xs font-semibold text-white/50">${esc(o.naam)}</p>`).join('')}
                        ${regel.opmerking && opmOpen !== i ? `<p class="mt-1 text-xs font-semibold italic text-white/40">"${esc(regel.opmerking)}"</p>` : ''}
                    </div>
                    <div class="mt-2.5 flex items-center justify-between gap-3">
                        <button type="button" data-mand-opm="${i}" class="text-xs font-bold text-white underline underline-offset-4 decoration-white/40 hover:decoration-white transition-colors cursor-pointer">
                            ${regel.opmerking ? 'Opmerking bewerken' : 'Opmerking toevoegen'}
                        </button>
                        <div class="flex items-center gap-1 shrink-0 rounded-full bg-white/10 p-1">
                            <button type="button" data-mand-min="${i}" class="w-7 h-7 rounded-full grid place-items-center text-white/70 hover:bg-white/10 transition-colors cursor-pointer" aria-label="Minder">
                                <i class="fa-solid ${regel.aantal === 1 ? 'fa-trash-can text-xs' : 'fa-minus'}" aria-hidden="true"></i>
                            </button>
                            <span class="w-6 text-center text-sm font-bold text-white">${regel.aantal}</span>
                            <button type="button" data-mand-plus="${i}" class="w-7 h-7 rounded-full grid place-items-center text-white/70 hover:bg-white/10 transition-colors cursor-pointer" aria-label="Meer">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    ${opmOpen === i ? `
                        <input id="mandOpmVeld" type="text" maxlength="140" placeholder="Bijv. geen ui, extra saus"
                            value="${esc(regel.opmerking || '')}"
                            class="mt-2.5 w-full rounded-xl bg-white/10 border border-white/10 px-3.5 py-2.5 text-sm font-semibold text-white placeholder:text-white/35 outline-none focus:border-primair transition-colors">` : ''}
                </div>`).join('');
            const subtotaal = mand.reduce((som, regel) => som + regelPrijs(regel), 0);
            const bezorgen = document.querySelector('#typeToggle button.aan')?.dataset.type !== 'afhalen';
            const vanafTarief = (T1_BEZORG.tiers && T1_BEZORG.tiers[0]) ? T1_BEZORG.tiers[0].kosten : 250;
            const bezorg = bezorgen && !buitenGebied ? (klantTier ? klantTier.kosten : vanafTarief) : 0;
            document.querySelector('#mandSubtotaal').textContent = euro(subtotaal);
            document.querySelector('#mandBezorgRij').classList.toggle('hidden', !bezorgen);
            document.querySelector('#mandBezorg').textContent = euro(bezorg);
            const buitenBlok = bezorgen && buitenGebied;
            const geslotenBlok = !T1_OPEN.open && !gekozenTijd;
            const minBedrag = bezorgen && !buitenGebied
                ? Number((klantTier ? klantTier.min : T1_BEZORG.tiers?.[0]?.min) || 0)
                : 0;
            const onderMinimum = subtotaal > 0 && minBedrag > 0 && subtotaal < minBedrag;
            document.querySelector('#mandMelding').classList.toggle('hidden', !buitenBlok);
            document.querySelector('#mandGesloten').classList.toggle('hidden', T1_OPEN.open);
            document.querySelector('#mandMinimum').classList.toggle('hidden', !onderMinimum);
            if (onderMinimum) {
                document.querySelector('#mandMinimumTekst').textContent =
                    `Het minimum bestelbedrag voor bezorgen is ${euro(minBedrag)}. Voeg nog ${euro(minBedrag - subtotaal)} toe om te kunnen bestellen.`;
            }
            const geblokkeerd = buitenBlok || geslotenBlok || onderMinimum;
            const afrekenen = document.querySelector('#mandAfrekenen');
            afrekenen.disabled = geblokkeerd;
            afrekenen.classList.toggle('opacity-40', geblokkeerd);
            afrekenen.classList.toggle('cursor-not-allowed', geblokkeerd);
            afrekenen.classList.toggle('cursor-pointer', !geblokkeerd);
            afrekenen.classList.toggle('hover:bg-primair-donker', !geblokkeerd);
            document.querySelector('#mandTotaal').textContent = euro(subtotaal + bezorg);
            const veld = document.querySelector('#mandOpmVeld');
            if (veld) {
                veld.focus();
                veld.setSelectionRange(veld.value.length, veld.value.length);
                const bewaarOpm = () => {
                    if (opmOpen === null) return;
                    mand[opmOpen].opmerking = veld.value.trim();
                    opmOpen = null;
                    bewaarMand();
                    renderMand();
                };
                veld.addEventListener('keydown', (e) => { if (e.key === 'Enter') bewaarOpm(); });
                veld.addEventListener('blur', bewaarOpm);
            }
        }

        /* Door naar afrekenen: bezorgkeuze, adres, tarief en gekozen tijd gaan mee */
        document.querySelector('#mandAfrekenen').addEventListener('click', () => {
            if (document.querySelector('#mandAfrekenen').disabled || !mand.length) return;
            localStorage.setItem(`${OPSLAG}_afrekenen`, JSON.stringify({
                type: document.querySelector('#typeToggle button.aan')?.dataset.type || 'bezorgen',
                adres: adresInput.value.trim(),
                lat: klantLat,
                lng: klantLng,
                tier: klantTier,
                kosten: klantTier ? klantTier.kosten : ((T1_BEZORG.tiers && T1_BEZORG.tiers[0]) ? T1_BEZORG.tiers[0].kosten : 250),
                tijd: gekozenTijd,
            }));
            location.href = `${T1_BASIS}/afrekenen`;
        });

        document.querySelector('#mandRegels').addEventListener('click', (e) => {
            const min = e.target.closest('[data-mand-min]');
            const plus = e.target.closest('[data-mand-plus]');
            const opm = e.target.closest('[data-mand-opm]');
            const open = e.target.closest('[data-mand-open]');
            if (open) return bewerkRegel(Number(open.dataset.mandOpen));
            if (opm) { opmOpen = Number(opm.dataset.mandOpm); return renderMand(); }
            if (min) {
                const idx = Number(min.dataset.mandMin);
                const regel = mand[idx];
                regel.aantal > 1 ? regel.aantal-- : mand.splice(idx, 1);
                if (opmOpen === idx) opmOpen = null;
            }
            if (plus) mand[Number(plus.dataset.mandPlus)].aantal++;
            if (min || plus) { bewaarMand(); renderMand(); }
        });
        renderMand();

        /* Zoeken in het menu: bij elke letter filteren op naam en omschrijving */
        document.querySelector('#menuZoek').addEventListener('input', (e) => {
            const zoek = e.target.value.trim().toLowerCase();
            let gevonden = 0;
            document.querySelectorAll('[data-categorie]').forEach((sectie) => {
                let zichtbaar = 0;
                sectie.querySelectorAll('[data-gerecht]').forEach((kaart) => {
                    const raak = !zoek || kaart.textContent.toLowerCase().includes(zoek);
                    kaart.style.display = raak ? '' : 'none';
                    if (raak) zichtbaar++;
                });
                sectie.style.display = zichtbaar ? '' : 'none';
                gevonden += zichtbaar;
            });
            document.querySelector('#menuLeegMelding').classList.toggle('hidden', gevonden > 0);
        });

        /* Categorie-chips: actieve chip wisselen */
        document.querySelectorAll('.cat-chip').forEach((chip) => chip.addEventListener('click', () => {
            document.querySelectorAll('.cat-chip').forEach((c) => c.classList.toggle('aan', c === chip));
        }));

        /* Toggle Bezorgen of Afhalen: het schuifje glijdt naar de actieve keuze */
        document.querySelectorAll('#typeToggle button').forEach((knop) => knop.addEventListener('click', () => {
            document.querySelectorAll('#typeToggle button').forEach((b) => b.classList.toggle('aan', b === knop));
            document.querySelector('#typeSchuif').style.transform = knop.dataset.type === 'afhalen' ? 'translateX(100%)' : 'translateX(0)';
            renderMand();   // bezorgkosten en totaal wisselen mee met bezorgen of afhalen
        }));
    </script>
</body>
</html>
