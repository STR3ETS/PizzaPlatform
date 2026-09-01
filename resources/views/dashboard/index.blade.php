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
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
@php $abonnementNodig = ! auth()->user()->heeftToegang(); @endphp
<body class="bg-crema font-body text-cacao min-h-screen antialiased overflow-x-hidden {{ $abonnementNodig ? 'overflow-hidden' : '' }}">

    <!-- Zwevende deco-emoji's -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden select-none" aria-hidden="true">
        <span class="float-emoji" style="top:14%; left:30%; --delay:0s;   --size:2.2rem;">🍕</span>
        <span class="float-emoji" style="top:75%; left:35%; --delay:1.4s; --size:1.8rem;">🍅</span>
        <span class="float-emoji" style="top:20%; left:90%; --delay:.7s;  --size:2rem;">🧀</span>
        <span class="float-emoji" style="top:70%; left:88%; --delay:2.2s; --size:2rem;">🌿</span>
    </div>

    <!-- Zwevende navigatie-rail (desktop) -->
    <aside class="dash-rail min-w-[300px]">
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
            <button type="button" data-nav="bestellingen" class="rail-item"><span class="r-ico">🧾</span> Bestellingen <span id="railOrdersBadge" class="n-soon" style="background:var(--color-tomato); color:#fff; display:none">0</span></button>
            <button type="button" data-nav="menukaart" class="rail-item"><span class="r-ico">📋</span> Menukaart</button>
            <button type="button" data-nav="bestelpagina" class="rail-item"><span class="r-ico">🎨</span> Bestelpagina</button>
            <button type="button" data-nav="webshop" class="rail-item"><span class="r-ico">🛒</span> Webshop</button>
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
        <button type="button" data-nav="overzicht" class="tab-item actief" aria-label="Overzicht"><span class="t-ico"><i class="fa-solid fa-house" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="bestellingen" class="tab-item" aria-label="Bestellingen"><span class="t-ico"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="menukaart" class="tab-item" aria-label="Menukaart"><span class="t-ico"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="bestelpagina" class="tab-item" aria-label="Bestelpagina"><span class="t-ico"><i class="fa-solid fa-palette" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="instellingen" class="tab-item" aria-label="Instellingen"><span class="t-ico"><i class="fa-solid fa-gear" aria-hidden="true"></i></span></button>
        <button type="button" data-nav="overzicht" id="tabStatus" class="tab-status" aria-label="Online-status">
            <span class="ts-dot" aria-hidden="true"></span>
            <span id="tabStatusTekst">Offline</span>
        </button>
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
            @php $setupKlaar = (bool) (auth()->user()->onboarding['setup_compleet'] ?? false); @endphp

            <!-- Bovenblok: meldingen en status links, profielkaart rechts -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_19rem] gap-4 mb-4 items-stretch">
            <div class="space-y-4 min-w-0 flex flex-col">
            @unless($setupKlaar)
            <!-- Eerst je zaak afmaken: de rest van het dashboard blijft dicht tot dit klaar is -->
            <div id="setupBanner" class="dash-card rise flex flex-col md:flex-row md:items-center gap-4" style="--d:.02s; background:color-mix(in srgb, var(--color-gold) 16%, #fff); border-color:color-mix(in srgb, var(--color-gold) 55%, transparent)">
                <img src="{{ asset('stickers') }}/behind-laptop.png" alt="" loading="lazy" class="w-16 shrink-0 hidden sm:block">
                <div class="flex-1">
                    <p class="font-display text-xl leading-tight">Nog even je zaak afmaken 🚧</p>
                    <p class="text-sm font-extrabold text-cacao/55 mt-1">Je account staat! Vul nu stap voor stap je openingstijden, menukaart, spaarpunten, bestel-adres en huisstijl in. Daarna kun je echt aan de slag.</p>
                </div>
                <a href="{{ route('onboarding') }}?stap=hours" class="btn-primary shrink-0 !px-6 !py-3">Verder met instellen 🚀</a>
            </div>
            @endunless

            <!-- Online-status -->
            <div class="dash-card rise flex flex-col md:flex-row md:items-center gap-4" style="--d:.03s">
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
            <div id="statusWaarschuwing" class="hidden flex-col sm:flex-row sm:items-center gap-3 rounded-2xl border-4 px-4 py-3"
                style="display:none; background:color-mix(in srgb, var(--color-gold) 16%, #fff); border-color:color-mix(in srgb, var(--color-gold) 55%, transparent)">
                <p id="waarschuwingTekst" class="flex-1 text-sm font-extrabold"></p>
                <button id="waarschuwingActie" type="button" class="btn-primary !text-sm !px-5 !py-2 shrink-0"></button>
            </div>

            <!-- Openingstijden in één oogopslag, met vandaag uitgelicht -->
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
            <div class="dash-card rise flex-1 flex flex-col" style="--d:.05s">
                <div class="flex items-center gap-3 mb-3">
                    <p class="font-display text-xl flex-1">🕐 Jouw openingstijden</p>
                    <a href="{{ route('onboarding') }}?stap=hours" class="skip-link !mt-0">wijzig</a>
                </div>
                <div class="flex-1 grid grid-cols-7 gap-1.5 text-center">
                    @foreach($tijdDagen as $tijdDag)
                        @php $tijd = $tijdVoor($tijdDag); @endphp
                        <div class="rounded-xl px-1 py-2 flex flex-col items-center justify-center {{ $tijdDag === $tijdVandaag ? 'border-2' : '' }}" style="background:{{ $tijd ? 'color-mix(in srgb, var(--color-basil) 10%, #fff)' : 'var(--color-crema)' }};{{ $tijdDag === $tijdVandaag ? ' border-color:var(--color-gold)' : '' }}">
                            <p class="text-[11px] font-extrabold uppercase {{ $tijd ? '' : 'text-cacao/35' }}">{{ $tijdDag }}</p>
                            @if($tijd)
                                <p class="text-[10px] font-extrabold text-cacao/60 leading-tight mt-0.5">{{ $tijd[0] }}</p>
                                <p class="text-[10px] font-extrabold text-cacao/60 leading-tight">{{ $tijd[1] }}</p>
                            @else
                                <p class="text-[10px] font-extrabold text-cacao/30 leading-tight mt-0.5">dicht</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="text-xs font-extrabold text-cacao/45 mt-3">
                    @if(empty($tijdOb['days']))
                        Je hebt je openingstijden nog niet ingesteld.
                    @elseif($tijdVoor($tijdVandaag))
                        Vandaag geopend van {{ $tijdVoor($tijdVandaag)[0] }} tot {{ $tijdVoor($tijdVandaag)[1] }}.
                    @else
                        Vandaag ben je gesloten.
                    @endif
                </p>
            </div>

            </div>

            <!-- Profielkaart: wie je bent en waar klanten je vinden -->
            <aside class="dash-card rise flex flex-col" style="--d:.06s">
                @php
                    $profielOb = auth()->user()->onboarding ?? [];
                    $profielDomein = ($profielOb['domainMode'] ?? 'sub') === 'own' && ! empty($profielOb['ownDomain'])
                        ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $profielOb['ownDomain']))
                        : auth()->user()->slug . '.mijnpizzeria.nl';
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 shrink-0 rounded-2xl overflow-hidden grid place-items-center bg-crema border-2 border-crema-dark">
                        @if($profielOb['logo'] ?? null)
                            <img src="{{ $profielOb['logo'] }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl">🍕</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-display text-xl leading-tight truncate">{{ $profielOb['name'] ?? 'Jouw zaak' }}</p>
                        <p class="text-xs font-extrabold text-cacao/45 truncate">{{ auth()->user()->name }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-3">
                    <span class="text-[11px] font-extrabold rounded-full px-2.5 py-1 {{ $setupKlaar ? '' : 'text-tomato' }}" style="background:var(--color-crema-dark)">{{ $setupKlaar ? 'Zaak compleet ✓' : 'Zaak nog niet af' }}</span>
                </div>
                <div class="mt-4 space-y-2.5 text-sm font-extrabold border-t-2 border-crema-dark pt-3.5">
                    <p class="flex items-center gap-2 min-w-0"><span class="shrink-0">🌐</span><span class="truncate">{{ $profielDomein }}</span></p>
                    <p class="flex items-center gap-2 min-w-0"><span class="shrink-0">✉️</span><span class="truncate">{{ auth()->user()->email }}</span></p>
                    @if(($profielOb['phone'] ?? '') !== '')
                        <p class="flex items-center gap-2 min-w-0"><span class="shrink-0">📞</span><span class="truncate">{{ $profielOb['phone'] }}</span></p>
                    @endif
                    @if(($profielOb['city'] ?? '') !== '')
                        <p class="flex items-center gap-2 min-w-0"><span class="shrink-0">📍</span><span class="truncate">{{ $profielOb['city'] }}</span></p>
                    @endif
                </div>
                <div class="flex-1 flex flex-col mt-4">
                    @if(auth()->user()->abonnement_actief)
                    @php
                        $aboSinds = auth()->user()->abonnement_sinds ?? now();
                        $aboVolgende = $aboSinds->copy();
                        while ($aboVolgende->isPast()) {
                            $aboVolgende = $aboVolgende->addMonthNoOverflow();
                        }
                    @endphp
                    <!-- Abonnement actief, met de volgende facturatiedatum -->
                    <div class="mt-auto flex flex-col justify-center rounded-xl border-2 px-3.5 py-3" style="background:color-mix(in srgb, var(--color-basil) 12%, #fff); border-color:color-mix(in srgb, var(--color-basil) 40%, transparent)">
                        <p class="text-sm font-extrabold">Abonnement actief</p>
                        <p class="text-xs font-extrabold text-cacao/45 mt-0.5">&euro; 24,95 per maand. Volgende facturatie op {{ $aboVolgende->locale('nl')->isoFormat('D MMMM YYYY') }}.</p>
                    </div>
                    @elseif(auth()->user()->inProefperiode())
                    @php $proefDagen = max(1, (int) ceil(now()->diffInDays(auth()->user()->proef_tot, false))); @endphp
                    <!-- Proefperiode: gratis proberen, met een korte route naar het abonnement -->
                    <div class="flex-1 flex flex-col justify-center rounded-xl border-2 px-3.5 py-3" style="background:color-mix(in srgb, var(--color-basil) 12%, #fff); border-color:color-mix(in srgb, var(--color-basil) 40%, transparent)">
                        <p class="text-sm font-extrabold">🕒 Proefperiode: nog <b>{{ $proefDagen }} {{ $proefDagen === 1 ? 'dag' : 'dagen' }}</b></p>
                        <p class="text-xs font-extrabold text-cacao/45 mt-0.5">Daarna &euro; 24,95 per maand, opzegbaar per maand.</p>
                        <form method="POST" action="{{ route('abonnement.starten') }}" class="mt-2.5">
                            @csrf
                            <button type="submit" class="btn-primary w-full !text-sm !py-2">Nu abonneren 🍕</button>
                        </form>
                    </div>
                    @endif
                    <button type="button" data-nav="instellingen" class="skip-link !mt-3">Profiel aanpassen</button>
                </div>
            </aside>
            </div>

            <!-- Openstaande bestellingen -->
            <div class="dash-card rise" style="--d:.04s">
                <p class="font-display text-xl mb-3">Openstaande bestellingen</p>
                <div id="openOrders" class="space-y-2.5"></div>
                <div id="openLeeg" class="empty-box hidden">🎉 Geen openstaande bestellingen, alles is de deur uit!</div>
            </div>

            <!-- SECTIE: cijfers en inzichten -->
            <p class="text-[.8rem] font-extrabold uppercase tracking-wider text-cacao/35 mt-10 mb-3">📊 Cijfers en inzichten</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="dash-card rise" style="--d:.05s">
                    <p class="lbl !ml-0">Omzet vandaag</p>
                    <p id="statOmzet" class="font-display text-4xl mt-1" data-cents="{{ $vandaagBestellingen->sum('totaal') }}">€ {{ number_format($vandaagBestellingen->sum('totaal') / 100, 2, ',', '.') }}</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">{{ $vandaagBestellingen->count() ? 'Van ' . $vandaagBestellingen->count() . ' bestelling' . ($vandaagBestellingen->count() === 1 ? '' : 'en') . ' vandaag' : 'De teller start bij je eerste bestelling' }}</p>
                </div>
                <div class="dash-card rise" style="--d:.08s">
                    <p class="lbl !ml-0">Bestellingen vandaag</p>
                    <p id="statAantal" class="font-display text-4xl mt-1" data-teller="{{ $vandaagBestellingen->count() }}">0</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">{{ $vandaagBestellingen->count() ? 'Lekker bezig! 💪' : 'Wachten op je eerste 🤞' }}</p>
                </div>
                <div class="dash-card rise" style="--d:.11s">
                    <p class="lbl !ml-0">Gemiddelde bestelling</p>
                    <p class="font-display text-4xl mt-1">&euro; {{ number_format(($gemBestelwaarde ?? 0) / 100, 2, ',', '.') }}</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">Over de laatste 30 dagen</p>
                </div>
                <div class="dash-card rise" style="--d:.14s">
                    <p class="lbl !ml-0">Terugkerende klanten</p>
                    <p class="font-display text-4xl mt-1">{{ $terugkerend }}</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">{{ $piekUur !== null ? 'Drukste moment: ' . sprintf('%02d:00 - %02d:00', $piekUur, $piekUur + 1) : 'Bestelden vaker dan een keer' }}</p>
                </div>
            </div>

            <!-- De coach: inzichten die geld opleveren, naast de drukteverwachting -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_20rem] gap-4 mb-4">
                <div class="dash-card rise" style="--d:.17s">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="font-display text-xl flex-1">🍕 Pizza Coach</p>
                        @if($coachActief && $weekPct !== null)
                            <span class="text-[11px] font-extrabold rounded-full px-2.5 py-1 {{ $weekPct >= 0 ? 'text-basil' : 'text-tomato' }}" style="background:var(--color-crema)">
                                Vorige week &euro; {{ number_format($weekOmzet / 100, 0, ',', '.') }}, {{ $weekPct >= 0 ? $weekPct . '% meer' : abs($weekPct) . '% minder' }} dan de week ervoor
                            </span>
                        @endif
                    </div>
                    @if(! $coachActief)
                        <p class="text-sm font-extrabold text-cacao/45 mt-2">De coach heeft een paar weken aan bestellingen nodig. Zodra er genoeg data is, zie je hier acties die direct omzet opleveren.</p>
                    @else
                        <div class="divide-y-2 divide-crema-dark">
                            @if($slapend->isNotEmpty())
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center gap-2.5">
                                    <p class="flex-1 text-sm font-extrabold">😴 {{ $slapend->count() }} klant{{ $slapend->count() === 1 ? ' heeft' : 'en hebben' }} al 30 dagen niet besteld.
                                        <button type="button" id="coachSlapendKnop" class="text-cacao/45 underline underline-offset-2 hover:text-cacao cursor-pointer">Bekijk wie</button>
                                    </p>
                                    <button type="button" disabled title="Kan zodra e-mail actief is" class="btn-primary !text-sm !px-4 !py-2 shrink-0 opacity-40 cursor-not-allowed">Stuur win-back actie</button>
                                </div>
                            @endif
                            @if($combo && $combo['pinId'])
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center gap-2.5">
                                    <p class="flex-1 text-sm font-extrabold">🧀 {{ $combo['a'] }} en {{ $combo['b'] }} worden vaak samen besteld ({{ $combo['aantal'] }} keer).</p>
                                    @if((string) $upsellPinActief === (string) $combo['pinId'])
                                        <button type="button" data-coach-pin-uit class="btn-primary !text-sm !px-4 !py-2 shrink-0" style="background:var(--color-basil)">✓ Upsell actief, zet uit</button>
                                    @else
                                        <button type="button" data-coach-pin="{{ $combo['pinId'] }}" class="btn-primary !text-sm !px-4 !py-2 shrink-0">Zet {{ $combo['pinNaam'] }} bovenaan de upsell</button>
                                    @endif
                                </div>
                            @endif
                            @if($rustig)
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center gap-2.5">
                                    <p class="flex-1 text-sm font-extrabold">🕐 {{ ucfirst($rustig['dag']) }} tussen {{ $rustig['van'] }} en {{ $rustig['tot'] }} is je rustigste moment.</p>
                                    <button type="button" disabled title="Kortingsacties komen binnenkort" class="btn-primary !text-sm !px-4 !py-2 shrink-0 opacity-40 cursor-not-allowed">Maak actie</button>
                                </div>
                            @endif
                            @if($slapend->isEmpty() && ! ($combo && $combo['pinId']) && ! $rustig)
                                <p class="text-sm font-extrabold text-cacao/45 py-3">Geen bijzonderheden deze week. De coach blijft meekijken.</p>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="dash-card rise" style="--d:.2s">
                    <div class="flex items-center justify-between">
                        <p class="lbl !ml-0">Schatting drukte</p>
                        <span id="drukteBadge" class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Vandaag</span>
                    </div>
                    <div id="drukteChart" class="flex items-end gap-[3px] h-20 mt-2"></div>
                    <p id="drukteSub" class="text-xs font-extrabold text-cacao/40 mt-2">…</p>
                </div>
            </div>

            <!-- Slapende klanten: wie zijn het? -->
            @if($coachActief && $slapend->isNotEmpty())
            <div id="coachSlapendModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-cacao/60 backdrop-blur-sm">
                <div class="min-h-full flex items-center justify-center p-4 py-10">
                    <div class="relative w-full max-w-md bg-white rounded-3xl border-4 border-crema-dark shadow-2xl p-6 animate-pop">
                        <button type="button" id="coachSlapendSluit" class="absolute top-4 right-4 w-10 h-10 rounded-full grid place-items-center cursor-pointer hover:bg-crema transition-colors" style="background:var(--color-crema-dark)" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                        <p class="font-display text-2xl mb-1">Slapende klanten 😴</p>
                        <p class="text-sm font-extrabold text-cacao/50 mb-4">Laatste bestelling 30 dagen of langer geleden. Zodra e-mail actief is kun je ze een win-back actie sturen.</p>
                        <div class="max-h-80 overflow-y-auto divide-y-2 divide-crema-dark">
                            @foreach($slapend as $slaper)
                                <div class="py-2.5 flex items-center gap-3 text-sm font-extrabold">
                                    <span class="flex-1 min-w-0 truncate">{{ $slaper['naam'] }}</span>
                                    <span class="shrink-0 text-cacao/45">laatst {{ $slaper['laatste'] }}</span>
                                    <span class="shrink-0 text-cacao/60">&euro; {{ number_format($slaper['besteed'] / 100, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Omzet breed, met toppers en doorlooptijd ernaast -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="dash-card rise lg:col-span-2 flex flex-col" style="--d:.23s">
                    <p class="font-display text-xl">Omzet afgelopen 7 dagen</p>
                    <div id="omzetWeek" class="flex items-end gap-2 h-24 mt-auto pt-3"></div>
                </div>
                <div class="flex flex-col gap-4">
                    <!-- Toppers deze week -->
                    <div class="dash-card rise flex-1" style="--d:.26s">
                        <p class="font-display text-xl mb-3">Toppers deze week</p>
                        <div id="topGerechten" class="space-y-3"></div>
                    </div>
                    <!-- Gemiddelde doorlooptijd -->
                    <div class="dash-card rise" style="--d:.29s">
                        <p class="lbl !ml-0">Gemiddelde doorlooptijd</p>
                        <p id="doorloopTijd" class="font-display text-4xl mt-1">&ndash;</p>
                        <p id="doorloopSub" class="mt-2.5 text-xs font-extrabold text-cacao/50"></p>
                    </div>
                </div>
            </div>

            <!-- SECTIE: jouw zaak regelen -->
            <p class="text-[.8rem] font-extrabold uppercase tracking-wider text-cacao/35 mt-10 mb-3">🍕 Jouw zaak regelen</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Checklist met voortgangsring -->
                <div class="dash-card rise" style="--d:.32s">
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

                <div class="flex flex-col gap-4">
                    <!-- Snel regelen -->
                    <div class="dash-card rise" style="--d:.35s">
                        <p class="font-display text-xl mb-3">Snel regelen</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" data-nav="menukaart" class="qa-btn"><span class="q-ico">📋</span> Menu aanpassen</button>
                            <a href="{{ route('onboarding') }}?stap=hours" class="qa-btn"><span class="q-ico">🕐</span> Tijden wijzigen</a>
                            <button type="button" data-inst="stijl" class="qa-btn"><span class="q-ico">🎨</span> Pagina stylen</button>
                            <button type="button" data-nav="webshop" class="qa-btn"><span class="q-ico">🛒</span> Webshop</button>
                        </div>
                    </div>

                    <!-- Deel je bestelpagina -->
                    <div class="dash-card rise flex-1" style="--d:.38s">
                        <p class="font-display text-xl mb-3">Deel je bestelpagina</p>
                        <div class="flex items-center gap-4">
                            {{-- Zaak nog niet af: de QR en link wazig, delen heeft nog geen zin --}}
                            <div id="qrBox" class="w-[104px] h-[104px] shrink-0 bg-white rounded-xl border-2 border-crema-dark grid place-items-center overflow-hidden p-1.5" @unless($setupKlaar) style="filter:blur(5px); pointer-events:none" @endunless></div>
                            <div class="min-w-0 flex-1">
                                <p id="shareLink" class="text-xs font-extrabold text-cacao/60 break-all mb-2 select-none" @unless($setupKlaar) style="filter:blur(4px)" @endunless>…</p>
                                <button id="copyLink" type="button" class="btn-primary !text-sm !px-4 !py-2">Kopieer link</button>
                                <p class="text-[11px] font-extrabold text-cacao/40 mt-2">Print de QR voor op je toonbank of flyers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PANEEL: Bestellingen (touch-first) -->
        <section data-panel="bestellingen" class="panel">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div id="orderTabs" class="flex flex-wrap gap-2"></div>
                <button id="demoOrderBtn" type="button" class="btn-primary !text-base !px-5 !py-2.5">Voorbeeld toevoegen ▶</button>
            </div>
            <div id="orderLijst" class="space-y-3"></div>
            <div id="ordersLeeg" class="dash-card text-center py-8 hidden">
                <img src="{{ asset('stickers/showing-pizza-order.png') }}" alt="" class="h-32 mx-auto mb-3 select-none pointer-events-none">
                <p class="font-display text-2xl mb-1">Hier rollen je bestellingen binnen</p>
                <p id="ordersLeegTekst" class="text-sm font-extrabold text-cacao/50 max-w-sm mx-auto">…</p>
            </div>
        </section>

        <!-- PANEEL: Menukaart -->
        <section data-panel="menukaart" class="panel">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <p class="font-display text-2xl">Jouw menukaart</p>
                    <p id="menuSamenvatting" class="text-sm font-extrabold text-cacao/50">Je hebt nog geen gerechten toegevoegd.</p>
                </div>
                <button type="button" id="menuNieuw" class="btn-primary !text-lg !px-6 !py-3 !flex items-center gap-2"><i class="fa-solid fa-plus" aria-hidden="true"></i> Gerecht toevoegen</button>
            </div>
            <div id="menuCats" class="flex flex-wrap gap-2 mb-4"></div>
            <div id="menuLijst" class="grid md:grid-cols-2 gap-4"></div>
            <div id="menuLeeg" class="hidden">
                <div class="dash-card flex flex-col sm:flex-row items-center gap-5">
                    <img src="{{ asset('stickers/sprinkling-cheese.png') }}" alt="" class="h-32 select-none pointer-events-none">
                    <div class="flex-1 text-center sm:text-left">
                        <p class="font-display text-2xl mb-1">Nog niks op de kaart</p>
                        <p class="text-sm font-extrabold text-cacao/50 mb-4">Voeg je eerste gerecht toe, inclusief opties voor de klant en allergenen.</p>
                        <button type="button" class="btn-primary !text-lg !px-8 !py-3" data-menu-nieuw>Eerste gerecht toevoegen</button>
                    </div>
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
                    <button type="button" data-inst="stijl" class="btn-primary !text-lg !px-8 !py-3">Pagina stylen</button>
                </div>
            </div>

            <!-- Link naar de pagina -->
            <div class="dash-card mt-4">
                <p class="font-display text-xl mb-1">🔗 Jouw bestel-adres</p>
                <p id="paginaLink" class="text-sm font-extrabold text-cacao/60 break-all mb-4"></p>
                <div class="flex flex-wrap gap-3">
                    <a id="paginaOpen" href="#" target="_blank" rel="noopener" class="btn-primary inline-block !text-base !px-6 !py-2.5">Bekijk je pagina</a>
                    <button type="button" id="paginaKopieer" class="btn-primary btn-grey !text-base !px-6 !py-2.5">Kopieer link</button>
                </div>
            </div>

            <!-- Live voorbeeld van de eigen pagina -->
            <div class="dash-card mt-4">
                <p class="font-display text-xl mb-1">📱 Live voorbeeld</p>
                <p class="text-sm font-extrabold text-cacao/50 mb-4">Zo ziet je bestelpagina er op dit moment uit voor je klanten.</p>
                <div class="relative w-full aspect-video rounded-2xl border-4 border-crema-dark overflow-hidden bg-white">
                    <iframe id="paginaPreview" title="Voorbeeld van je bestelpagina" class="absolute inset-0 pointer-events-none select-none" style="width:200%; height:200%; transform:scale(.5); transform-origin:top left; border:0"></iframe>
                </div>
                <p class="text-xs font-extrabold text-cacao/40 mt-2">Het voorbeeld ververst vanzelf na het opslaan van je stijl.</p>
            </div>
        </section>

        <!-- PANEEL: Webshop -->
        <section data-panel="webshop" class="panel">
            @php
                $doosOb = auth()->user()->onboarding ?? [];
                $doosQrPatroon = ['111010111', '101001101', '111010111', '000101100', '101110011', '010011010', '111011010', '101001110', '111010011'];
                // Bestelknoppen per categorie: de url invullen zodra de groothandel de links aanlevert
                $webshopCats = [
                    ['emoji' => '🥤', 'naam' => 'Frisdrank', 'knop' => 'Bestel uw frisdrank', 'url' => null,
                        'tekst' => 'Blikjes en flessen voor bij de bestelling, tegen groothandelsprijzen.',
                        'items' => ['Cola', 'Sinas', 'Ice tea', 'Bronwater', 'Sappen']],
                    ['emoji' => '🧀', 'naam' => 'Food', 'knop' => 'Bestel uw food', 'url' => null,
                        'tekst' => 'De basis van je keuken, vers en voordelig ingekocht.',
                        'items' => ['Bloem', 'Tomatensaus', 'Mozzarella', 'Salami', 'Olijfolie']],
                    ['emoji' => '🥡', 'naam' => 'Verpakking', 'knop' => 'Bestel uw verpakking', 'url' => null,
                        'tekst' => 'Alles om netjes mee te geven, van sauscupjes tot pastabakken.',
                        'items' => ['Pastabakken', 'Sauscupjes', 'Aluminium bakken', 'Bestek', 'Servetten']],
                ];
                $webshopMarketingUrl = null; // link van de groothandel voor dozen en tasjes met opdruk
            @endphp
            <p class="font-display text-2xl mb-1">Webshop</p>
            <p class="text-sm font-extrabold text-cacao/50 mb-4">Alles voor je zaak op één plek. Je bestelt rechtstreeks bij onze vaste groothandel, geleverd tot aan de deur.</p>

            {{-- Blikvanger: marketing met jouw opdruk, inclusief de spaar-QR doos --}}
            <div class="dash-card rise mb-4" style="--d:.03s">
                <div class="flex flex-col sm:flex-row items-center gap-7">
                    <div class="relative w-48 h-48 shrink-0 rounded-2xl grid place-items-center" style="background:#DDBE92; background-image:repeating-linear-gradient(45deg, rgba(0,0,0,.025) 0 7px, transparent 7px 14px); box-shadow:inset 0 0 0 3px #C9A369, 0 10px 22px rgb(0 0 0 / .12)">
                        <div class="absolute inset-3 rounded-xl pointer-events-none" style="border:2px dashed rgba(93,58,33,.22)"></div>
                        <div class="text-center px-4">
                            <div class="w-16 h-16 mx-auto rounded-2xl overflow-hidden grid place-items-center" style="background:rgba(255,255,255,.55)">
                                @if($doosOb['logo'] ?? null)
                                    <img src="{{ $doosOb['logo'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl">🍕</span>
                                @endif
                            </div>
                            <p class="mt-2 font-display text-sm leading-tight" style="color:#5D3A21">{{ $doosOb['name'] ?? 'Jouw zaak' }}</p>
                        </div>
                        <div class="absolute right-3.5 bottom-3.5 bg-white rounded-md p-1.5 shadow-sm">
                            <div class="grid grid-cols-9 gap-[1px]">
                                @foreach($doosQrPatroon as $qrRij)
                                    @foreach(str_split($qrRij) as $qrCel)
                                        <span class="w-[3px] h-[3px] {{ $qrCel === '1' ? '' : 'opacity-0' }}" style="background:#38221A"></span>
                                    @endforeach
                                @endforeach
                            </div>
                            <p class="text-[5px] font-extrabold text-center mt-0.5 tracking-wide" style="color:#38221A">SCAN &amp; SPAAR</p>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-display text-xl">🎁 Marketing met jouw opdruk</p>
                        <p class="text-sm font-extrabold text-cacao/50 mt-1.5">Pizzadozen en draagtasjes met jouw logo in full-color. De spaar-QR op de doos geeft klanten korting bij hun volgende bestelling en brengt ze rechtstreeks terug naar jouw bestelpagina.</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach(['Pizzadozen', 'Draagtasjes', 'Stickers', 'Servetten met logo'] as $marketingItem)
                                <span class="text-xs font-extrabold text-cacao/55 bg-crema rounded-full border-2 border-crema-dark px-3 py-1">{{ $marketingItem }}</span>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ $webshopMarketingUrl ?? '#' }}" @if($webshopMarketingUrl) target="_blank" rel="noopener" @endif class="btn-primary inline-block !text-base !px-6 !py-2.5">Bestel uw marketing 🎁</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- De overige categorieën: knoppen gaan straks direct door naar de groothandel --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($webshopCats as $i => $webshopCat)
                    <div class="dash-card rise flex flex-col" style="--d:.{{ 6 + $i * 3 }}s">
                        <p class="font-display text-xl">{{ $webshopCat['emoji'] }} {{ $webshopCat['naam'] }}</p>
                        <p class="text-sm font-extrabold text-cacao/50 mt-1.5">{{ $webshopCat['tekst'] }}</p>
                        <div class="flex flex-wrap gap-2 mt-3 mb-4">
                            @foreach($webshopCat['items'] as $webshopItem)
                                <span class="text-xs font-extrabold text-cacao/55 bg-crema rounded-full border-2 border-crema-dark px-3 py-1">{{ $webshopItem }}</span>
                            @endforeach
                        </div>
                        <div class="mt-auto">
                            <a href="{{ $webshopCat['url'] ?? '#' }}" @if($webshopCat['url']) target="_blank" rel="noopener" @endif class="btn-primary inline-block w-full text-center !text-base !px-4 !py-2.5">{{ $webshopCat['knop'] }}</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-xs font-extrabold text-cacao/40 mt-4">Bestellen en afrekenen doe je rechtstreeks bij onze samenwerkende groothandel. Zodra de koppeling live is, brengen de knoppen je daar meteen naartoe.</p>
        </section>

        <!-- PANEEL: Instellingen -->
        @php
            $ob = auth()->user()->onboarding ?? [];
            $obSlug = substr(preg_replace('/[^a-z0-9]+/', '', strtolower(\Illuminate\Support\Str::ascii($ob['name'] ?? ''))), 0, 30) ?: 'jouwpizzeria';
            $eigenDomein = ($ob['domainMode'] ?? 'sub') === 'own' && ! empty($ob['ownDomain']);
        @endphp
        <section data-panel="instellingen" class="panel">
            <p class="font-display text-2xl mb-4">Instellingen</p>
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="dash-card">
                    <div class="flex items-center gap-3 mb-3">
                        <p class="font-display text-xl flex-1">🍕 Jouw zaak</p>
                        <button type="button" data-inst="zaak" class="skip-link !mt-0">wijzig</button>
                    </div>
                    <div class="space-y-2 text-sm font-extrabold">
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Pizzeria</span><span class="text-right truncate">{{ $ob['name'] ?? '-' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Contactpersoon</span><span class="text-right truncate">{{ $ob['person'] ?? '-' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Telefoon</span><span class="text-right truncate">{{ ($ob['phone'] ?? '') !== '' ? $ob['phone'] : 'Nog niet ingevuld' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">E-mail</span><span class="text-right truncate">{{ auth()->user()->email }}</span></div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="flex items-center gap-3 mb-3">
                        <p class="font-display text-xl flex-1">📇 Bedrijfsgegevens</p>
                        <button type="button" data-inst="bedrijf" class="skip-link !mt-0">wijzig</button>
                    </div>
                    <div class="space-y-2 text-sm font-extrabold">
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">KVK-nummer</span><span class="text-right truncate">{{ ($ob['kvk'] ?? '') !== '' ? $ob['kvk'] : 'Nog niet ingevuld' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Adres</span><span class="text-right truncate">{{ ($ob['street'] ?? '') !== '' ? $ob['street'] : 'Nog niet ingevuld' }}</span></div>
                        <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Plaats</span><span class="text-right truncate">{{ trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? '')) !== '' ? trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? '')) : 'Nog niet ingevuld' }}</span></div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="dash-card">
                        <div class="flex items-center gap-3 mb-3">
                            <p class="font-display text-xl flex-1">🕐 Openingstijden</p>
                            <button type="button" data-inst="tijden" class="skip-link !mt-0">wijzig</button>
                        </div>
                        <p id="tijdenSamenvatting" class="text-sm font-extrabold text-cacao/50 mb-3">Nog niet ingesteld.</p>
                        <div id="tijdenLijst" class="space-y-1.5 text-sm font-extrabold"></div>
                    </div>

                    <div class="dash-card">
                        <div class="flex items-center gap-3 mb-3">
                            <p class="font-display text-xl flex-1">🌟 Spaarpunten</p>
                            <button type="button" data-inst="punten" class="skip-link !mt-0">wijzig</button>
                        </div>
                        <p id="puntenWaarde" class="text-sm font-extrabold">{{ ($ob['spaarpunten'] ?? false) ? 'Aan, klanten sparen automatisch mee' : 'Uit' }}</p>
                        @if($ob['spaarpunten'] ?? false)
                            @php
                                $spaarders = auth()->user()->klanten()->where('punten', '>', 0)->orderByDesc('punten')->take(5)->get();
                                $puntenUitstaand = (int) auth()->user()->klanten()->sum('punten');
                            @endphp
                            <div class="mt-3 pt-3 border-t-2 border-crema-dark">
                                <p class="text-xs font-extrabold text-cacao/45 mb-2">Uitstaand: {{ $puntenUitstaand }} punten, samen goed voor &euro; {{ number_format(intdiv($puntenUitstaand, 100) * 5, 0, ',', '.') }} korting</p>
                                @forelse($spaarders as $spaarder)
                                    <div class="flex items-center justify-between gap-3 text-sm font-extrabold py-1">
                                        <span class="truncate">{{ $spaarder->naam }}</span>
                                        <span class="shrink-0 text-cacao/60">{{ $spaarder->punten }} punten{{ intdiv($spaarder->punten, 100) > 0 ? ', kan € ' . (intdiv($spaarder->punten, 100) * 5) . ' claimen' : '' }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs font-extrabold text-cacao/40">Nog geen klanten met punten.</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="dash-card">
                        <p class="font-display text-xl mb-3">💶 Betalen</p>
                        <p class="text-sm font-extrabold">Online betalen via Stripe</p>
                        <p class="text-xs font-extrabold text-cacao/45 mt-1">iDEAL, creditcard, Apple Pay en Google Pay. Uitbetalen gaat rechtstreeks naar jouw rekening; de koppeling zetten we binnenkort samen live.</p>
                    </div>

                    <div class="dash-card">
                        <div class="flex items-center gap-3 mb-3">
                            <p class="font-display text-xl flex-1">🌐 Jouw domein</p>
                            <button type="button" data-inst="domein" class="skip-link !mt-0">wijzig</button>
                        </div>
                        <p id="domeinWaarde" class="text-sm font-extrabold break-all">{{ $eigenDomein ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $ob['ownDomain'])) : $obSlug . '.mijnpizzeria.nl' }}</p>
                    </div>

                    <div class="dash-card">
                        <p class="font-display text-xl mb-3">🎬 Hulp en account</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="introOpnieuw" type="button" class="btn-primary !text-base !px-6 !py-2.5">Bekijk de uitlegvideo</button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-grey !text-base !px-6 !py-2.5">Uitloggen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bezorggebied (kaart, alleen ter inzage) en bezorgkosten per straal -->
            <div class="grid lg:grid-cols-2 gap-4 items-start">
                <div class="dash-card">
                    <p class="font-display text-xl mb-1">🗺️ Jouw bezorggebied</p>
                    <p class="text-sm font-extrabold text-cacao/50 mb-4">Zo ver bezorg je op dit moment. De ringen horen bij de tarieven hiernaast.</p>
                    <div id="bezorgMap" class="aspect-square w-full max-w-lg mx-auto rounded-2xl border-4 border-crema-dark z-0" style="background:var(--color-crema)"></div>
                    <p id="bezorgHint" class="text-xs font-extrabold text-cacao/40 mt-2"></p>
                </div>
                <div class="dash-card">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="font-display text-xl flex-1">🛵 Bezorgkosten</p>
                        <span id="bezorgStatus" class="text-xs font-extrabold" style="color:var(--color-basil); display:none">Opgeslagen ✓</span>
                    </div>
                    <p class="text-sm font-extrabold text-cacao/50 mb-4">Stel per afstand een tarief in, bijvoorbeeld tot 2 km voor € 1,00.</p>
                    <div id="bezorgTiers" class="space-y-2 mb-2"></div>
                    <button type="button" id="bezorgTierAdd" class="skip-link !mt-0">+ straal toevoegen</button>
                    <button type="button" id="bezorgOpslaan" class="btn-primary w-full !py-3 mt-5">Bezorgkosten opslaan</button>
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

    <!-- Instellingen bewerken: popup per sectie, in dezelfde stijl als de gerecht-editor -->
    {{-- De kaart mag nooit hoger worden dan het scherm: de velden scrollen
         intern, zodat sluiten en opslaan altijd bereikbaar blijven. --}}
    <div id="instModal" class="hidden fixed inset-0 z-50 bg-cacao/60 backdrop-blur-sm">
        <div id="instModalMidden" class="h-full flex items-center justify-center p-4">
            <div id="instKaartWrap" class="relative w-full max-w-xl max-h-full">
                <div id="instMascot" class="mascot mascot-right" aria-hidden="true">
                    <div id="instMascotBubble" class="bubble"></div>
                    <img id="instMascotImg" src="{{ asset('stickers/waving-hello.png') }}" alt="">
                </div>
                <div class="relative bg-white rounded-3xl border-4 border-crema-dark shadow-2xl p-6 sm:p-10 text-center animate-pop flex flex-col max-h-[calc(100dvh-2rem)]">
                    <button type="button" id="instModalSluit" class="absolute top-4 right-4 w-10 h-10 rounded-full grid place-items-center cursor-pointer hover:bg-crema transition-colors z-10" style="background:var(--color-crema-dark)" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    <p id="instKicker" class="step-kicker shrink-0"></p>
                    <h2 id="instTitel" class="step-title shrink-0"></h2>
                    <p id="instSub" class="step-sub shrink-0"></p>
                    <div id="instVelden" class="text-left space-y-4 mt-2 flex-1 min-h-0 overflow-y-auto overscroll-contain"></div>
                    <div class="mt-8 shrink-0">
                        <button type="button" id="instOpslaan" class="btn-primary !px-10 !py-3">Opslaan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gerecht toevoegen of bewerken: stap voor stap, net als de onboarding -->
    <div id="menuModal" class="hidden fixed inset-0 z-50 bg-cacao/60 backdrop-blur-sm">
        <div id="mModalMidden" class="h-full flex items-center justify-center p-4">
            <div class="relative w-full max-w-xl max-h-full">
                <div id="mMascot" class="mascot mascot-right" aria-hidden="true">
                    <div id="mMascotBubble" class="bubble"></div>
                    <img id="mMascotImg" src="{{ asset('stickers/tossing-dough.png') }}" alt="">
                </div>
                <div class="relative bg-white rounded-3xl border-4 border-crema-dark shadow-2xl p-6 sm:p-10 text-center animate-pop flex flex-col max-h-[calc(100dvh-2rem)]">
                    <button type="button" id="menuModalSluit" class="absolute top-4 right-4 w-10 h-10 rounded-full grid place-items-center cursor-pointer hover:bg-crema transition-colors z-10" style="background:var(--color-crema-dark)" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    <p id="mKicker" class="step-kicker shrink-0"></p>
                    <h2 id="mTitel" class="step-title shrink-0"></h2>
                    <p id="mSub" class="step-sub shrink-0"></p>

                    {{-- De stappen scrollen intern; de knoppenrij eronder blijft staan. --}}
                    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain">
                    <div data-mstap="naam" class="m-stap hidden">
                        <input id="mNaam" type="text" class="inp text-center" placeholder="bijv. Margherita" maxlength="100">
                        <p class="err" id="mNaamErr"></p>
                    </div>
                    <div data-mstap="prijs" class="m-stap hidden">
                        <div class="relative max-w-xs mx-auto">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 font-extrabold text-cacao/45 text-lg">€</span>
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
                            <button type="button" id="mFotoKies" class="w-full max-w-xs mx-auto flex flex-col items-center gap-2 rounded-3xl border-4 border-dashed border-crema-dark px-6 py-8 cursor-pointer hover:border-tomato/50 transition-colors" style="background:var(--color-crema)">
                                <i class="fa-solid fa-camera text-3xl text-cacao/35" aria-hidden="true"></i>
                                <span class="font-extrabold">Foto uploaden</span>
                                <span class="text-xs font-bold text-cacao/45">JPG, PNG of WebP</span>
                            </button>
                        </div>
                        <div id="mFotoVol" class="hidden">
                            <img id="mFotoPreview" src="" alt="Foto van het gerecht" class="w-40 h-40 mx-auto rounded-3xl object-cover border-4 border-crema-dark shadow-lg">
                            <div class="mt-4 flex items-center justify-center gap-4">
                                <button type="button" id="mFotoAnders" class="skip-link !mt-0">Andere foto kiezen</button>
                                <button type="button" id="mFotoWeg" class="skip-link !mt-0">Foto verwijderen</button>
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
                        <div id="mOptieGroepen" class="space-y-3"></div>
                    </div>
                    <div data-mstap="overzicht" class="m-stap hidden text-left">
                        <div id="mOverzicht" class="space-y-2.5"></div>
                    </div>
                    </div>{{-- /scrollend stappengedeelte --}}

                    <div class="mt-8 shrink-0 flex flex-wrap items-center justify-center gap-3">
                        <button type="button" id="mTerug" class="btn-primary btn-grey">Terug</button>
                        <button type="button" id="mVolgende" class="btn-primary">Volgende</button>
                    </div>
                    <p class="enter-hint" id="mEnterHint">of druk op <b>Enter ↵</b></p>
                    <p class="mt-3"><button type="button" id="menuVerwijder" class="hidden font-extrabold text-sm cursor-pointer" style="color:var(--color-tomato)">Dit gerecht verwijderen</button></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Introvideo bij je eerste bezoek -->
    <div id="introModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-cacao/60 backdrop-blur-sm p-5">
        <div class="bg-white rounded-3xl border-4 border-crema-dark shadow-2xl max-w-2xl w-full p-6 sm:p-8 text-center animate-pop max-h-[calc(100dvh-2.5rem)] overflow-y-auto overscroll-contain">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>window.PP_BEZORG = @json(['lat' => auth()->user()->lat, 'lng' => auth()->user()->lng, 'tiers' => auth()->user()->bezorgkosten ?? []]);</script>
    <script>window.PP_DATA = @json(auth()->user()->onboarding ?? new stdClass);</script>
    @php
        $ppOrders = auth()->user()->orders()
            ->where(fn ($q) => $q->where('status', '!=', 'bezorgd')->orWhereDate('created_at', today()))
            ->latest()->get();
    @endphp
    <script>window.PP_ORDERS = @json($ppOrders);</script>
    <script>window.PP_SLUG = @json(auth()->user()->slug);</script>
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
        $omzet = collect(range(6, 0))->map(function ($terug) use ($weekOrders) {
            $dag = now()->subDays($terug);
            return [
                'label' => $dag->locale('nl')->isoFormat('dd'),
                'bedrag' => $weekOrders->filter(fn ($o) => $o->created_at->isSameDay($dag))->sum('totaal'),
            ];
        });
        $ppStats = ['toppers' => $toppers, 'doorloop' => $doorloop, 'klaarAantal' => $klaar->count(), 'omzet' => $omzet];
    @endphp
    <script>window.PP_STATS = @json($ppStats);</script>
    <script>window.PP_MENU = @json(auth()->user()->menuItems()->orderBy('volgorde')->orderBy('id')->get());</script>
    {{-- De uitlegvideo wacht tot het abonnement gestart is, anders staan er twee vensters tegelijk open --}}
    <script>window.PP_INTRO = @json(! auth()->user()->intro_seen && ! $abonnementNodig);</script>
    <script>window.PP_STATUS = @json(['online' => (bool) auth()->user()->is_online, 'mode' => auth()->user()->order_mode ?? 'bezorgen_afhalen']);</script>
    <script>window.PP_SETUP = @json($setupKlaar);</script>
    <script>window.PP_ABO = @json((bool) auth()->user()->abonnement_actief);</script>
    <script>window.PP_ABO_FEEST = @json((bool) session('abonnementGestart'));</script>

    <!-- Feestje bij een net geactiveerd abonnement: confetti plus een toast onderin -->
    <canvas id="confetti" class="pointer-events-none fixed inset-0 z-[140] hidden"></canvas>
    <div id="dashToast" class="fixed bottom-6 left-1/2 z-[140] bg-cacao text-crema font-extrabold text-sm px-5 py-3 rounded-full shadow-lg"></div>
    @if($abonnementNodig)
        @include('dashboard.abonnement-overlay')
    @endif

    <script src="{{ asset('assets/stijl-tiles.js') }}?v={{ filemtime(public_path('assets/stijl-tiles.js')) }}"></script>
    <script src="{{ asset('assets/dashboard.js') }}?v={{ filemtime(public_path('assets/dashboard.js')) }}"></script>
</body>
</html>
