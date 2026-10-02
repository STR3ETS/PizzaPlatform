@php
    $abonnementNodig = ! auth()->user()->heeftToegang();
    // Zaak compleet? Zo niet, dan staat het venster "zaak afmaken" boven het dashboard
    $setupKlaar = (bool) (auth()->user()->onboarding['setup_compleet'] ?? false);
    // Zolang een venster de pagina blokkeert, mag de pagina eronder niet scrollen.
    // Dat moet op <html>: door overflow-x: clip (style.css) geeft <body> zijn overflow niet door aan het venster.
    $paginaOpSlot = $abonnementNodig || ! $setupKlaar;
@endphp
<!DOCTYPE html>
{{-- Het dashboard start licht. De knop in de topbalk wisselt naar donker en
     onthoudt die keuze in de browser (zie het script hieronder). --}}
<html lang="nl" data-theme="light" class="{{ $paginaOpSlot ? 'overflow-hidden' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | Shop &amp; Eat</title>
    {{-- Themakeuze uit de browser, vóór de stylesheets: zo knippert de pagina niet
         van donker naar licht bij het laden. De knop staat in de topbalk. --}}
    <script>
        try { var t = localStorage.getItem('se-thema'); if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t; } catch (e) {}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* De oude tokennamen wijzen nu naar het palet van /stijlgids. Zo lopen de
           panelen die nog niet zijn herbouwd mee in dezelfde kleuren.
           "static" zodat ook kleuren die alleen in css gebruikt worden blijven bestaan. */
        @theme static {
            --color-crema: var(--soft);         /* was ivoor, nu het zachte vlak */
            --color-crema-dark: var(--line);    /* was lijnkleur, nu --line */
            --color-tomato: var(--omgekeerd);   /* hoofdactie is het omgekeerde vlak */
            --color-tomato-dark: var(--omgekeerd);
            --color-basil: var(--omgekeerd);    /* groen is geen tekstkleur meer, alleen status */
            --color-basil-dark: var(--omgekeerd);
            --color-gold: var(--ink3);
            --color-cacao: var(--ink);
            --color-white: var(--card);         /* bg-white en text-white volgen het thema */
            --font-display: "Figtree", sans-serif;
            --font-body: "Figtree", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/systeem.css') }}?v={{ filemtime(public_path('assets/stijl/systeem.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/dashboard.css') }}?v={{ filemtime(public_path('assets/stijl/dashboard.css')) }}">
    {{-- De zaak kleurt het dashboard: de kleur die ze voor de bestelpagina kozen
         wordt hier de hoofdkleur. Geen kleur gekozen, dan blijft Tomaat, de
         huiskleur uit systeem.css. Bij een lichte kleur wordt de tekst op knoppen
         donker in plaats van wit (zie App\Support\Kleur). --}}
    @php
        $zaakPalet = collect(\App\Http\Controllers\BestelController::KLEUREN)->firstWhere('hex', auth()->user()->onboarding['color'] ?? null);
    @endphp
    @if($zaakPalet)
        <style>
            :root, :root[data-theme="dark"] { --accent: {{ $zaakPalet['palet']['primair'] }}; --accent-ink: {{ \App\Support\Kleur::inkOp($zaakPalet['palet']['primair']) }}; --accent-2: color-mix(in srgb, {{ $zaakPalet['palet']['primair'] }} 22%, #ffffff); --accent-2-ink: #1b1714; }
        </style>
    @endif
</head>
<body class="min-h-screen antialiased {{ $paginaOpSlot ? 'overflow-hidden' : '' }}">

    <!-- Zwevende navigatie-rail (desktop) -->
    <aside class="dash-rail">
        <div class="px-5 pt-6 pb-5">
            @include('deel.merk')
        </div>

        <!-- Altijd zichtbare status -->
        <button type="button" id="railStatus" data-nav="overzicht" class="rail-status cursor-pointer" title="Naar online-instellingen">
            <span id="railDot" class="status-dot"></span>
            <span class="min-w-0">
                <span id="railStatusTitel" class="block text-[13px] font-medium leading-tight">Offline</span>
                <span id="railStatusSub" class="block text-[11px] leading-tight" style="color:var(--ink3)">neemt geen bestellingen aan</span>
            </span>
        </button>

        <nav class="flex-1 space-y-1 overflow-y-auto">
            <button type="button" data-nav="overzicht" class="rail-item actief cursor-pointer"><span class="r-ico"><i class="fa-solid fa-house" aria-hidden="true"></i></span> Overzicht</button>
            <button type="button" data-nav="bestellingen" class="rail-item cursor-pointer"><span class="r-ico"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span> Bestellingen <span id="railOrdersBadge" class="n-soon" style="display:none">0</span></button>
            <button type="button" data-nav="menukaart" class="rail-item cursor-pointer"><span class="r-ico"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span> Menukaart</button>
            <button type="button" data-nav="team" class="rail-item cursor-pointer"><span class="r-ico"><i class="fa-solid fa-user-group" aria-hidden="true"></i></span> Team</button>
            <button type="button" data-nav="instellingen" class="rail-item cursor-pointer"><span class="r-ico"><i class="fa-solid fa-gear" aria-hidden="true"></i></span> Instellingen</button>
            {{-- De webshop staat los van het dagelijkse werk, vandaar de eigen sectie --}}
            <div class="!mt-4 pt-4" style="border-top:1px solid var(--line)">
                <button type="button" data-nav="webshop" class="rail-item cursor-pointer"><span class="r-ico"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i></span> Inkoop</button>
            </div>
        </nav>
        <div class="p-4" style="border-top:1px solid var(--line)">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-full grid place-items-center shrink-0 text-[13px] font-medium" style="background:var(--soft)">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-medium truncate">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[11px] cursor-pointer transition-colors hover:!text-[color:var(--ink)]" style="color:var(--ink3)">Uitloggen</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Tabbalk (mobiel) -->
    <nav class="dash-tabbar">
        <button type="button" data-nav="overzicht" class="tab-item actief" aria-label="Overzicht"><span class="t-ico"><i class="fa-solid fa-house" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="bestellingen" class="tab-item" aria-label="Bestellingen"><span class="t-ico"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="menukaart" class="tab-item" aria-label="Menukaart"><span class="t-ico"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="team" class="tab-item" aria-label="Team"><span class="t-ico"><i class="fa-solid fa-user-group" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="instellingen" class="tab-item" aria-label="Instellingen"><span class="t-ico"><i class="fa-solid fa-gear" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="overzicht" id="tabStatus" class="tab-status" aria-label="Online-status">
            <span class="ts-dot" aria-hidden="true"></span>
            <span id="tabStatusTekst">Offline</span>
        </button>
    </nav>

    <main class="relative z-10 dash-main">
        <div class="flex flex-col gap-[var(--gap)]">

        {{-- Topbalk: de dingen die je op elk paneel wilt zien: status, uren van
             vandaag, openstaande bestellingen en de klok. Plakt bij het scrollen. --}}
        @php
            $tbOb = auth()->user()->onboarding ?? [];
            $tbDag = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'][now()->dayOfWeekIso - 1];
            $tbOpen = null;
            if (! empty($tbOb['days'][$tbDag])) {
                $tbOpen = ($tbOb['hoursMode'] ?? 'same') === 'perday'
                    ? (isset($tbOb['dayTimes'][$tbDag]) ? [$tbOb['dayTimes'][$tbDag]['open'], $tbOb['dayTimes'][$tbDag]['close']] : null)
                    : [$tbOb['open'] ?? '16:00', $tbOb['close'] ?? '21:30'];
            }
        @endphp
        <div class="topbalk">
            <button type="button" data-nav="overzicht" class="los inline-flex cursor-pointer">
                <span id="topbalkDot" aria-hidden="true"></span>
                <span id="topbalkStatus">Offline</span>
            </button>
            <span class="los hidden sm:inline-flex">
                {{ $tbOpen ? 'Vandaag open van ' . $tbOpen[0] . ' tot ' . $tbOpen[1] : 'Vandaag gesloten' }}
            </span>
            <button type="button" data-nav="bestellingen" class="los inline-flex ml-auto cursor-pointer">
                <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                <span id="topbalkOrders">0 openstaand</span>
            </button>
            <span class="los hidden md:inline-flex">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span id="topbalkKlok"></span>
            </span>
            <button type="button" id="themaKnop" class="los inline-flex cursor-pointer" aria-label="Wissel naar licht" title="Wissel naar licht">
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
            </button>
        </div>

        <!-- PANEEL: Overzicht -->
        <section data-panel="overzicht" class="panel actief">
            <div class="flex flex-col gap-[var(--gap)]">

            <!-- Begroeting en online-status: het eerste wat je 's middags wilt weten -->
            @php
                // De begroeting is altijd de hoofdkleur zelf (geen lichtere ochtend of donkerdere avond) en laat, als er een gerechtfoto is, het eigen eten van de zaak doorschemeren
                // Dezelfde foto als klanten zien: eigen upload als die er is, anders de foto bij de naam of categorie
                $groetItem = auth()->user()->menuItems()->orderBy('volgorde')->orderBy('id')->first();
                $groetFoto = $groetItem ? \App\Http\Controllers\BestelController::fotoVoor($groetItem) : null;
            @endphp
            <div class="vlak app groet-vlak {{ $groetFoto ? 'met-foto' : '' }} rise flex flex-col lg:flex-row lg:items-end gap-7" style="--d:.02s;{{ $groetFoto ? ' --groet-foto:url(' . $groetFoto . ')' : '' }}">
                <div class="flex-1 min-w-0">
                    <p id="dagregel" class="label">Zo staat je zaak ervoor.</p>
                    <h1 class="groet mt-1.5">{{ now()->hour < 12 ? 'Goedemorgen' : (now()->hour < 18 ? 'Goedemiddag' : 'Goedenavond') }}, <b>{{ explode(' ', auth()->user()->name)[0] }}</b></h1>
                    <p class="status mt-4"><i id="statusDot" class="status-dot"></i><span id="statusTitel">Je bent offline</span></p>
                    <p id="vandaagTijden" class="small mt-1">…</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <div id="modeSwitch" class="mode-switch hidden">
                        <button type="button" data-mode="bezorgen_afhalen" class="mode-opt cursor-pointer">Bezorgen &amp; afhalen</button>
                        <button type="button" data-mode="alleen_afhalen" class="mode-opt cursor-pointer">Alleen afhalen</button>
                    </div>
                    <button id="onlineToggle" type="button" class="btn dark cursor-pointer">Online gaan</button>
                </div>
            </div>

            @php
                $vandaagBestellingen = auth()->user()->orders()->where('is_demo', false)->whereDate('created_at', today())->get();

                // Omzet-first cijfers en de coach: alles uit echte bestellingen (demo telt niet mee)
                $coachOrders = auth()->user()->orders()->where('is_demo', false)->where('created_at', '>=', now()->subDays(60))->get();
                $maandOrders = $coachOrders->where('created_at', '>=', now()->subDays(30));
                $gemBestelwaarde = $maandOrders->count() ? (int) round($maandOrders->avg('totaal')) : 0;
                $terugkerend = auth()->user()->orders()->where('is_demo', false)->whereNotNull('klant_id')
                    ->selectRaw('klant_id, COUNT(*) as aantal')->groupBy('klant_id')->havingRaw('COUNT(*) >= 2')->get()->count();
                $piekUren = $maandOrders->groupBy(fn ($o) => (int) $o->created_at->format('H'))->map->count()->sortDesc();
                $piekUur = $piekUren->isNotEmpty() && $piekUren->first() >= 3 ? $piekUren->keys()->first() : null;

                $weekOmzet = (int) $coachOrders->where('created_at', '>=', now()->subDays(7))->sum('totaal');
                $vorigeWeekOmzet = (int) $coachOrders->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->sum('totaal');
                $weekPct = $vorigeWeekOmzet > 0 ? (int) round(($weekOmzet - $vorigeWeekOmzet) / $vorigeWeekOmzet * 100) : null;

                // Slapende klanten: accounts waarvan de laatste bestelling 30+ dagen geleden is
                $perKlant = auth()->user()->orders()->where('is_demo', false)->whereNotNull('klant_id')
                    ->selectRaw('klant_id, MAX(created_at) as laatste, SUM(totaal) as besteed')->groupBy('klant_id')->get()->keyBy('klant_id');
                $slapend = auth()->user()->klanten->map(function ($k) use ($perKlant) {
                    $stat = $perKlant[$k->id] ?? null;
                    if (! $stat || ! \Illuminate\Support\Carbon::parse($stat->laatste)->lt(now()->subDays(30))) {
                        return null;
                    }
                    return ['naam' => $k->naam, 'laatste' => \Illuminate\Support\Carbon::parse($stat->laatste)->format('d-m-Y'), 'besteed' => (int) $stat->besteed];
                })->filter()->values();

                // Beste combo: welke twee gerechten zitten het vaakst samen in een bestelling?
                $paren = [];
                foreach ($coachOrders as $coachOrder) {
                    $namen = collect($coachOrder->items)->pluck('naam')
                        ->reject(fn ($n) => in_array($n, ['Bezorgkosten', 'Fooi bezorger', 'Spaarpunten korting'], true))
                        ->unique()->values();
                    for ($i = 0; $i < $namen->count(); $i++) {
                        for ($j = $i + 1; $j < $namen->count(); $j++) {
                            $paarSleutel = collect([$namen[$i], $namen[$j]])->sort()->implode('|');
                            $paren[$paarSleutel] = ($paren[$paarSleutel] ?? 0) + 1;
                        }
                    }
                }
                arsort($paren);
                $combo = null;
                if ($paren !== [] && reset($paren) >= 3) {
                    [$comboA, $comboB] = explode('|', array_key_first($paren));
                    $pinItem = auth()->user()->menuItems()->whereIn('naam', [$comboA, $comboB])->orderBy('prijs')->first();
                    $combo = ['a' => $comboA, 'b' => $comboB, 'aantal' => reset($paren), 'pinId' => $pinItem?->id, 'pinNaam' => $pinItem?->naam];
                }

                // Rustigste moment: blokken van 2 uur op geopende dagen, afgelopen 4 weken
                $obCoach = auth()->user()->onboarding ?? [];
                $rustig = null;
                $maand28 = $coachOrders->where('created_at', '>=', now()->subDays(28));
                if ($maand28->count() >= 20) {
                    $dagKort = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
                    $dagVol = ['ma' => 'maandag', 'di' => 'dinsdag', 'wo' => 'woensdag', 'do' => 'donderdag', 'vr' => 'vrijdag', 'za' => 'zaterdag', 'zo' => 'zondag'];
                    $telling = [];
                    foreach ($maand28 as $rustOrder) {
                        $dag = $dagKort[$rustOrder->created_at->dayOfWeekIso - 1];
                        $blok = intdiv((int) $rustOrder->created_at->format('H'), 2) * 2;
                        $telling[$dag . '|' . $blok] = ($telling[$dag . '|' . $blok] ?? 0) + 1;
                    }
                    $kandidaten = [];
                    foreach ($dagKort as $dag) {
                        if (! (($obCoach['days'] ?? [])[$dag] ?? false)) continue;
                        $tijd = ($obCoach['hoursMode'] ?? 'same') === 'perday' && isset($obCoach['dayTimes'][$dag])
                            ? $obCoach['dayTimes'][$dag]
                            : ['open' => $obCoach['open'] ?? '16:00', 'close' => $obCoach['close'] ?? '21:30'];
                        $van = intdiv((int) substr($tijd['open'], 0, 2), 2) * 2;
                        $tot = (int) substr($tijd['close'], 0, 2);
                        for ($blok = $van; $blok < $tot; $blok += 2) {
                            $kandidaten[$dag . '|' . $blok] = $telling[$dag . '|' . $blok] ?? 0;
                        }
                    }
                    if ($kandidaten !== []) {
                        asort($kandidaten);
                        [$rustDag, $rustBlok] = explode('|', array_key_first($kandidaten));
                        $rustig = ['dag' => $dagVol[$rustDag], 'van' => sprintf('%02d:00', (int) $rustBlok), 'tot' => sprintf('%02d:00', (int) $rustBlok + 2), 'aantal' => reset($kandidaten)];
                    }
                }

                $coachActief = $coachOrders->count() >= 10;
                $upsellPinActief = $obCoach['upsellPin'] ?? null;
            @endphp

            <!-- Waarschuwing als status en openingstijden niet kloppen -->
            <div id="statusWaarschuwing" class="melding hidden" style="display:none">
                <p id="waarschuwingTekst"></p>
                <button id="waarschuwingActie" type="button" class="btn dark klein shrink-0 cursor-pointer"></button>
            </div>

            @php
                $tijdOb = auth()->user()->onboarding ?? [];
                $tijdDagen = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
                $tijdVandaag = $tijdDagen[now()->dayOfWeekIso - 1];
                $tijdVoor = function (string $dag) use ($tijdOb) {
                    if (empty($tijdOb['days'][$dag])) {
                        return null;
                    }
                    if (($tijdOb['hoursMode'] ?? 'same') === 'perday') {
                        $t = $tijdOb['dayTimes'][$dag] ?? null;

                        return $t ? [$t['open'], $t['close']] : null;
                    }

                    return [$tijdOb['open'] ?? '16:00', $tijdOb['close'] ?? '21:30'];
                };
            @endphp
                @php
                    $profielOb = auth()->user()->onboarding ?? [];
                    $profielDomein = ($profielOb['domainMode'] ?? 'sub') === 'own' && ! empty($profielOb['ownDomain'])
                        ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $profielOb['ownDomain']))
                        : auth()->user()->slug . '.' . config('app.centraal_domein');
                @endphp

            {{-- Twee vaste kolommen. Links wat elke dag verandert: bestellingen, drukte
                 en cijfers. Rechts wat je zelden aanraakt: profiel, tijden, checklist.
                 Elke groep krijgt een eigen kop met lucht erboven, zodat het geen
                 aaneengesloten stapel kaarten is. --}}
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_22rem] gap-[var(--zone)] items-start">

                <!-- LINKS: vandaag en cijfers -->
                <div class="flex flex-col gap-[var(--gap)] min-w-0">

                    <div class="sectie-kop">
                        <h2>Vandaag</h2>
                        <p>Wat er nu speelt in je zaak.</p>
                    </div>

                    <!-- Openstaande bestellingen -->
                    <div class="vlak app rise" style="--d:.04s">
                        <div class="head">
                            <p class="kop">Openstaande bestellingen</p>
                            <button type="button" data-nav="bestellingen" class="btn line klein shrink-0 cursor-pointer">Alle bestellingen</button>
                        </div>
                        <div id="openOrders" class="flex flex-col gap-2"></div>
                        <div id="openLeeg" class="empty-box hidden">Geen openstaande bestellingen, alles is de deur uit.</div>
                    </div>

                    <!-- Schatting drukte: over de volle breedte, zodat elk half uur ruimte heeft -->
                    <div class="vlak app rise" style="--d:.05s">
                        <div class="head">
                            <div>
                                <p class="kop">Schatting drukte</p>
                                <p id="drukteSub" class="small mt-0.5">…</p>
                            </div>
                            <span id="drukteBadge" class="tag shrink-0">Vandaag</span>
                        </div>
                        <div id="drukteChart" class="flex items-end gap-1 h-24"></div>
                    </div>

                    <div class="sectie-kop">
                        <h2>Cijfers en inzichten</h2>
                        <p>Hoe het loopt, en wat je eraan kunt doen.</p>
                    </div>

                    <!-- Kerncijfers -->
                    <div class="vlak app rise" style="--d:.06s">
                        <div class="stats">
                            <div class="stat">
                                <span id="statOmzet" class="n" data-cents="{{ $vandaagBestellingen->sum('totaal') }}">€ {{ number_format($vandaagBestellingen->sum('totaal') / 100, 2, ',', '.') }}</span>
                                <span class="c">Omzet vandaag, {{ $vandaagBestellingen->count() ? 'uit ' . $vandaagBestellingen->count() . ' bestelling' . ($vandaagBestellingen->count() === 1 ? '' : 'en') : 'de teller start bij je eerste bestelling' }}</span>
                            </div>
                            <div class="stat">
                                <span id="statAantal" class="n" data-teller="{{ $vandaagBestellingen->count() }}">0</span>
                                <span class="c">{{ $vandaagBestellingen->count() ? 'Bestellingen vandaag binnengekomen' : 'Nog geen bestellingen vandaag' }}</span>
                            </div>
                            <div class="stat">
                                <span class="n">&euro; {{ number_format(($gemBestelwaarde ?? 0) / 100, 2, ',', '.') }}</span>
                                <span class="c">Gemiddelde bestelling over de laatste 30 dagen</span>
                            </div>
                            <div class="stat">
                                <span class="n">{{ $terugkerend }}</span>
                                <span class="c">Terugkerende klanten. {{ $piekUur !== null ? 'Drukst rond ' . sprintf('%02d:00', $piekUur) : 'Bestelden vaker dan een keer' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Omzet over de volle breedte, met een vaste hoogte: geen lege doos als er nog geen data is -->
                    <div class="vlak app rise" style="--d:.07s">
                        <p class="kop">Omzet afgelopen 14 dagen</p>
                        <div id="omzetWeek" class="flex items-end gap-2 h-28 mt-5"></div>
                    </div>

                    <!-- Toppers en doorlooptijd naast elkaar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-[var(--gap)]">
                        <div class="vlak app rise" style="--d:.08s">
                            <p class="kop mb-4">Toppers deze week</p>
                            <div id="topGerechten" class="flex flex-col gap-2.5"></div>
                        </div>
                        <div class="vlak app rise flex flex-col justify-center" style="--d:.09s">
                            <div class="stat">
                                <span id="doorloopTijd" class="n">&ndash;</span>
                                <span id="doorloopSub" class="c">Gemiddelde doorlooptijd</span>
                            </div>
                        </div>
                    </div>

                    <!-- De coach: inzichten die geld opleveren -->
                    <div class="vlak app rise" style="--d:.1s">
                        <div class="head">
                            <p class="kop">Coach</p>
                            @if($coachActief && $weekPct !== null)
                                <span class="tag shrink-0">Vorige week &euro; {{ number_format($weekOmzet / 100, 0, ',', '.') }}, {{ $weekPct >= 0 ? $weekPct . '% meer' : abs($weekPct) . '% minder' }}</span>
                            @endif
                        </div>
                        @if(! $coachActief)
                            <p class="lead">De coach heeft een paar weken aan bestellingen nodig. Zodra er genoeg data is, zie je hier acties die direct omzet opleveren.</p>
                        @else
                            <div>
                                @if($slapend->isNotEmpty())
                                    <div class="coach-regel">
                                        <p>{{ $slapend->count() }} klant{{ $slapend->count() === 1 ? ' heeft' : 'en hebben' }} al 30 dagen niet besteld.
                                            <button type="button" id="coachSlapendKnop" class="underline underline-offset-2 cursor-pointer" style="color:var(--ink2)">Bekijk wie</button>
                                        </p>
                                        <button type="button" disabled title="Kan zodra e-mail actief is" class="btn line klein shrink-0 uit">Stuur win-back actie</button>
                                    </div>
                                @endif
                                @if($combo && $combo['pinId'])
                                    <div class="coach-regel">
                                        <p>{{ $combo['a'] }} en {{ $combo['b'] }} worden vaak samen besteld ({{ $combo['aantal'] }} keer).</p>
                                        @if((string) $upsellPinActief === (string) $combo['pinId'])
                                            <button type="button" data-coach-pin-uit class="btn line klein shrink-0 cursor-pointer"><i class="fa-solid fa-check text-[11px]" aria-hidden="true"></i> Upsell actief, zet uit</button>
                                        @else
                                            <button type="button" data-coach-pin="{{ $combo['pinId'] }}" class="btn dark klein shrink-0 cursor-pointer">Zet {{ $combo['pinNaam'] }} bovenaan</button>
                                        @endif
                                    </div>
                                @endif
                                @if($rustig)
                                    <div class="coach-regel">
                                        <p>{{ ucfirst($rustig['dag']) }} tussen {{ $rustig['van'] }} en {{ $rustig['tot'] }} is je rustigste moment.</p>
                                        <button type="button" disabled title="Kortingsacties komen binnenkort" class="btn line klein shrink-0 uit">Maak actie</button>
                                    </div>
                                @endif
                                @if($slapend->isEmpty() && ! ($combo && $combo['pinId']) && ! $rustig)
                                    <p class="lead">Geen bijzonderheden deze week. De coach blijft meekijken.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RECHTS: alles over je zaak -->
                <aside class="flex flex-col gap-[var(--gap)] min-w-0">

                    <div class="sectie-kop">
                        <h2>Je zaak</h2>
                        <p>Wat je instelt en zelden aanraakt.</p>
                    </div>

                    <!-- Profielkaart: wie je bent en waar klanten je vinden -->
                    <div class="vlak app rise" style="--d:.05s">
                        <div class="flex items-center gap-3.5">
                            <div class="w-14 h-14 shrink-0 overflow-hidden grid place-items-center" style="background:var(--soft); border-radius:var(--r-blok)">
                                @if($profielOb['logo'] ?? null)
                                    <img src="{{ $profielOb['logo'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-store text-lg" style="color:var(--ink3)" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="kop truncate">{{ $profielOb['name'] ?? 'Jouw zaak' }}</p>
                                <p class="mini truncate">{{ auth()->user()->name }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <span class="tag {{ $setupKlaar ? 'ok' : '' }}">{{ $setupKlaar ? 'Zaak compleet' : 'Zaak nog niet af' }}</span>
                        </div>

                        <hr class="streep">

                        <div class="flex flex-col gap-2.5 text-[13px]">
                            <p class="flex items-center gap-2.5 min-w-0"><i class="fa-solid fa-globe w-4 text-center text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i><span class="truncate">{{ $profielDomein }}</span></p>
                            <p class="flex items-center gap-2.5 min-w-0"><i class="fa-solid fa-envelope w-4 text-center text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i><span class="truncate">{{ auth()->user()->email }}</span></p>
                            @if(($profielOb['phone'] ?? '') !== '')
                                <p class="flex items-center gap-2.5 min-w-0"><i class="fa-solid fa-phone w-4 text-center text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i><span class="truncate">{{ $profielOb['phone'] }}</span></p>
                            @endif
                            @if(($profielOb['city'] ?? '') !== '')
                                <p class="flex items-center gap-2.5 min-w-0"><i class="fa-solid fa-location-dot w-4 text-center text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i><span class="truncate">{{ $profielOb['city'] }}</span></p>
                            @endif
                        </div>

                        <div class="mt-5">
                            @if(auth()->user()->abonnement_actief)
                            @php
                                $aboSinds = auth()->user()->abonnement_sinds ?? now();
                                $aboVolgende = $aboSinds->copy();
                                while ($aboVolgende->isPast()) {
                                    $aboVolgende = $aboVolgende->addMonthNoOverflow();
                                }
                            @endphp
                            <!-- Abonnement actief, met de volgende facturatiedatum -->
                            <div class="kaart">
                                <p class="status live text-[13px]"><i></i>Abonnement actief</p>
                                <p class="mini">&euro; 24,95 per maand. Volgende facturatie op {{ $aboVolgende->locale('nl')->isoFormat('D MMMM YYYY') }}.</p>
                            </div>
                            @elseif(auth()->user()->inProefperiode())
                            @php $proefDagen = max(1, (int) ceil(now()->diffInDays(auth()->user()->proef_tot, false))); @endphp
                            <!-- Proefperiode: gratis proberen, met een korte route naar het abonnement -->
                            <div class="kaart">
                                <p class="text-[13px] font-medium">Proefperiode: nog {{ $proefDagen }} {{ $proefDagen === 1 ? 'dag' : 'dagen' }}</p>
                                <p class="mini">Daarna &euro; 24,95 per maand, opzegbaar per maand.</p>
                                <form method="POST" action="{{ route('abonnement.starten') }}">
                                    @csrf
                                    <button type="submit" class="btn dark klein breed cursor-pointer">Nu abonneren</button>
                                </form>
                            </div>
                            @endif
                            <button type="button" data-nav="instellingen" class="btn line klein breed mt-3 cursor-pointer">Profiel aanpassen</button>
                        </div>
                    </div>

                    <!-- Openingstijden in één oogopslag, met vandaag uitgelicht -->
                    <div class="vlak app rise" style="--d:.06s">
                        <div class="head">
                            <p class="kop">Openingstijden</p>
                            <a href="{{ route('onboarding') }}?stap=hours" class="btn line klein shrink-0">Wijzigen</a>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            @foreach($tijdDagen as $tijdDag)
                                @php $tijd = $tijdVoor($tijdDag); @endphp
                                <div class="dag {{ $tijdDag === $tijdVandaag ? 'vandaag' : ($tijd ? 'open' : 'dicht') }}">
                                    <b>{{ $tijdDag }}</b>
                                    @if($tijd)
                                        <span>{{ $tijd[0] }}</span>
                                        <span>{{ $tijd[1] }}</span>
                                    @else
                                        <span>dicht</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="small mt-4">
                            @if(empty($tijdOb['days']))
                                Je hebt je openingstijden nog niet ingesteld.
                            @elseif($tijdVoor($tijdVandaag))
                                Vandaag geopend van {{ $tijdVoor($tijdVandaag)[0] }} tot {{ $tijdVoor($tijdVandaag)[1] }}.
                            @else
                                Vandaag ben je gesloten.
                            @endif
                        </p>
                    </div>

                    <!-- Checklist met voortgangsring -->
                    <div class="vlak app rise" style="--d:.07s">
                        <div class="flex items-center gap-4 mb-5">
                            <svg viewBox="0 0 64 64" class="w-14 h-14 shrink-0">
                                <circle cx="32" cy="32" r="26" stroke-width="6" fill="none" class="ring-track"/>
                                <circle id="ringBar" cx="32" cy="32" r="26" stroke-width="6" fill="none" class="ring-bar"
                                    stroke-linecap="round" stroke-dasharray="163.4" stroke-dashoffset="163.4"
                                    transform="rotate(-90 32 32)"/>
                                <text id="ringPct" x="32" y="37" text-anchor="middle" style="font-size:13px; font-weight:400; fill:var(--ink)">0%</text>
                            </svg>
                            <div class="min-w-0">
                                <p class="kop">Maak je zaak compleet</p>
                                <p class="mini mt-0.5">Tik een punt aan om het meteen te regelen.</p>
                            </div>
                        </div>
                        <div id="checklist" class="flex flex-col gap-1"></div>
                    </div>

                    <!-- Snel regelen -->
                    <div class="vlak app rise" style="--d:.08s">
                        <p class="kop mb-4">Snel regelen</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-2">
                            <button type="button" data-nav="menukaart" class="snel qa-btn cursor-pointer"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i> Menu aanpassen</button>
                            <a href="{{ route('onboarding') }}?stap=hours" class="snel qa-btn"><i class="fa-solid fa-clock" aria-hidden="true"></i> Tijden wijzigen</a>
                            <button type="button" data-inst="stijl" class="snel qa-btn cursor-pointer"><i class="fa-solid fa-palette" aria-hidden="true"></i> Pagina stylen</button>
                            <button type="button" data-nav="webshop" class="snel qa-btn cursor-pointer"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Voorraad inkopen</button>
                        </div>
                    </div>

                    <!-- Deel je bestelpagina -->
                    <div class="vlak app rise" style="--d:.09s">
                        <p class="kop mb-4">Deel je bestelpagina</p>
                        <div class="flex items-center gap-4">
                            {{-- Zaak nog niet af: de QR en link wazig, delen heeft nog geen zin --}}
                            <div id="qrBox" class="w-[96px] h-[96px] shrink-0 grid place-items-center overflow-hidden p-1.5" style="background:#fff; border-radius:var(--r-blok); box-shadow:inset 0 0 0 1px var(--line);@unless($setupKlaar) filter:blur(5px); pointer-events:none @endunless"></div>
                            <div class="min-w-0 flex-1">
                                <p id="shareLink" class="mini break-all mb-3 select-none" @unless($setupKlaar) style="filter:blur(4px)" @endunless>…</p>
                                <button id="copyLink" type="button" class="btn line klein cursor-pointer">Kopieer link</button>
                            </div>
                        </div>
                        <p class="mini mt-3">Print de QR voor op je toonbank of flyers.</p>
                    </div>

                    <!-- Voorraad inslaan bij de groothandel: het omgekeerde vlak sluit de kolom af -->
                    <div class="vlak app tweede rise" style="--d:.1s">
                        <p class="kop">Voorraad nodig?</p>
                        <p class="text-[13.5px] mt-1.5 mb-5" style="color:color-mix(in srgb, var(--accent-2-ink) 75%, transparent)">Frisdrank, ingrediënten, verpakking en dozen met je eigen opdruk. Tegen groothandelsprijzen, geleverd tot aan de deur.</p>
                        <button type="button" data-nav="webshop" class="btn wh cursor-pointer">Naar de inkoop <i class="ar fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </aside>
            </div>

            <!-- Slapende klanten: wie zijn het? -->
            @if($coachActief && $slapend->isNotEmpty())
            <div id="coachSlapendModal" class="hidden venster-laag">
                <div class="venster">
                    <div class="head !mb-0">
                        <p class="kop">Slapende klanten</p>
                        <button type="button" id="coachSlapendSluit" class="w-9 h-9 rounded-full grid place-items-center cursor-pointer shrink-0" style="background:var(--soft)" aria-label="Sluiten"><i class="fa-solid fa-xmark text-[13px]" aria-hidden="true"></i></button>
                    </div>
                    <p class="lead">Laatste bestelling 30 dagen of langer geleden. Zodra e-mail actief is kun je ze een win-back actie sturen.</p>
                    <div class="max-h-80 venster-scroll overscroll-contain">
                        @foreach($slapend as $slaper)
                            <div class="regel mb-1.5">
                                <span class="op truncate text-[13.5px]">{{ $slaper['naam'] }}</span>
                                <span class="mini shrink-0">laatst {{ $slaper['laatste'] }}</span>
                                <span class="text-[13.5px] shrink-0">&euro; {{ number_format($slaper['besteed'] / 100, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            </div>
        </section>

        <!-- PANEEL: Bestellingen (touch-first) -->
        <section data-panel="bestellingen" class="panel">
            <div class="flex flex-col gap-[var(--gap)]">
                <div class="paneel-kop">
                    <div>
                        <h2>Bestellingen</h2>
                        <p>Alles wat vandaag binnenkwam en nog open staat, van betaald tot bezorgd.</p>
                    </div>
                    <button id="demoOrderBtn" type="button" class="btn line klein cursor-pointer">Voorbeeld toevoegen</button>
                </div>
                <div id="orderTabs" class="tabs"></div>
                <div id="orderLijst" class="grid grid-cols-1 2xl:grid-cols-2 gap-[var(--gap)] items-start"></div>
                <div id="ordersLeeg" class="vlak app hidden">
                    <p class="kop">Hier rollen je bestellingen binnen</p>
                    <p id="ordersLeegTekst" class="lead mt-2">…</p>
                </div>
            </div>
        </section>

        <!-- PANEEL: Menukaart -->
        <section data-panel="menukaart" class="panel">
            <div class="flex flex-col gap-[var(--gap)]">
                <div class="paneel-kop">
                    <div>
                        <h2>Menukaart</h2>
                        <p id="menuSamenvatting">Je hebt nog geen gerechten toegevoegd.</p>
                    </div>
                    <button type="button" id="menuNieuw" class="btn dark cursor-pointer"><i class="fa-solid fa-plus text-[12px]" aria-hidden="true"></i> Gerecht toevoegen</button>
                </div>
                <div id="menuCats" class="tabs"></div>
                <div id="menuLijst" class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-[var(--gap)] items-start"></div>
                <div id="menuLeeg" class="hidden">
                    <div class="vlak app flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="flex-1">
                            <p class="kop">Nog niks op de kaart</p>
                            <p class="lead mt-2">Voeg je eerste gerecht toe, inclusief opties voor de klant en allergenen.</p>
                        </div>
                        <button type="button" class="btn dark shrink-0 cursor-pointer" data-menu-nieuw>Eerste gerecht toevoegen</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- PANEEL: Team -->
        <section data-panel="team" class="panel">
            <div class="flex flex-col gap-[var(--gap)]">
                <div class="paneel-kop">
                    <div>
                        <h2>Team</h2>
                        <p>Bezorgers en keuken werken op hun eigen scherm; hier beheer je wie erbij hoort.</p>
                    </div>
                    <button type="button" id="teamNieuw" class="btn dark cursor-pointer">Teamlid uitnodigen</button>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_22rem] gap-[var(--zone)] items-start">
                    <div class="vlak app">
                        <div class="head"><p class="kop">Bezorgers en keuken</p></div>
                        <div id="teamLijst" class="flex flex-col gap-2"></div>
                    </div>

                    <aside class="flex flex-col gap-[var(--gap)] min-w-0">
                        <div class="vlak app">
                            <p class="kop">Het teamscherm</p>
                            <p class="mini mt-2">Je team werkt op een eigen scherm op de telefoon en komt niet in dit dashboard. Bezorgers pakken daar een rit op en vinken onderweg zelf af dat ze bezorgd hebben.</p>
                            <div class="regel mt-4">
                                <i class="fa-solid fa-link text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i>
                                <span id="teamLink" class="op text-[13px] break-all">{{ preg_replace('#^https?://#', '', route('bezorger.login')) }}</span>
                            </div>
                            <button type="button" id="teamLinkKopie" class="btn line klein breed mt-3 cursor-pointer">Link kopiëren</button>
                        </div>

                        <div class="vlak app">
                            <p class="kop mb-4">Wat mag wie?</p>
                            <div class="flex flex-col gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="rol-icoon"><i class="fa-solid fa-moped" aria-hidden="true"></i></span>
                                    <span class="min-w-0"><span class="block text-[13.5px] font-medium">Bezorger</span><span class="block mini">Pakt ritten op, navigeert en vinkt af dat de bestelling bezorgd is</span></span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="rol-icoon"><i class="fa-solid fa-fire-burner" aria-hidden="true"></i></span>
                                    <span class="min-w-0"><span class="block text-[13.5px] font-medium">Keuken</span><span class="block mini">Zet bestellingen door van bereiden naar de oven</span></span>
                                </div>
                            </div>
                            <p class="small mt-4">Bestellingen accepteren en de tijdsschatting kiezen blijft bij jou.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- PANEEL: Inkoop -->
        <section data-panel="webshop" class="panel">
            @php
                $doosOb = auth()->user()->onboarding ?? [];
                $doosQrPatroon = ['111010111', '101001101', '111010111', '000101100', '101110011', '010011010', '111011010', '101001110', '111010011'];
                // Bestelknoppen per categorie: de url invullen zodra de groothandel de links aanlevert
                $webshopCats = [
                    ['emoji' => 'fa-bottle-water', 'naam' => 'Frisdrank', 'knop' => 'Bestel uw frisdrank', 'url' => null,
                        'tekst' => 'Blikjes en flessen voor bij de bestelling, tegen groothandelsprijzen.',
                        'items' => ['Cola', 'Sinas', 'Ice tea', 'Bronwater', 'Sappen']],
                    ['emoji' => 'fa-cheese', 'naam' => 'Food', 'knop' => 'Bestel uw food', 'url' => null,
                        'tekst' => 'De basis van je keuken, vers en voordelig ingekocht.',
                        'items' => ['Bloem', 'Tomatensaus', 'Mozzarella', 'Salami', 'Olijfolie']],
                    ['emoji' => 'fa-box-open', 'naam' => 'Verpakking', 'knop' => 'Bestel uw verpakking', 'url' => null,
                        'tekst' => 'Alles om netjes mee te geven, van sauscupjes tot pastabakken.',
                        'items' => ['Pastabakken', 'Sauscupjes', 'Aluminium bakken', 'Bestek', 'Servetten']],
                ];
                $webshopMarketingUrl = null; // link van de groothandel voor dozen en tasjes met opdruk
            @endphp
            <div class="flex flex-col gap-[var(--gap)]">
                <div class="paneel-kop">
                    <div>
                        <h2>Inkoop</h2>
                        <p>Sla je voorraad in tegen groothandelsprijzen: frisdrank, ingrediënten, verpakking en dozen met jouw eigen opdruk. Rechtstreeks bij onze vaste groothandel, geleverd tot aan de deur.</p>
                    </div>
                </div>

                {{-- Blikvanger: marketing met jouw opdruk, inclusief de spaar-QR doos. De doos zelf
                     is een tekening van een gedrukt product en houdt daarom zijn eigen kleuren. --}}
                <div class="vlak app flex flex-col sm:flex-row sm:items-center gap-8">
                    <div class="relative w-44 h-44 shrink-0 grid place-items-center" style="border-radius:var(--r-blok); background:#DDBE92; background-image:repeating-linear-gradient(45deg, rgba(0,0,0,.025) 0 7px, transparent 7px 14px); box-shadow:inset 0 0 0 3px #C9A369">
                        <div class="absolute inset-3 rounded-xl pointer-events-none" style="border:2px dashed rgba(93,58,33,.22)"></div>
                        <div class="text-center px-4">
                            <div class="w-14 h-14 mx-auto rounded-2xl overflow-hidden grid place-items-center" style="background:rgba(255,255,255,.55)">
                                @if($doosOb['logo'] ?? null)
                                    <img src="{{ $doosOb['logo'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl" style="color:#5D3A21"><i class="fa-solid fa-utensils" aria-hidden="true"></i></span>
                                @endif
                            </div>
                            <p class="mt-2 text-[12px] font-medium leading-tight" style="color:#5D3A21">{{ $doosOb['name'] ?? 'Jouw zaak' }}</p>
                        </div>
                        <div class="absolute right-3 bottom-3 rounded-md p-1.5" style="background:#fff">
                            <div class="grid grid-cols-9 gap-[1px]">
                                @foreach($doosQrPatroon as $qrRij)
                                    @foreach(str_split($qrRij) as $qrCel)
                                        <span class="w-[3px] h-[3px] {{ $qrCel === '1' ? '' : 'opacity-0' }}" style="background:#241712"></span>
                                    @endforeach
                                @endforeach
                            </div>
                            <p class="text-[5px] font-semibold text-center mt-0.5 tracking-wide" style="color:#241712">SCAN &amp; SPAAR</p>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="kop">Marketing met jouw opdruk</p>
                        <p class="lead mt-2">Dozen, bakjes en draagtasjes met jouw logo in full-color. De spaar-QR op de doos geeft klanten korting bij hun volgende bestelling en brengt ze rechtstreeks terug naar jouw bestelpagina.</p>
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach(['Dozen en bakjes', 'Draagtasjes', 'Stickers', 'Servetten met logo'] as $marketingItem)
                                <span class="tag">{{ $marketingItem }}</span>
                            @endforeach
                        </div>
                        <div class="mt-5">
                            <a href="{{ $webshopMarketingUrl ?? '#' }}" @if($webshopMarketingUrl) target="_blank" rel="noopener" @endif class="btn dark">Bestel uw marketing</a>
                        </div>
                    </div>
                </div>

                {{-- De overige categorieën: knoppen gaan straks direct door naar de groothandel --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-[var(--gap)]">
                    @foreach($webshopCats as $webshopCat)
                        <div class="vlak app flex flex-col">
                            <p class="kop">{{ $webshopCat['naam'] }}</p>
                            <p class="mini mt-1.5">{{ $webshopCat['tekst'] }}</p>
                            <div class="flex flex-wrap gap-2 mt-4 mb-6">
                                @foreach($webshopCat['items'] as $webshopItem)
                                    <span class="tag">{{ $webshopItem }}</span>
                                @endforeach
                            </div>
                            <a href="{{ $webshopCat['url'] ?? '#' }}" @if($webshopCat['url']) target="_blank" rel="noopener" @endif class="btn line breed mt-auto">{{ $webshopCat['knop'] }}</a>
                        </div>
                    @endforeach
                </div>

                <p class="small">Bestellen en afrekenen doe je rechtstreeks bij onze samenwerkende groothandel. Zodra de koppeling live is, brengen de knoppen je daar meteen naartoe.</p>
            </div>
        </section>

        <!-- PANEEL: Instellingen -->
        @php
            $ob = auth()->user()->onboarding ?? [];
            $obSlug = auth()->user()->slug ?: 'jouwzaak';
            $eigenDomein = ($ob['domainMode'] ?? 'sub') === 'own' && ! empty($ob['ownDomain']);
            $stripeKlaar = \App\Support\StripeConnect::kanOntvangen(auth()->user());
            $stripeGekoppeld = auth()->user()->stripe_account_id !== null;
        @endphp
        <section data-panel="instellingen" class="panel">
            <div class="flex flex-col gap-[var(--gap)]">
                <div class="paneel-kop">
                    <div>
                        <h2>Instellingen</h2>
                        <p>Je gegevens, tijden, betalingen en bezorging. Wijzigen gaat per blok.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-[var(--gap)] items-start">
                    <div class="flex flex-col gap-[var(--gap)] min-w-0">
                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Jouw zaak</p>
                                <button type="button" data-inst="zaak" class="btn line klein shrink-0 cursor-pointer">Wijzigen</button>
                            </div>
                            <div class="kv"><span>Naam zaak</span><span>{{ $ob['name'] ?? '-' }}</span></div>
                            <div class="kv"><span>Contactpersoon</span><span>{{ $ob['person'] ?? '-' }}</span></div>
                            <div class="kv"><span>Telefoon</span><span>{{ ($ob['phone'] ?? '') !== '' ? $ob['phone'] : 'Nog niet ingevuld' }}</span></div>
                            <div class="kv"><span>E-mail</span><span>{{ auth()->user()->email }}</span></div>
                        </div>

                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Openingstijden</p>
                                <button type="button" data-inst="tijden" class="btn line klein shrink-0 cursor-pointer">Wijzigen</button>
                            </div>
                            <p id="tijdenSamenvatting" class="small mb-3">Nog niet ingesteld.</p>
                            <div id="tijdenLijst"></div>
                        </div>

                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Spaarpunten</p>
                                <button type="button" data-inst="punten" class="btn line klein shrink-0 cursor-pointer">Wijzigen</button>
                            </div>
                            <p id="puntenWaarde" class="status {{ ($ob['spaarpunten'] ?? false) ? 'live' : 'uit' }}"><i></i>{{ ($ob['spaarpunten'] ?? false) ? 'Aan, klanten sparen automatisch mee' : 'Uit' }}</p>
                            @if($ob['spaarpunten'] ?? false)
                                @php
                                    $spaarders = auth()->user()->klanten()->where('punten', '>', 0)->orderByDesc('punten')->take(5)->get();
                                    $puntenUitstaand = (int) auth()->user()->klanten()->sum('punten');
                                @endphp
                                <hr class="streep">
                                <p class="small mb-2">Uitstaand: {{ $puntenUitstaand }} punten, samen goed voor &euro; {{ number_format(intdiv($puntenUitstaand, 100) * 5, 0, ',', '.') }} korting</p>
                                @forelse($spaarders as $spaarder)
                                    <div class="kv"><span style="color:var(--ink)">{{ $spaarder->naam }}</span><span style="color:var(--ink2)">{{ $spaarder->punten }} punten{{ intdiv($spaarder->punten, 100) > 0 ? ', kan € ' . (intdiv($spaarder->punten, 100) * 5) . ' claimen' : '' }}</span></div>
                                @empty
                                    <p class="small">Nog geen klanten met punten.</p>
                                @endforelse
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-[var(--gap)] min-w-0">
                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Bedrijfsgegevens</p>
                                <button type="button" data-inst="bedrijf" class="btn line klein shrink-0 cursor-pointer">Wijzigen</button>
                            </div>
                            <div class="kv"><span>KVK-nummer</span><span>{{ ($ob['kvk'] ?? '') !== '' ? $ob['kvk'] : 'Nog niet ingevuld' }}</span></div>
                            <div class="kv"><span>Adres</span><span>{{ ($ob['street'] ?? '') !== '' ? $ob['street'] : 'Nog niet ingevuld' }}</span></div>
                            <div class="kv"><span>Plaats</span><span>{{ trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? '')) !== '' ? trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? '')) : 'Nog niet ingevuld' }}</span></div>
                        </div>

                        {{-- Stripe Connect: klanten betalen rechtstreeks op het account van de zaak --}}
                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Betalingen ontvangen</p>
                                @if($stripeKlaar)
                                    <span class="tag ok shrink-0">Klaar</span>
                                @elseif($stripeGekoppeld)
                                    <span class="tag shrink-0">Nog niet af</span>
                                @endif
                            </div>
                            @if($stripeKlaar)
                                <p class="text-[14px] font-medium">Je ontvangt betalingen via Stripe</p>
                                <p class="mini mt-1.5">iDEAL, creditcard, Apple Pay en Google Pay. Het geld van je bestellingen gaat rechtstreeks naar jouw rekening, wij houden niets in.</p>
                                <a href="{{ route('stripe.connect.dashboard') }}" class="btn line klein mt-4">Bekijk je uitbetalingen</a>
                            @elseif($stripeGekoppeld)
                                <p class="text-[14px] font-medium">Stripe heeft nog niet alles van je</p>
                                <p class="mini mt-1.5">Maak je gegevens af (bankrekening, KvK en identiteit), dan kun je betalingen ontvangen. Zolang dit niet rond is, kun je niet online.</p>
                                <a href="{{ route('stripe.connect.start') }}" class="btn dark klein mt-4">Gegevens afmaken</a>
                            @else
                                <p class="text-[14px] font-medium">Koppel je bankrekening</p>
                                <p class="mini mt-1.5">Klanten betalen met iDEAL, creditcard, Apple Pay of Google Pay. Je regelt het bij Stripe, in een paar minuten. Het geld komt rechtstreeks bij jou binnen, zonder commissie van ons.</p>
                                <a href="{{ route('stripe.connect.start') }}" class="btn dark klein mt-4">Koppel met Stripe</a>
                            @endif
                        </div>

                        <div class="vlak app">
                            <div class="head">
                                <p class="kop">Jouw domein</p>
                                <button type="button" data-inst="domein" class="btn line klein shrink-0 cursor-pointer">Wijzigen</button>
                            </div>
                            <p id="domeinWaarde" class="text-[14px] break-all">{{ $eigenDomein ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $ob['ownDomain'])) : $obSlug . '.' . config('app.centraal_domein') }}</p>
                        </div>

                        {{-- De bonprinter: de printer haalt zelf zijn bonnen op bij ons, dus hier alleen het adres, de status en wat er geprint wordt --}}
                        @php $printerOpties = \App\Support\Bonprinter::opties(auth()->user()); @endphp
                        <div class="vlak app" id="printerVlak" data-aan="{{ auth()->user()->printer_aan ? 1 : 0 }}">
                            <div class="head">
                                <p class="kop">Bonprinter</p>
                                <span id="printerStatus" class="status uit shrink-0"><i></i>Nog niet gezien</span>
                            </div>
                            <p class="mini mb-4">Elke betaalde bestelling komt vanzelf uit je bonprinter. Heb je een Epson met netwerk, zet dan dit adres in de printer bij <b>Server Direct Print</b> (interval 5 seconden). Elke andere netwerkprinter werkt via ons hulpprogramma op een pc in de zaak, met ditzelfde adres.</p>
                            <div class="regel" style="align-items:flex-start">
                                <i class="fa-solid fa-print mt-1 text-[12px]" style="color:var(--ink3)" aria-hidden="true"></i>
                                <span class="op"><span id="printerUrl" class="text-[13px] break-all">{{ \App\Support\Bonprinter::url(auth()->user()) }}</span></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                <button type="button" id="printerKopieer" class="btn line klein cursor-pointer">Adres kopiëren</button>
                                <button type="button" id="printerTest" class="btn line klein cursor-pointer">Testbon printen</button>
                            </div>
                            <p class="mini mt-3">Geen Epson? Start op een pc in de zaak het hulpprogramma met dit adres en het IP van de printer: <code class="text-[12px]">php artisan bon:agent --url=&lt;adres&gt; --printer=&lt;ip&gt;</code></p>
                            <div class="mt-5 flex flex-col gap-2">
                                <label class="check-item"><input type="checkbox" id="printerAan" {{ auth()->user()->printer_aan ? 'checked' : '' }}> <span>Bonnen printen bij elke betaalde bestelling</span></label>
                                <label class="check-item"><input type="checkbox" id="printerKeuken" {{ $printerOpties['keukenbon'] ? 'checked' : '' }}> <span>Keukenbon (zonder prijzen)</span></label>
                                <label class="check-item"><input type="checkbox" id="printerKlant" {{ $printerOpties['klantbon'] ? 'checked' : '' }}> <span>Klantbon met prijzen en QR naar de bestelstatus</span></label>
                            </div>
                            <div class="grid grid-cols-2 gap-[var(--gap)] mt-4">
                                <div>
                                    <p class="label mb-2">Keukenbonnen</p>
                                    <div class="flex gap-1.5" id="printerKopieen">
                                        @foreach([1, 2, 3] as $n)<button type="button" class="keuze-chip justify-center !py-2 flex-1 cursor-pointer {{ (int) $printerOpties['kopieen'] === $n ? 'aan' : '' }}" data-kopieen="{{ $n }}">{{ $n }}</button>@endforeach
                                    </div>
                                </div>
                                <div>
                                    <p class="label mb-2">Papier</p>
                                    <div class="flex gap-1.5" id="printerBreedte">
                                        @foreach([48 => '80 mm', 32 => '58 mm'] as $b => $l)<button type="button" class="keuze-chip justify-center !py-2 flex-1 cursor-pointer {{ (int) $printerOpties['breedte'] === $b ? 'aan' : '' }}" data-breedte="{{ $b }}">{{ $l }}</button>@endforeach
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="printerOpslaan" class="btn dark breed mt-5 cursor-pointer">Printer opslaan</button>
                            <p id="printerMelding" class="small mt-3"></p>
                        </div>

                        <div class="vlak app">
                            <p class="kop mb-4">Hulp en account</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <button id="introOpnieuw" type="button" class="btn line klein cursor-pointer">Bekijk de uitlegvideo</button>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn line klein cursor-pointer">Uitloggen</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sectie-kop">
                    <h2>Bezorging</h2>
                    <p>Hoe ver je bezorgt en wat het kost.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-[var(--gap)] items-start">
                    <div class="vlak app">
                        <p class="kop">Jouw bezorggebied</p>
                        <p class="mini mt-1.5 mb-4">Zo ver bezorg je op dit moment. De ringen horen bij de tarieven hiernaast.</p>
                        <div id="bezorgMap" class="aspect-square w-full max-w-lg mx-auto z-0" style="border-radius:var(--r-blok); background:var(--soft)"></div>
                        <p id="bezorgHint" class="small mt-3"></p>
                    </div>
                    <div class="vlak app">
                        <div class="head">
                            <p class="kop">Bezorgkosten</p>
                            <span id="bezorgStatus" class="status live shrink-0" style="display:none"><i></i>Opgeslagen</span>
                        </div>
                        <p class="mini mb-4">Stel per afstand een tarief in, bijvoorbeeld tot 2 km voor € 1,00.</p>
                        <div id="bezorgTiers" class="flex flex-col gap-2 mb-3"></div>
                        <button type="button" id="bezorgTierAdd" class="link-knop cursor-pointer">+ straal toevoegen</button>
                        <button type="button" id="bezorgOpslaan" class="btn dark breed mt-5 cursor-pointer">Bezorgkosten opslaan</button>
                    </div>
                </div>

                <div class="sectie-kop">
                    <h2>Jouw bestelpagina</h2>
                    <p>Stijl, adres en een live voorbeeld van wat klanten zien.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-[var(--gap)] items-start">
                    <div class="vlak app">
                        <p class="kop">Stijl van je pagina</p>
                        <p id="paginaSamenvatting" class="mini mt-1.5 mb-5">Kies je template, kleur en logo.</p>
                        <button type="button" data-inst="stijl" class="btn dark cursor-pointer">Pagina stylen</button>
                    </div>
                    <div class="vlak app">
                        <p class="kop">Jouw bestel-adres</p>
                        <p id="paginaLink" class="mini mt-1.5 mb-5 break-all"></p>
                        <div class="flex flex-wrap gap-2">
                            <a id="paginaOpen" href="#" target="_blank" rel="noopener" class="btn dark klein">Bekijk je pagina</a>
                            <button type="button" id="paginaKopieer" class="btn line klein cursor-pointer">Kopieer link</button>
                        </div>
                    </div>
                </div>

                {{-- Live voorbeeld: de pagina van de klant, altijd op wit, wat het thema hier ook is --}}
                <div class="vlak app">
                    <div class="head">
                        <p class="kop">Live voorbeeld</p>
                        <p class="small">Ververst vanzelf na het opslaan van je stijl.</p>
                    </div>
                    <div class="relative w-full aspect-video overflow-hidden" style="border-radius:var(--r-blok); background:#fff; box-shadow:inset 0 0 0 1px var(--line)">
                        <iframe id="paginaPreview" title="Voorbeeld van je bestelpagina" class="absolute inset-0 pointer-events-none select-none" style="width:200%; height:200%; transform:scale(.5); transform-origin:top left; border:0"></iframe>
                    </div>
                </div>
            </div>
        </section>
        </div>
    </main>

    <!-- Instellingen bewerken: venster per blok -->
    {{-- Het venster mag nooit hoger worden dan het scherm: de velden scrollen
         intern, zodat sluiten en opslaan altijd bereikbaar blijven. --}}
    <div id="instModal" class="hidden venster-laag">
        <div id="instModalMidden" class="venster-midden">
            <div id="instKaartWrap" class="venster vrij hoog max-w-xl">
                <button type="button" id="instModalSluit" class="venster-sluit cursor-pointer" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="shrink-0 pr-10">
                    <p id="instKicker" class="label"></p>
                    <h2 id="instTitel" class="venster-titel mt-1"></h2>
                    <p id="instSub" class="lead mt-2"></p>
                </div>
                <div id="instVelden" class="flex flex-col gap-4 flex-1 min-h-0 venster-scroll overscroll-contain"></div>
                <div class="shrink-0 flex justify-end">
                    <button type="button" id="instOpslaan" class="btn dark cursor-pointer">Opslaan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gerecht toevoegen of bewerken: stap voor stap, net als de onboarding -->
    <div id="menuModal" class="hidden venster-laag">
        <div id="mModalMidden" class="venster-midden">
            <div class="venster vrij hoog max-w-xl midden">
                <button type="button" id="menuModalSluit" class="venster-sluit cursor-pointer" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="shrink-0 px-8">
                    <p id="mKicker" class="label"></p>
                    <h2 id="mTitel" class="venster-titel mt-1"></h2>
                    <p id="mSub" class="lead mt-2 mx-auto"></p>
                </div>

                {{-- De stappen scrollen intern; de knoppenrij eronder blijft staan. --}}
                <div class="flex-1 min-h-0 venster-scroll overscroll-contain w-full">
                    <div data-mstap="naam" class="m-stap hidden">
                        <input id="mNaam" type="text" class="inp text-center" placeholder="bijv. Margherita" maxlength="100">
                        <p class="err" id="mNaamErr"></p>
                    </div>
                    <div data-mstap="prijs" class="m-stap hidden">
                        <div class="relative max-w-xs mx-auto">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-lg" style="color:var(--ink3)">€</span>
                            <input id="mPrijs" class="inp text-center" placeholder="9,50" inputmode="decimal">
                        </div>
                        <p class="err" id="mPrijsErr"></p>
                    </div>
                    <div data-mstap="categorie" class="m-stap hidden">
                        <div id="mCatChips" class="flex flex-wrap justify-center gap-2"></div>
                    </div>
                    <div data-mstap="beschrijving" class="m-stap hidden">
                        <textarea id="mBeschrijving" class="inp !h-28" rows="3" maxlength="300" placeholder="bijv. Tomatensaus, mozzarella en verse basilicum"></textarea>
                    </div>
                    <div data-mstap="foto" class="m-stap hidden">
                        <input id="mFotoInp" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <div id="mFotoLeeg">
                            <button type="button" id="mFotoKies" class="foto-drop cursor-pointer">
                                <i class="fa-solid fa-camera text-2xl" style="color:var(--ink3)" aria-hidden="true"></i>
                                <span class="text-[14px] font-medium">Foto uploaden</span>
                                <span class="mini">JPG, PNG of WebP</span>
                            </button>
                        </div>
                        <div id="mFotoVol" class="hidden">
                            <img id="mFotoPreview" src="" alt="Foto van het gerecht" class="w-40 h-40 mx-auto object-cover" style="border-radius:var(--r-blok); box-shadow:inset 0 0 0 1px var(--line)">
                            <div class="mt-4 flex items-center justify-center gap-5">
                                <button type="button" id="mFotoAnders" class="link-knop cursor-pointer">Andere foto kiezen</button>
                                <button type="button" id="mFotoWeg" class="link-knop cursor-pointer">Foto verwijderen</button>
                            </div>
                        </div>
                        <p class="err" id="mFotoErr"></p>
                    </div>
                    <div data-mstap="ingredienten" class="m-stap hidden">
                        <div id="mIngChips" class="flex flex-wrap justify-center gap-2 mb-3"></div>
                        <input id="mIngInput" class="inp text-center" placeholder="Typ een ingrediënt en druk op Enter">
                    </div>
                    <div data-mstap="allergenen" class="m-stap hidden">
                        <div id="mAllergChips" class="flex flex-wrap justify-center gap-2"></div>
                        <p id="mAllergHint" class="m-hint mt-3"></p>
                    </div>
                    <div data-mstap="opties" class="m-stap hidden text-left">
                        <div id="mOptieTemplates" class="flex flex-wrap justify-center gap-2 mb-3"></div>
                        <div id="mOptieGroepen" class="flex flex-col gap-3"></div>
                    </div>
                    <div data-mstap="overzicht" class="m-stap hidden text-left">
                        <div id="mOverzicht" class="flex flex-col gap-2"></div>
                    </div>
                </div>{{-- /scrollend stappengedeelte --}}

                <div class="shrink-0 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" id="mTerug" class="btn line cursor-pointer">Terug</button>
                    <button type="button" id="mVolgende" class="btn dark cursor-pointer">Volgende</button>
                </div>
                <p class="enter-hint small" id="mEnterHint">of druk op <b>Enter ↵</b></p>
                <p><button type="button" id="menuVerwijder" class="hidden link-knop cursor-pointer">Dit gerecht verwijderen</button></p>
            </div>
        </div>
    </div>

    <!-- Introvideo bij je eerste bezoek -->
    <div id="introModal" class="hidden venster-laag">
        <div class="venster vrij max-w-2xl midden venster-scroll overscroll-contain" style="max-height:calc(100dvh - var(--gap) * 2)">
            <p class="label">Welkom</p>
            <h2 class="venster-titel">Zo werkt jouw dashboard</h2>
            <p class="lead mx-auto">In een paar minuten leggen we je uit hoe je bestellingen ontvangt, je menukaart beheert en je pagina live zet.</p>

            {{-- Hier komt straks de echte uitlegvideo; het vlak is bewust altijd donker, zoals een videospeler --}}
            <div class="relative aspect-video overflow-hidden grid place-items-center w-full" style="border-radius:var(--r-blok); background:var(--dark); color:#fff">
                <div class="text-center">
                    <span class="w-16 h-16 rounded-full grid place-items-center mx-auto mb-3 text-xl" style="background:rgba(255,255,255,.14)"><i class="fa-solid fa-play" aria-hidden="true"></i></span>
                    <p class="text-[13px]" style="color:rgba(255,255,255,.7)">De uitlegvideo komt hier binnenkort</p>
                </div>
            </div>

            <button id="introSluiten" type="button" class="btn dark cursor-pointer">Aan de slag</button>
            <p class="small">Later terugkijken? Je vindt 'm bij Instellingen.</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>window.PP_BEZORG = @json(['lat' => auth()->user()->lat, 'lng' => auth()->user()->lng, 'tiers' => auth()->user()->bezorgkosten ?? []]);</script>
    <script>window.PP_DATA = @json(auth()->user()->onboarding ?? new stdClass);</script>
    @php
        $ppOrders = auth()->user()->orders()
            ->where(fn ($q) => $q->where('status', '!=', 'bezorgd')->orWhereDate('created_at', today()))
            ->with('bezorger:id,naam')   // wie de rit heeft opgepakt, zichtbaar op de kaart
            ->latest()->get();
    @endphp
    <script>window.PP_ORDERS = @json($ppOrders);</script>
    <script>window.PP_SLUG = @json(auth()->user()->slug);</script>
    <script>window.PP_DOMEIN = @json(config('app.centraal_domein'));</script>
    @php
        // De publieke bestel-URL: subdomein als dat gekozen is, anders het padadres
        $ppDomeinModus = (auth()->user()->onboarding ?? [])['domainMode'] ?? 'sub';
        $ppPoort = in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort();
        $ppBestelUrl = $ppDomeinModus === 'sub' && config('app.centraal_domein')
            ? request()->getScheme() . '://' . auth()->user()->slug . '.' . config('app.centraal_domein') . $ppPoort
            : url('/bestellen/' . auth()->user()->slug);
    @endphp
    <script>window.PP_BESTEL_URL = @json($ppBestelUrl);</script>
    <script>window.PP_KLEUREN = @json(\App\Http\Controllers\BestelController::KLEUREN);</script>
    @php
        // Echte cijfers voor de overzicht-widgets, over de afgelopen 7 dagen
        $weekOrders = auth()->user()->orders()->where('created_at', '>=', now()->subDays(7))->get();
        $toppers = collect($weekOrders)
            ->flatMap(fn ($o) => $o->items)
            ->reject(fn ($i) => in_array($i['naam'], ['Bezorgkosten', 'Fooi bezorger'], true))
            ->groupBy('naam')
            ->map(fn ($groep, $naam) => ['naam' => $naam, 'aantal' => $groep->sum('aantal')])
            ->sortByDesc('aantal')->values()->take(3);
        // Alleen realistische doorlooptijden meetellen (tot 4 uur), anders vertekenen oude orders het beeld
        $doorlooptijden = $weekOrders->where('status', 'bezorgd')
            ->map(fn ($o) => max(1, $o->created_at->diffInMinutes($o->updated_at)))
            ->filter(fn ($m) => $m <= 240);
        $klaar = $doorlooptijden;
        $doorloop = $doorlooptijden->count() ? (int) round($doorlooptijden->avg()) : null;
        // De omzetgrafiek kijkt twee weken terug; de toppers en doorlooptijd hierboven blijven op een week
        $veertienOrders = auth()->user()->orders()->where('created_at', '>=', now()->subDays(14)->startOfDay())->get();
        $omzet = collect(range(13, 0))->map(function ($terug) use ($veertienOrders) {
            $dag = now()->subDays($terug);
            return [
                // Dagnummer als label: met veertien kolommen zouden weekdagen twee keer voorkomen
                'label' => $dag->isoFormat('D'),
                'bedrag' => $veertienOrders->filter(fn ($o) => $o->created_at->isSameDay($dag))->sum('totaal'),
            ];
        });
        $ppStats = ['toppers' => $toppers, 'doorloop' => $doorloop, 'klaarAantal' => $klaar->count(), 'omzet' => $omzet];
    @endphp
    <script>window.PP_STATS = @json($ppStats);</script>
    {{-- fotoUrl is wat klanten zien (eigen upload of de standaardfoto); foto blijft de eigen upload voor de editor --}}
    <script>window.PP_MENU = @json(auth()->user()->menuItems()->orderBy('volgorde')->orderBy('id')->get()->map(fn ($m) => $m->setAttribute('fotoUrl', \App\Http\Controllers\BestelController::fotoVoor($m))));</script>
    {{-- De uitlegvideo wacht tot het abonnement gestart is en de zaak af is, anders staan er twee vensters tegelijk open --}}
    <script>window.PP_INTRO = @json(! auth()->user()->intro_seen && ! $abonnementNodig && $setupKlaar);</script>
    <script>window.PP_STATUS = @json(['online' => (bool) auth()->user()->is_online, 'mode' => auth()->user()->order_mode ?? 'bezorgen_afhalen']);</script>
    <script>window.PP_SETUP = @json($setupKlaar);</script>
    <script>window.PP_STRIPE_MELDING = @json(session('stripeMelding'));</script>
    <script>window.PP_STRIPE_FOUT = @json(session('stripeFout'));</script>
    <script>window.PP_STRIPE_KLAAR = @json(\App\Support\StripeConnect::kanOntvangen(auth()->user()));</script>
    <script>window.PP_STRIPE_VERPLICHT = @json(\App\Support\StripeConnect::betalingenAan());</script>
    <script>window.PP_TEAM = @json(auth()->user()->medewerkers()->orderBy('naam')->get()->map(fn ($m) => \App\Http\Controllers\TeamController::voorDashboard($m))->values());</script>
    <script>window.PP_ABO = @json((bool) auth()->user()->abonnement_actief);</script>
    <script>window.PP_ABO_FEEST = @json((bool) session('abonnementGestart'));</script>
    <script>window.PP_ABO_FOUT = @json(session('abonnementFout'));</script>

    <!-- Weet-je-het-zeker voor online of offline gaan -->
    <div id="statusModal" class="hidden venster-laag">
        <div class="venster-midden">
            <div class="venster vrij max-w-sm midden">
                <span id="statusModalIcoon" class="venster-icoon" aria-hidden="true"><i class="fa-solid fa-circle-check"></i></span>
                <p id="statusModalTitel" class="venster-titel">Online gaan?</p>
                <p id="statusModalTekst" class="lead mx-auto"></p>
                <div class="flex flex-col gap-2 w-full mt-2">
                    <button type="button" id="statusModalBevestig" class="btn dark breed cursor-pointer">Ja, ga online</button>
                    <button type="button" id="statusModalAnnuleer" class="btn line breed cursor-pointer">Annuleren</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teamlid uitnodigen -->
    <div id="teamModal" class="hidden venster-laag">
        <div id="teamModalMidden" class="venster-midden">
            <div class="venster vrij hoog max-w-lg">
                <button type="button" id="teamModalSluit" class="venster-sluit cursor-pointer" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="shrink-0 pr-10">
                    <p class="label">Nieuw teamlid</p>
                    <h2 class="venster-titel mt-1">Wie neem je erbij?</h2>
                    <p class="lead mt-2">Hij of zij krijgt een mail met een link om een wachtwoord te kiezen.</p>
                </div>

                <div class="flex flex-col gap-5 flex-1 min-h-0 venster-scroll overscroll-contain">
                    <div>
                        <p class="label mb-2">Rol</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button type="button" data-teamrol="bezorger" class="keuze aan cursor-pointer">
                                <span class="rol-icoon"><i class="fa-solid fa-moped" aria-hidden="true"></i></span>
                                <span class="min-w-0"><b>Bezorger</b><span class="block">Pakt ritten op en vinkt af</span></span>
                            </button>
                            <button type="button" data-teamrol="keuken" class="keuze cursor-pointer">
                                <span class="rol-icoon"><i class="fa-solid fa-fire-burner" aria-hidden="true"></i></span>
                                <span class="min-w-0"><b>Keuken</b><span class="block">Zet bestellingen door</span></span>
                            </button>
                        </div>
                    </div>
                    <div class="fld">
                        <label for="teamNaam">Naam</label>
                        <input id="teamNaam" type="text" placeholder="bijv. Youssef" maxlength="80">
                    </div>
                    <div class="fld">
                        <label for="teamEmail">E-mailadres</label>
                        <input id="teamEmail" type="email" placeholder="youssef@voorbeeld.nl" maxlength="255">
                    </div>
                    <div class="fld">
                        <label for="teamTelefoon">Telefoon (mag ook later)</label>
                        <input id="teamTelefoon" type="tel" placeholder="06 12 34 56 78" maxlength="30">
                    </div>
                    <p class="err" id="teamErr"></p>
                </div>

                <div class="shrink-0">
                    <button type="button" id="teamOpslaan" class="btn dark breed cursor-pointer">Uitnodiging versturen</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Weet-je-het-zeker bij het weghalen of uitzetten van een teamlid -->
    <div id="teamBevestig" class="hidden venster-laag" style="z-index:130">
        <div class="venster-midden">
            <div class="venster vrij max-w-sm midden">
                <span id="teamBevestigIcoon" class="venster-icoon" aria-hidden="true"><i class="fa-solid fa-circle-question"></i></span>
                <p id="teamBevestigTitel" class="venster-titel">Zeker weten?</p>
                <p id="teamBevestigTekst" class="lead mx-auto"></p>
                <div class="flex flex-col gap-2 w-full mt-2">
                    <button type="button" id="teamBevestigJa" class="btn dark breed cursor-pointer">Ja, doen</button>
                    <button type="button" id="teamBevestigNee" class="btn line breed cursor-pointer">Annuleren</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Melding bij een net geactiveerd abonnement: een toast onderin -->
    <div id="dashToast" class="fixed bottom-6 left-1/2 z-[140] text-sm px-5 py-3 rounded-full"></div>
    @if($abonnementNodig)
        @include('dashboard.abonnement-overlay')
    @endif
    @unless($setupKlaar)
        @include('dashboard.setup-modal')
    @endunless

    <script src="{{ asset('assets/stijl-tiles.js') }}?v={{ filemtime(public_path('assets/stijl-tiles.js')) }}"></script>
    <script src="{{ asset('assets/dashboard.js') }}?v={{ filemtime(public_path('assets/dashboard.js')) }}"></script>
    @unless($setupKlaar)
        {{-- Dezelfde wizard als /onboarding, in modal-modus (zie setup-modal.blade.php) --}}
        <script src="{{ asset('assets/onboarding/app.js') }}?v={{ filemtime(public_path('assets/onboarding/app.js')) }}"></script>
    @endunless
</body>
</html>
