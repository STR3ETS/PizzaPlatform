<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $naam }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --font-sans: "Archivo", sans-serif;
            --color-primair: {{ $palet['primair'] }};
            --color-primair-donker: {{ $palet['primairDonker'] }};
            --color-secundair: {{ $palet['secundair'] }};
            --color-secundair-licht: {{ $palet['secundairLicht'] }};
            --color-wit-warm: {{ $palet['witWarm'] }};
        }
        body { font-family: var(--font-sans); }
        .blok { font-family: "Anton", "Arial Narrow", sans-serif; text-transform: uppercase; letter-spacing: .015em; }
        .over-pin-bol {
            width: 2.75rem; height: 2.75rem; border-radius: 9999px;
            background: #fff; border: 3px solid var(--color-primair);
            display: grid; place-items: center; color: var(--color-primair);
            font-size: 1.05rem; box-shadow: 0 6px 16px rgb(0 0 0 / .25);
        }
    </style>
</head>
<body class="bg-wit-warm text-secundair">
    {{-- Kopbalk: wordmark links, dikke mand-knop rechts --}}
    <header class="sticky top-0 z-40 bg-wit-warm border-b border-secundair/15">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-4">
            <div class="w-10 h-10 border border-secundair/20 bg-white grid place-items-center overflow-hidden shrink-0">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-pizza-slice text-primair" aria-hidden="true"></i>
                @endif
            </div>
            <p class="blok text-lg truncate">{{ $naam }}</p>
            <button type="button" data-mand-open-knop class="ml-auto flex items-center gap-2.5 px-4 py-2.5 bg-primair text-white blok text-sm hover:bg-primair-donker transition-colors cursor-pointer">
                <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>
                <span class="hidden sm:inline">Mand</span>
                <span class="mand-badge min-w-5 h-5 px-1 bg-white text-secundair text-xs font-extrabold grid place-items-center" style="display:none">0</span>
            </button>
        </div>
    </header>

    {{-- Fotoband met de naam eroverheen, zoals een menubord --}}
    <section class="relative h-72 sm:h-80 overflow-hidden">
        <img src="{{ asset('assets/eten/oven.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-black/10"></div>
        <div class="absolute inset-x-0 bottom-0">
            <div class="max-w-6xl mx-auto px-6 pb-6">
                <h1 class="blok text-6xl sm:text-7xl leading-[0.95] text-white">{{ $naam }}</h1>
            </div>
        </div>
    </section>

    {{-- Metaregel en bezorgadres, vlak en zakelijk --}}
    <section class="max-w-6xl mx-auto px-6 pt-6 pb-10">
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-bold">
            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-star text-primair" aria-hidden="true"></i>
                4,8 <a href="#" id="openBeoordelingen" class="text-secundair/55 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors">(1.200+)</a>
            </span>
            <span class="inline-flex items-center gap-2 {{ $openInfo['open'] ? 'text-secundair' : 'text-secundair/50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $openInfo['open'] ? 'bg-primair' : 'bg-secundair/25' }}"></span>
                {{ $openInfo['open'] ? 'Nu geopend' : 'Gesloten' }}
            </span>
            <span id="infoMin" class="text-secundair/70">Minimum bestelbedrag &euro; {{ number_format(($laagsteTier['min'] ?? 1500) / 100, 2, ',', '.') }}</span>
            <span id="infoBezorg" class="text-secundair/70">Bezorging vanaf &euro; {{ number_format(($laagsteTier['kosten'] ?? 250) / 100, 2, ',', '.') }}</span>
            <button type="button" id="openInfoKnop" class="text-secundair/55 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors cursor-pointer">Over ons</button>
        </div>

        <div class="max-w-xl mt-8">
            <p class="blok text-xs tracking-wide text-secundair/60">Bezorgadres</p>
            <div class="relative mt-2">
                <div class="flex items-center gap-3 border border-secundair/20 bg-white px-4 py-3.5 focus-within:border-secundair/50 transition-colors">
                    <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                    <input id="adresInput" type="text" value="Stationsplein 12, 6811 KG Arnhem" placeholder="Straat, huisnummer en plaats" autocomplete="off"
                        class="flex-1 min-w-0 text-sm bg-transparent outline-none font-bold text-secundair placeholder:text-secundair/35">
                </div>
                <div id="adresMenu" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-secundair/20 shadow-lg overflow-hidden divide-y divide-secundair/10 z-20 text-left"></div>
            </div>
        </div>
    </section>

    {{-- Sticky balk met zoeken en categorie-tags --}}
    <div class="sticky top-16 z-30 bg-wit-warm border-y border-secundair/15">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-3">
            <div class="flex items-center gap-3 border border-secundair/20 bg-white px-3.5 py-2 w-52 sm:w-64 shrink-0">
                <i class="fa-solid fa-magnifying-glass text-primair text-sm" aria-hidden="true"></i>
                <input id="menuZoek" type="text" placeholder="Zoeken" class="flex-1 min-w-0 text-sm bg-transparent outline-none font-bold text-secundair placeholder:text-secundair/35">
            </div>
            <div class="flex-1 min-w-0 flex items-center gap-2 overflow-x-auto [scrollbar-width:none]">
                @foreach($categorieen as $categorie)
                    <button type="button" class="cat-chip {{ $loop->first ? 'aan' : '' }} shrink-0 border border-secundair/20 bg-white px-3.5 py-2 blok text-xs [&.aan]:bg-secundair [&.aan]:text-white transition-colors cursor-pointer">{{ $categorie }}</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Menukaart: inktbalken en dik omrande kaarten --}}
    <main class="max-w-6xl mx-auto px-6 py-10 pb-32">
        @foreach($menu as $categorie => $items)
            <section class="mt-12 first:mt-0 scroll-mt-24" data-categorie="{{ $categorie }}">
                <div class="flex items-baseline gap-3 border-b border-secundair/15 pb-3">
                    <h2 class="blok text-3xl">{{ $categorie }}</h2>
                    <span class="blok text-sm text-primair">{{ sprintf('%02d', $loop->iteration) }}</span>
                </div>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($items as $item)
                        <article data-gerecht="{{ $item['id'] }}" class="group flex items-stretch border border-secundair/20 bg-white hover:border-secundair/40 transition-colors cursor-pointer">
                            @if($item['foto'])
                                <div class="w-28 sm:w-40 shrink-0 border-r border-secundair/15">
                                    <img src="{{ $item['foto'] }}" alt="" class="w-full aspect-square object-cover">
                                </div>
                            @endif
                            <div class="flex-1 min-w-0 flex flex-col p-4">
                                <p class="blok text-base leading-tight">{{ $item['naam'] }}</p>
                                @if($item['beschrijving'])
                                    <p class="mt-1.5 text-sm font-semibold text-secundair/60 line-clamp-2">{{ $item['beschrijving'] }}</p>
                                @endif
                                @if(!empty($item['allergenen']))
                                    <p class="mt-1.5 text-[11px] font-extrabold uppercase tracking-wide text-secundair/40">Allergenen: {{ implode(', ', $item['allergenen']) }}</p>
                                @endif
                                <div class="mt-auto pt-3 flex items-end justify-between gap-3">
                                    <p class="blok text-2xl text-primair">&euro; {{ $item['prijs'] }}</p>
                                    <span class="w-10 h-10 shrink-0 grid place-items-center bg-secundair text-white group-hover:bg-primair transition-colors" aria-hidden="true">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div id="menuLeegMelding" class="hidden mt-14 border border-secundair/20 bg-white p-8 text-center">
            <p class="blok text-2xl">Niks gevonden</p>
            <p class="mt-2 text-sm font-semibold text-secundair/60">Geen gerechten gevonden, probeer een andere zoekopdracht.</p>
        </div>
    </main>

    {{-- Zwevende mand-knop --}}
    <button type="button" data-mand-open-knop class="fixed bottom-6 right-6 z-40 flex items-center gap-3 px-5 py-4 bg-primair text-white blok text-sm shadow-lg hover:bg-primair-donker transition-colors cursor-pointer">
        <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>
        Mand
        <span class="mand-badge min-w-5 h-5 px-1 bg-white text-secundair text-xs font-extrabold grid place-items-center" style="display:none">0</span>
    </button>

    {{-- De winkelmand als drawer met dikke linkerrand --}}
    <div id="mandDrawer" class="fixed inset-0 z-50 pointer-events-none">
        <div id="mandDrawerBackdrop" class="absolute inset-0 bg-black/50 opacity-0 transition-opacity duration-300"></div>
        <aside id="mandPaneel" class="absolute right-0 top-0 h-full w-full max-w-md bg-wit-warm border-l border-secundair/20 translate-x-full transition-transform duration-300 flex flex-col p-6">
            <div class="flex items-center gap-3 pb-4 border-b border-secundair/15">
                <p class="blok text-xl flex-1">Winkelmand</p>
                <button type="button" id="mandSluit" class="w-9 h-9 border border-secundair/20 bg-white grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Sluiten">
                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div id="typeToggle" class="relative grid grid-cols-2 p-1 border border-secundair/20 bg-white mt-4">
                <span id="typeSchuif" class="absolute top-1 bottom-1 left-1 w-[calc(50%-0.25rem)] bg-primair transition-transform duration-300 ease-out"></span>
                <button type="button" data-type="bezorgen"
                    class="aan relative z-10 py-2.5 blok text-xs text-secundair/60 [&.aan]:text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-motorcycle" aria-hidden="true"></i> Bezorgen
                </button>
                <button type="button" data-type="afhalen"
                    class="relative z-10 py-2.5 blok text-xs text-secundair/60 [&.aan]:text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Afhalen
                </button>
            </div>

            <div id="mandLeeg" class="flex-1 min-h-0">
                <div class="h-full grid place-items-center">
                    <div class="text-center">
                        <i class="fa-solid fa-basket-shopping text-3xl text-secundair/25" aria-hidden="true"></i>
                        <p class="mt-3 blok text-sm text-secundair/45">Winkelmand leeg</p>
                    </div>
                </div>
            </div>

            <div id="mandLijst" class="hidden flex-1 min-h-0 mt-3">
                <div class="h-full overflow-y-auto divide-y divide-secundair/10" id="mandRegels"></div>
            </div>
            <div id="mandOnder" class="hidden pt-4 mt-4 border-t border-secundair/15">
                <div class="space-y-1.5 text-sm font-bold text-secundair/70">
                    <div class="flex items-start justify-between gap-3">
                        <span>Subtotaal</span>
                        <span id="mandSubtotaal" class="shrink-0"></span>
                    </div>
                    <div id="mandBezorgRij" class="flex items-start justify-between gap-3">
                        <span>Bezorgkosten <i class="fa-solid fa-circle-info text-secundair/35" aria-hidden="true"></i></span>
                        <span id="mandBezorg" class="shrink-0"></span>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="blok text-sm">Totaal</span>
                    <span id="mandTotaal" class="blok text-lg"></span>
                </div>
                <div id="mandMelding" class="hidden mt-3">
                    <div class="border border-secundair/20 bg-primair/10 px-3.5 py-2.5">
                        <p class="text-xs font-extrabold text-secundair">Dit adres ligt buiten ons bezorggebied. Kies een ander adres of ga voor afhalen.</p>
                    </div>
                </div>
                <div id="mandMinimum" class="hidden mt-3">
                    <div class="border border-secundair/20 bg-primair/10 px-3.5 py-2.5">
                        <p id="mandMinimumTekst" class="text-xs font-extrabold text-secundair"></p>
                    </div>
                </div>
                <div id="mandGesloten" class="hidden mt-3">
                    <div class="border border-secundair/20 bg-primair/10 px-3.5 py-2.5">
                        <p class="text-xs font-extrabold text-secundair">{{ ($openInfo['pauze'] ?? false) ? 'We nemen op dit moment even geen bestellingen aan. Probeer het straks opnieuw.' : 'We zijn op dit moment gesloten. Je kunt wel alvast bestellen voor een ander moment.' }}</p>
                        @if(count($openInfo['slots']))
                            <div id="tijdKiezer" class="relative mt-2.5">
                                <button type="button" id="tijdKnop" class="w-full flex items-center justify-between gap-3 border border-secundair/20 bg-white px-3.5 py-2.5 text-sm font-bold text-secundair hover:bg-wit-warm transition-colors cursor-pointer">
                                    <span id="tijdLabel">Kies een moment</span>
                                    <i id="tijdPijl" class="fa-solid fa-chevron-down text-xs text-secundair/50 transition-transform duration-200" aria-hidden="true"></i>
                                </button>
                                <div id="tijdMenu" class="hidden absolute left-0 right-0 bottom-full mb-2 max-h-56 overflow-y-auto bg-white border border-secundair/20 z-30 divide-y divide-secundair/10">
                                    @foreach($openInfo['slots'] as $slot)
                                        <button type="button" data-tijd="{{ $slot }}" class="tijd-optie w-full flex items-center justify-between gap-3 px-3.5 py-2.5 text-left text-sm font-bold text-secundair hover:bg-wit-warm transition-colors cursor-pointer">
                                            <span>{{ $slot }}</span>
                                            <i class="fa-solid fa-check text-primair" style="display:none" aria-hidden="true"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" id="mandAfrekenen" class="mt-4 w-full py-3.5 bg-primair text-white blok text-sm hover:bg-primair-donker transition-colors cursor-pointer">Ga naar afrekenen</button>
            </div>
        </aside>
    </div>

    {{-- Productmodal --}}
    <div id="productModal" class="hidden fixed inset-0 z-50">
        <div id="productBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 grid place-items-center p-4 sm:p-8 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg max-h-[85vh] flex flex-col bg-white border border-secundair/20 overflow-hidden">
                <div class="flex items-start gap-3 px-6 pt-6 pb-4">
                    <h2 id="pmNaam" class="flex-1 min-w-0 blok text-2xl leading-tight"></h2>
                    <button type="button" id="productSluit" class="w-9 h-9 -mr-2 -mt-1 shrink-0 border border-secundair/20 bg-white grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div id="pmFotoWrap" class="hidden px-6 pb-4">
                        <img id="pmFoto" src="" alt="" class="w-full h-48 object-cover border border-secundair/20">
                    </div>
                    <div class="px-6 pb-5">
                        <p id="pmPrijs" class="blok text-xl text-primair"></p>
                        <p id="pmBeschrijving" class="mt-1.5 text-sm font-semibold text-secundair/60"></p>
                        <div id="pmAllergenen" class="mt-2.5"></div>
                    </div>
                    <div id="pmOpties"></div>
                </div>
                <div class="flex items-center gap-3 px-6 py-4 border-t border-secundair/15">
                    <div class="flex items-center gap-1 border border-secundair/20 p-1">
                        <button type="button" id="pmMin" class="w-9 h-9 grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Minder">
                            <i class="fa-solid fa-minus" aria-hidden="true"></i>
                        </button>
                        <span id="pmAantal" class="w-8 text-center font-extrabold">1</span>
                        <button type="button" id="pmPlus" class="w-9 h-9 grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Meer">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button type="button" id="pmToevoegen" class="flex-1 py-3.5 blok text-sm transition-all cursor-pointer"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Over-popup --}}
    <div id="overModal" class="hidden fixed inset-0 z-50">
        <div id="overBackdrop" class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 grid place-items-center p-4 sm:p-8 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg max-h-[85vh] flex flex-col bg-white border border-secundair/20 overflow-hidden">
                <div class="flex items-start gap-3 px-6 pt-6 pb-3">
                    <h2 class="flex-1 min-w-0 blok text-2xl leading-tight">Over {{ $naam }}</h2>
                    <button type="button" id="overSluit" class="w-9 h-9 -mr-2 -mt-1 shrink-0 border border-secundair/20 bg-white grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex gap-2 px-6 pb-4">
                    <button type="button" data-over-tab="beoordelingen" class="border border-secundair/20 px-3.5 py-2 blok text-xs text-secundair/60 [&.aan]:bg-secundair [&.aan]:text-white transition-colors cursor-pointer">Beoordelingen</button>
                    <button type="button" data-over-tab="info" class="border border-secundair/20 px-3.5 py-2 blok text-xs text-secundair/60 [&.aan]:bg-secundair [&.aan]:text-white transition-colors cursor-pointer">Info</button>
                </div>
                <div class="flex-1 overflow-y-auto border-t border-secundair/15 pt-5">
                    <div id="overTabBeoordelingen">
                        <div class="px-6 pb-4 flex items-baseline gap-4">
                            <p class="blok text-5xl">4,8</p>
                            <div>
                                <p class="flex items-center gap-1 text-primair">
                                    @for($i = 0; $i < 5; $i++) <i class="fa-solid fa-star text-sm" aria-hidden="true"></i> @endfor
                                </p>
                                <p class="mt-1 text-xs font-bold text-secundair/55">1.200+ beoordelingen</p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 space-y-3">
                            @foreach([
                                ['naam' => 'Sanne', 'sterren' => 5, 'tijd' => '3 dagen geleden', 'tekst' => 'Wat een aanrader, alles vers en de bezorging was keurig op tijd.'],
                                ['naam' => 'Mehmet', 'sterren' => 5, 'tijd' => '1 week geleden', 'tekst' => 'Snelle bezorging en alles was nog goed warm. Aanrader!'],
                                ['naam' => 'Kim', 'sterren' => 4, 'tijd' => '2 weken geleden', 'tekst' => 'Ruime porties, volgende keer graag iets meer saus.'],
                                ['naam' => 'Jeroen', 'sterren' => 5, 'tijd' => '3 weken geleden', 'tekst' => 'Bestellen gaat makkelijk en het is altijd op tijd.'],
                            ] as $review)
                                <div class="border border-secundair/20 bg-white p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-extrabold uppercase">{{ $review['naam'] }}</p>
                                        <p class="text-xs font-bold text-secundair/45">{{ $review['tijd'] }}</p>
                                    </div>
                                    <p class="mt-1 flex items-center gap-0.5 text-primair">
                                        @for($i = 0; $i < $review['sterren']; $i++) <i class="fa-solid fa-star text-xs" aria-hidden="true"></i> @endfor
                                    </p>
                                    <p class="mt-1.5 text-sm font-semibold text-secundair/70">{{ $review['tekst'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="overTabInfo" class="hidden">
                        <div class="px-6 pb-5">
                            <div id="overMap" class="h-56 border border-secundair/20 z-0" style="background:var(--color-wit-warm)"></div>
                            <div class="mt-3 flex items-start gap-3">
                                <i class="fa-solid fa-location-dot text-primair mt-1" aria-hidden="true"></i>
                                <div>
                                    <p class="text-sm font-extrabold">{{ $overInfo['straat'] }}</p>
                                    <p class="text-sm font-bold text-secundair/60">{{ trim($overInfo['postcode'] . ' ' . $overInfo['plaats']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-5">
                            <p class="blok text-xs tracking-wide">Openingstijden</p>
                            <div class="mt-2.5 space-y-1.5">
                                @foreach($overInfo['dagen'] as $dag)
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="font-bold text-secundair/60">{{ $dag['naam'] }}</span>
                                        <span class="font-extrabold {{ $dag['open'] ? 'text-secundair' : 'text-secundair/40' }}">{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            <p class="blok text-xs tracking-wide">Bezorgkosten</p>
                            <div class="mt-2.5 space-y-1.5">
                                @forelse($bezorg['tiers'] as $tier)
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="font-bold text-secundair/60">tot {{ rtrim(rtrim(number_format((float) $tier['km'], 1, ',', '.'), '0'), ',') }} km</span>
                                        <span class="font-extrabold">
                                            &euro; {{ number_format($tier['kosten'] / 100, 2, ',', '.') }}
                                            @if(($tier['min'] ?? 0) > 0)
                                                <span class="font-bold text-secundair/50">(min. &euro; {{ number_format($tier['min'] / 100, 2, ',', '.') }})</span>
                                            @endif
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm font-bold text-secundair/50">Geen bezorgkosten ingesteld.</p>
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
        /* De winkelmand-drawer schuift van rechts in beeld */
        function openMand() {
            document.querySelector('#mandDrawer').classList.remove('pointer-events-none');
            document.querySelector('#mandDrawerBackdrop').classList.remove('opacity-0');
            document.querySelector('#mandPaneel').classList.remove('translate-x-full');
        }
        function sluitMandDrawer() {
            document.querySelector('#mandDrawer').classList.add('pointer-events-none');
            document.querySelector('#mandDrawerBackdrop').classList.add('opacity-0');
            document.querySelector('#mandPaneel').classList.add('translate-x-full');
        }
        document.querySelectorAll('[data-mand-open-knop]').forEach((knop) => knop.addEventListener('click', openMand));
        document.querySelector('#mandSluit').addEventListener('click', sluitMandDrawer);
        document.querySelector('#mandDrawerBackdrop').addEventListener('click', sluitMandDrawer);

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
                        adresMenu.innerHTML = '<p class="px-5 py-3.5 text-sm font-bold text-secundair/50">Geen adres gevonden</p>';
                        adresMenu.classList.remove('hidden');
                        return;
                    }
                    adresMenu.innerHTML = data.map((hit) => {
                        const a = hit.address || {};
                        const straat = [a.road || a.pedestrian || a.square, a.house_number].filter(Boolean).join(' ');
                        const plaats = [a.postcode, a.city || a.town || a.village || a.municipality].filter(Boolean).join(' ');
                        const label = [straat, plaats].filter(Boolean).join(', ') || hit.display_name.split(',').slice(0, 3).join(',');
                        return `<button type="button" class="adres-optie w-full flex items-center gap-3 px-5 py-3.5 text-left text-sm font-bold text-secundair hover:bg-wit-warm transition-colors cursor-pointer" data-adres="${esc(label)}" data-lat="${esc(hit.lat)}" data-lon="${esc(hit.lon)}">
                            <i class="fa-solid fa-location-dot text-primair" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0">
                                <span class="block truncate">${esc(label)}</span>
                                <span class="block text-xs font-semibold text-secundair/45 truncate">${esc(hit.display_name)}</span>
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
            if (e.key === 'Escape') { sluitAdresMenu(); sluitProduct(); sluitTijdMenu(); sluitOver(); sluitMandDrawer(); }
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
            document.querySelector('#pmAllergenen').innerHTML = (pmItem.allergenen || []).length
                ? `<p class="text-[11px] font-extrabold uppercase tracking-wide text-secundair/40">Allergenen: ${esc(pmItem.allergenen.join(', '))}</p>`
                : '';
            document.querySelector('#pmOpties').innerHTML = (pmItem.opties || []).map((groep, gi) => `
                <div>
                    <div class="flex items-center justify-between gap-3 px-6 py-2.5 bg-wit-warm border-y border-secundair/10">
                        <p class="blok text-xs tracking-wide text-secundair/70">${esc(groep.naam)}</p>
                        ${groep.type === 'een'
                            ? '<span class="shrink-0 blok text-xs text-primair">Verplicht</span>'
                            : '<span class="shrink-0 blok text-xs text-secundair/35">Optioneel</span>'}
                    </div>
                    ${groep.keuzes.map((keuze, ki) => `
                        <label class="flex items-center gap-3 px-6 py-3 cursor-pointer hover:bg-wit-warm transition-colors">
                            <input type="${groep.type === 'een' ? 'radio' : 'checkbox'}" name="pmGroep${gi}" value="${ki}" class="w-4 h-4 accent-primair shrink-0">
                            <span class="flex-1 min-w-0 text-sm font-bold">${esc(keuze.naam)}</span>
                            ${keuze.prijs > 0 ? `<span class="shrink-0 text-sm font-bold text-secundair/50">+ ${euro(keuze.prijs)}</span>` : ''}
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
            knop.className = 'flex-1 py-3.5 blok text-sm transition-colors ' + (verplichtOk
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
            openMand();
        });

        /* De winkelmand in de drawer: regels, aantallen en totaal (bewaard in localStorage) */
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
            const aantalTotaal = mand.reduce((som, regel) => som + regel.aantal, 0);
            document.querySelectorAll('.mand-badge').forEach((badge) => {
                badge.textContent = aantalTotaal;
                badge.style.display = aantalTotaal ? '' : 'none';
            });
            document.querySelector('#mandRegels').innerHTML = mand.map((regel, i) => `
                <div class="py-4">
                    <div data-mand-open="${i}" class="cursor-pointer group">
                        <div class="flex items-start justify-between gap-3">
                            <p class="flex-1 min-w-0 text-sm font-extrabold text-secundair group-hover:text-primair transition-colors">${esc(regel.naam)}</p>
                            <p class="shrink-0 text-sm font-extrabold text-secundair">${euro(regelPrijs(regel))}</p>
                        </div>
                        ${regel.opties.map((o) => `<p class="mt-1 text-xs font-bold text-secundair/50">${esc(o.naam)}</p>`).join('')}
                        ${regel.opmerking && opmOpen !== i ? `<p class="mt-1 text-xs font-bold italic text-secundair/40">"${esc(regel.opmerking)}"</p>` : ''}
                    </div>
                    <div class="mt-2.5 flex items-center justify-between gap-3">
                        <button type="button" data-mand-opm="${i}" class="text-xs font-extrabold text-secundair underline underline-offset-4 decoration-secundair/40 hover:decoration-primair hover:text-primair transition-colors cursor-pointer">
                            ${regel.opmerking ? 'Opmerking bewerken' : 'Opmerking toevoegen'}
                        </button>
                        <div class="flex items-center gap-1 shrink-0 border border-secundair/20 bg-white p-1">
                            <button type="button" data-mand-min="${i}" class="w-7 h-7 grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Minder">
                                <i class="fa-solid ${regel.aantal === 1 ? 'fa-trash-can text-xs' : 'fa-minus'}" aria-hidden="true"></i>
                            </button>
                            <span class="w-6 text-center text-sm font-extrabold text-secundair">${regel.aantal}</span>
                            <button type="button" data-mand-plus="${i}" class="w-7 h-7 grid place-items-center text-secundair hover:bg-secundair hover:text-white transition-colors cursor-pointer" aria-label="Meer">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    ${opmOpen === i ? `
                        <input id="mandOpmVeld" type="text" maxlength="140" placeholder="Bijv. geen ui, extra saus"
                            value="${esc(regel.opmerking || '')}"
                            class="mt-2.5 w-full border border-secundair/20 bg-white px-3.5 py-2.5 text-sm font-bold text-secundair placeholder:text-secundair/35 outline-none focus:border-primair transition-colors">` : ''}
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

        /* Categorie-tags: actieve wisselen en ernaartoe scrollen */
        document.querySelectorAll('.cat-chip').forEach((chip, idx) => chip.addEventListener('click', () => {
            document.querySelectorAll('.cat-chip').forEach((c) => c.classList.toggle('aan', c === chip));
            document.querySelectorAll('[data-categorie]')[idx]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
