<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beheer | MijnPizzeria</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: #FAF8F4;
            --color-crema-dark: #E9E2D8;
            --color-tomato: #B04A3F;
            --color-tomato-dark: #93382F;
            --color-basil: #2C7A4B;
            --color-basil-dark: #1F5C38;
            --color-gold: #B97F10;
            --color-cacao: #241712;
            --font-display: "Young Serif", serif;
            --font-body: "Inter Tight", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen antialiased overflow-x-hidden">
@php
    $euro = fn ($centen) => '€ ' . number_format($centen / 100, 2, ',', '.');
    $euroKort = fn ($centen) => '€ ' . number_format($centen / 100, 0, ',', '.');
    $aboBadge = function (array $p) {
        if ($p['abonnement'] === 'actief') return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-basil/10 text-basil">Actief</span>';
        if ($p['abonnement'] === 'proef') return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gold/15 text-cacao/70">Proef tot ' . ($p['proef_tot']?->format('d-m') ?? '?') . '</span>';
        return '<span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-tomato/10 text-tomato">Verlopen</span>';
    };
@endphp

    @include('admin.deel.nav')

    <main class="relative z-10 lg:ml-[16rem] px-5 md:px-8 pt-5 pb-28 lg:pb-10">
        <div class="max-w-6xl mx-auto">

        {{-- Topbalk: de platformcijfers die je op elk paneel wilt zien. Plakt bij het scrollen. --}}
        <div class="sticky top-3 z-30 mb-6 rounded-xl border border-crema-dark bg-white/90 backdrop-blur-md px-4 py-2.5 shadow-[0_1px_2px_rgb(36_23_18_/_.06)] flex items-center gap-x-5 gap-y-1 flex-wrap">
            <button type="button" data-nav="pizzerias" class="flex items-center gap-2 text-sm font-semibold cursor-pointer hover:text-tomato transition-colors">
                <span class="w-2.5 h-2.5 rounded-full {{ $totalen['online'] > 0 ? 'bg-basil' : 'bg-cacao/25' }}" aria-hidden="true"></span>
                {{ $totalen['online'] }} {{ $totalen['online'] === 1 ? 'zaak' : 'zaken' }} online
            </button>
            <span class="hidden sm:flex items-center gap-2 text-sm font-semibold text-cacao/45">
                <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                {{ $totalen['vandaag'] }} {{ $totalen['vandaag'] === 1 ? 'bestelling' : 'bestellingen' }} vandaag
            </span>
            @if($bijnaAf->count() > 0)
                <button type="button" data-nav="sales" class="flex items-center gap-2 text-sm font-semibold text-gold cursor-pointer hover:text-tomato transition-colors">
                    <i class="fa-solid fa-hourglass-half" aria-hidden="true"></i>
                    {{ $bijnaAf->count() }} {{ $bijnaAf->count() === 1 ? 'proef loopt' : 'proeven lopen' }} bijna af
                </button>
            @endif
            <button type="button" data-nav="financieel" class="ml-auto hidden sm:flex items-center gap-2 text-sm font-semibold cursor-pointer hover:text-tomato transition-colors">
                <i class="fa-solid fa-arrow-trend-up text-cacao/40" aria-hidden="true"></i>
                {{ $euroKort($financieel['mrr']) }} per maand
            </button>
            <span class="hidden md:flex items-center gap-2 text-sm font-semibold text-cacao/45">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span id="beheerKlok"></span>
            </span>
        </div>

        <!-- Begroeting -->
        <div class="mb-7 rise" style="--d:0s">
            <h1 class="font-display text-3xl md:text-4xl">{{ now()->hour < 12 ? 'Goedemorgen' : (now()->hour < 18 ? 'Goedemiddag' : 'Goedenavond') }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="font-semibold text-cacao/50 mt-1">Zo staat het platform ervoor.</p>
        </div>

        <!-- PANEEL: Overzicht -->
        <section data-panel="overzicht" class="panel actief">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="dash-card rise" style="--d:.03s">
                    <p class="lbl !ml-0">Pizzeria's</p>
                    <p class="font-display text-4xl mt-1">{{ $totalen['pizzerias'] }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $totalen['online'] }} nu online</p>
                </div>
                <div class="dash-card rise" style="--d:.06s">
                    <p class="lbl !ml-0">Abonnementen</p>
                    <p class="font-display text-4xl mt-1">{{ $financieel['actief'] }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $financieel['proef'] }} in proefperiode</p>
                </div>
                <div class="dash-card rise" style="--d:.09s">
                    <p class="lbl !ml-0">Bestellingen vandaag</p>
                    <p class="font-display text-4xl mt-1">{{ $totalen['vandaag'] }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $totalen['bestellingen'] }} in totaal</p>
                </div>
                <div class="dash-card rise" style="--d:.12s">
                    <p class="lbl !ml-0">Maandinkomsten</p>
                    <p class="font-display text-4xl mt-1">{{ $euroKort($financieel['mrr']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Uit {{ $financieel['actief'] }} abonnement{{ $financieel['actief'] === 1 ? '' : 'en' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.4fr] gap-4">
                <!-- Recente aanmeldingen -->
                <div class="dash-card rise" style="--d:.15s">
                    <p class="font-display text-xl mb-3">Recente aanmeldingen</p>
                    <div class="divide-y divide-crema-dark">
                        @forelse($pizzerias->take(6) as $p)
                            <div class="py-2.5 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl overflow-hidden border border-crema-dark bg-crema grid place-items-center shrink-0">
                                    @if($p['logo'])<img src="{{ $p['logo'] }}" alt="" class="w-full h-full object-cover">@else<i class="fa-solid fa-pizza-slice text-cacao/30" aria-hidden="true"></i>@endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('admin.pizzeria', $p['id']) }}" class="block text-sm font-semibold truncate hover:underline underline-offset-2">{{ $p['naam'] }}</a>
                                    <p class="text-xs font-semibold text-cacao/40 truncate">{{ $p['plaats'] ?: 'Plaats onbekend' }}, {{ $p['aangemeld']->locale('nl')->isoFormat('D MMM') }}</p>
                                </div>
                                {!! $aboBadge($p) !!}
                            </div>
                        @empty
                            <p class="py-3 text-sm font-semibold text-cacao/40">Nog geen aanmeldingen.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recente bestellingen -->
                <div class="dash-card rise" style="--d:.18s">
                    <p class="font-display text-xl mb-3">Recente bestellingen</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm font-bold text-left">
                            <thead>
                                <tr class="text-xs font-semibold text-cacao/40 uppercase tracking-wide border-b border-crema-dark">
                                    <th class="py-2 pr-3">Zaak</th>
                                    <th class="py-2 pr-3">Klant</th>
                                    <th class="py-2 pr-3">Status</th>
                                    <th class="py-2 pr-3 text-right">Bedrag</th>
                                    <th class="py-2 text-right">Wanneer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-crema-dark">
                                @forelse($recent as $order)
                                    <tr>
                                        <td class="py-2.5 pr-3 font-semibold truncate max-w-40">{{ $order->user->onboarding['name'] ?? $order->user->name }}</td>
                                        <td class="py-2.5 pr-3 truncate max-w-32">{{ $order->klant }}</td>
                                        <td class="py-2.5 pr-3 text-xs font-semibold text-cacao/50">{{ $statusLabels[$order->status] ?? $order->status }}</td>
                                        <td class="py-2.5 pr-3 text-right whitespace-nowrap">{{ $euro($order->totaal) }}</td>
                                        <td class="py-2.5 text-right text-xs font-semibold text-cacao/40 whitespace-nowrap">{{ $order->created_at->locale('nl')->isoFormat('D MMM HH:mm') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-center text-cacao/40">Nog geen bestellingen.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- PANEEL: Pizzeria's -->
        <section data-panel="pizzerias" class="panel">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
                <div class="flex-1">
                    <p class="font-display text-2xl mb-1">Pizzeria's</p>
                    <p class="text-sm font-semibold text-cacao/50">Alle aangesloten zaken, klik op een zaak voor het volledige dossier.</p>
                </div>
                <input id="zaakZoek" type="search" placeholder="Zoek op naam of plaats" class="inp !text-sm !py-2.5 sm:!w-72">
            </div>
            <div class="dash-card rise" style="--d:.03s">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm font-bold text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-cacao/40 uppercase tracking-wide border-b border-crema-dark">
                                <th class="py-2.5 pr-4">Zaak</th>
                                <th class="py-2.5 pr-4">Status</th>
                                <th class="py-2.5 pr-4">Abonnement</th>
                                <th class="py-2.5 pr-4">Template</th>
                                <th class="py-2.5 pr-4 text-right">Gerechten</th>
                                <th class="py-2.5 pr-4 text-right">Bestellingen</th>
                                <th class="py-2.5 pr-4 text-right">Omzet</th>
                                <th class="py-2.5 pr-4">Laatste bestelling</th>
                                <th class="py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-crema-dark">
                            @forelse($pizzerias as $p)
                                <tr class="hover:bg-crema/60 transition-colors">
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('admin.pizzeria', $p['id']) }}" class="flex items-center gap-3 group">
                                            <span class="w-10 h-10 rounded-xl overflow-hidden border border-crema-dark bg-crema grid place-items-center shrink-0">
                                                @if($p['logo'])<img src="{{ $p['logo'] }}" alt="" class="w-full h-full object-cover">@else<i class="fa-solid fa-pizza-slice text-cacao/30" aria-hidden="true"></i>@endif
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block font-semibold group-hover:underline underline-offset-2 truncate">{{ $p['naam'] }}</span>
                                                <span class="block text-xs font-semibold text-cacao/40">{{ $p['plaats'] ?: 'Plaats onbekend' }}, sinds {{ $p['aangemeld']->format('d-m-Y') }}</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $p['online'] ? 'bg-basil/10 text-basil' : 'bg-cacao/5 text-cacao/45' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $p['online'] ? 'bg-basil' : 'bg-cacao/30' }}"></span>
                                            {{ $p['online'] ? 'Online' : 'Offline' }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">{!! $aboBadge($p) !!}</td>
                                    <td class="py-3 pr-4">{{ $p['template'] }}</td>
                                    <td class="py-3 pr-4 text-right">{{ $p['gerechten'] }}</td>
                                    <td class="py-3 pr-4 text-right">{{ $p['bestellingen'] }}</td>
                                    <td class="py-3 pr-4 text-right">{{ $euro($p['omzet']) }}</td>
                                    <td class="py-3 pr-4 text-cacao/60">{{ $p['laatste'] ? \Illuminate\Support\Carbon::parse($p['laatste'])->format('d-m H:i') : '-' }}</td>
                                    <td class="py-3 text-right whitespace-nowrap">
                                        <a href="/bestellen/{{ $p['slug'] }}" target="_blank" rel="noopener" title="Bekijk bestelpagina"
                                            class="inline-grid w-8 h-8 rounded-full place-items-center border border-crema-dark text-cacao/50 hover:text-cacao hover:bg-crema transition-colors">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-6 text-center text-cacao/45">Nog geen pizzeria's aangemeld.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- PANEEL: Financieel -->
        <section data-panel="financieel" class="panel">
            <p class="font-display text-2xl mb-1">Financieel</p>
            <p class="text-sm font-semibold text-cacao/50 mb-4">Abonnementsinkomsten, conversie en de omzet die door het platform loopt.</p>

            <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3"><i class="fa-solid fa-euro-sign" aria-hidden="true"></i> Onze inkomsten</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="dash-card rise" style="--d:.03s">
                    <p class="lbl !ml-0">Maandinkomsten</p>
                    <p class="font-display text-4xl mt-1">{{ $euroKort($financieel['mrr']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $financieel['actief'] }} abonnement{{ $financieel['actief'] === 1 ? '' : 'en' }} x 24,95</p>
                </div>
                <div class="dash-card rise" style="--d:.06s">
                    <p class="lbl !ml-0">Jaarprognose</p>
                    <p class="font-display text-4xl mt-1">{{ $euroKort($financieel['jaar']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Bij de huidige abonnementen</p>
                </div>
                <div class="dash-card rise" style="--d:.09s">
                    <p class="lbl !ml-0">Conversie</p>
                    <p class="font-display text-4xl mt-1">{{ $financieel['conversie'] !== null ? $financieel['conversie'] . '%' : '-' }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Van proef naar betaald ({{ $financieel['actief'] }} van {{ $financieel['actief'] + $financieel['verlopen'] }})</p>
                </div>
                <div class="dash-card rise" style="--d:.12s">
                    <p class="lbl !ml-0">Activatiegraad</p>
                    <p class="font-display text-4xl mt-1">{{ $financieel['activatie'] !== null ? $financieel['activatie'] . '%' : '-' }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Heeft de zaak volledig afgemaakt</p>
                </div>
            </div>

            <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-8"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Omzet via het platform</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="dash-card rise" style="--d:.15s">
                    <p class="lbl !ml-0">Totale omzet</p>
                    <p class="font-display text-4xl mt-1">{{ $euroKort($financieel['gmv']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Alle bestellingen samen</p>
                </div>
                <div class="dash-card rise" style="--d:.18s">
                    <p class="lbl !ml-0">Laatste 30 dagen</p>
                    <p class="font-display text-4xl mt-1">{{ $euroKort($financieel['gmv30']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Omzet van alle zaken samen</p>
                </div>
                <div class="dash-card rise" style="--d:.21s">
                    <p class="lbl !ml-0">Gem. bestelling</p>
                    <p class="font-display text-4xl mt-1">{{ $euro($financieel['gemBestelling']) }}</p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">Over {{ $totalen['bestellingen'] }} bestellingen</p>
                </div>
                <div class="dash-card rise" style="--d:.24s">
                    <p class="lbl !ml-0">Abonnementen</p>
                    <p class="font-display text-4xl mt-1">{{ $financieel['actief'] }} <span class="text-lg text-cacao/40">/ {{ $totalen['pizzerias'] }}</span></p>
                    <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $financieel['proef'] }} in proef, {{ $financieel['verlopen'] }} verlopen</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="dash-card rise" style="--d:.27s">
                    <p class="font-display text-xl">Platformomzet per week</p>
                    <p class="text-sm font-semibold text-cacao/50 mb-4">De omzet van alle zaken samen, laatste 8 weken.</p>
                    @php $weekMax = max(1, $financieel['weekOmzet']->max('bedrag')); @endphp
                    <div class="flex items-end gap-2 h-28">
                        @foreach($financieel['weekOmzet'] as $week)
                            <div class="flex-1 flex flex-col items-center gap-1 min-w-0" title="{{ $euro($week['bedrag']) }}">
                                <div class="w-full rounded-t-lg {{ $loop->last ? 'bg-tomato' : 'bg-gold/70' }}" style="height:{{ max(4, (int) round($week['bedrag'] / $weekMax * 100)) }}%"></div>
                                <span class="text-[10px] font-semibold text-cacao/40">{{ $week['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="dash-card rise" style="--d:.3s">
                    <p class="font-display text-xl">Aanmeldingen per week</p>
                    <p class="text-sm font-semibold text-cacao/50 mb-4">Nieuwe zaken, laatste 8 weken.</p>
                    @php $aanmeldMax = max(1, $aanmeldWeek->max('aantal')); @endphp
                    <div class="flex items-end gap-2 h-28">
                        @foreach($aanmeldWeek as $week)
                            <div class="flex-1 flex flex-col items-center gap-1 min-w-0" title="{{ $week['aantal'] }} aanmeldingen">
                                <div class="w-full rounded-t-lg {{ $loop->last ? 'bg-tomato' : 'bg-basil/60' }}" style="height:{{ max(4, (int) round($week['aantal'] / $aanmeldMax * 100)) }}%"></div>
                                <span class="text-[10px] font-semibold text-cacao/40">{{ $week['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- De activatie-funnel: waar verliezen we mensen -->
            <div class="dash-card rise" style="--d:.33s">
                <p class="font-display text-xl">Activatie-funnel</p>
                <p class="text-sm font-semibold text-cacao/50 mb-4">Van aanmelding naar betalend abonnement; hier zie je waar zaken blijven steken.</p>
                @php $funnelMax = max(1, $funnel[0][1]); @endphp
                <div class="space-y-2.5">
                    @foreach($funnel as [$stap, $aantal])
                        <div class="flex items-center gap-3">
                            <span class="w-32 shrink-0 text-xs font-semibold text-cacao/50 uppercase tracking-wide">{{ $stap }}</span>
                            <div class="flex-1 h-7 bg-crema rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $loop->last ? 'bg-basil' : 'bg-tomato/80' }}" style="width:{{ max(4, (int) round($aantal / $funnelMax * 100)) }}%"></div>
                            </div>
                            <span class="w-20 shrink-0 text-right text-sm font-semibold">{{ $aantal }} <span class="text-cacao/40 text-xs">({{ (int) round($aantal / $funnelMax * 100) }}%)</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- PANEEL: Sales -->
        <section data-panel="sales" class="panel">
            <p class="font-display text-2xl mb-1">Sales</p>
            <p class="text-sm font-semibold text-cacao/50 mb-4">Wie heeft er een duwtje nodig? Bel of mail direct vanuit deze lijsten.</p>

            <!-- Proef loopt bijna af -->
            <div class="dash-card rise mb-4" style="--d:.03s; border-color:color-mix(in srgb, var(--color-gold) 55%, transparent)">
                <p class="font-display text-xl"><i class="fa-solid fa-fire" aria-hidden="true"></i> Proef loopt bijna af</p>
                <p class="text-sm font-semibold text-cacao/50 mb-3">Binnen 7 dagen beslissen deze zaken. Een kort telefoontje helpt vaak.</p>
                <div class="divide-y divide-crema-dark">
                    @forelse($bijnaAf as $p)
                        @php $dagen = max(0, (int) ceil(now()->diffInDays($p['proef_tot'], false))); @endphp
                        <div class="py-3 flex flex-col md:flex-row md:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.pizzeria', $p['id']) }}" class="font-semibold hover:underline underline-offset-2">{{ $p['naam'] }}</a>
                                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gold/15 text-cacao/70">nog {{ $dagen }} {{ $dagen === 1 ? 'dag' : 'dagen' }}</span>
                                <p class="text-xs font-semibold text-cacao/45 mt-0.5">
                                    {{ $p['persoon'] }}{{ $p['plaats'] ? ', ' . $p['plaats'] : '' }}
                                    &middot; {{ $p['setup'] ? 'zaak compleet' : 'zaak nog niet af' }}
                                    &middot; {{ $p['gerechten'] }} gerechten
                                    &middot; {{ $p['bestellingen'] }} bestellingen
                                </p>
                                @if($p['notitie'])
                                    <p class="text-xs font-semibold text-cacao/55 mt-1 bg-crema rounded-lg px-2 py-1 inline-block"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> {{ \Illuminate\Support\Str::limit($p['notitie'], 90) }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="mailto:{{ $p['email'] }}?subject={{ rawurlencode('Je proefperiode bij MijnPizzeria loopt bijna af') }}" class="btn-primary !text-sm !px-4 !py-2">Mail</a>
                                @if($p['telefoon'])
                                    <a href="tel:{{ preg_replace('/\s+/', '', $p['telefoon']) }}" class="btn-primary btn-grey !text-sm !px-4 !py-2">Bel</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-sm font-semibold text-cacao/40">Niemand loopt de komende 7 dagen uit de proef.</p>
                    @endforelse
                </div>
            </div>

            <!-- Afgehaakt: win ze terug -->
            <div class="dash-card rise" style="--d:.08s; border-color:color-mix(in srgb, var(--color-tomato) 40%, transparent)">
                <p class="font-display text-xl"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Afgehaakt, win ze terug</p>
                <p class="text-sm font-semibold text-cacao/50 mb-3">Proef verlopen zonder abonnement. Vraag wat er miste en haal ze terug aan boord.</p>
                <div class="divide-y divide-crema-dark">
                    @forelse($afgehaakt as $p)
                        @php $wegDagen = $p['proef_tot'] ? (int) floor($p['proef_tot']->diffInDays(now())) : null; @endphp
                        <div class="py-3 flex flex-col md:flex-row md:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.pizzeria', $p['id']) }}" class="font-semibold hover:underline underline-offset-2">{{ $p['naam'] }}</a>
                                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-tomato/10 text-tomato">{{ $wegDagen !== null ? $wegDagen . ' ' . ($wegDagen === 1 ? 'dag' : 'dagen') . ' geleden verlopen' : 'geen proefdatum' }}</span>
                                <p class="text-xs font-semibold text-cacao/45 mt-0.5">
                                    {{ $p['persoon'] }}{{ $p['plaats'] ? ', ' . $p['plaats'] : '' }}
                                    &middot; {{ $p['setup'] ? 'zaak compleet' : 'zaak nog niet af' }}
                                    &middot; {{ $p['gerechten'] }} gerechten
                                    &middot; {{ $p['bestellingen'] }} bestellingen{{ $p['omzet'] > 0 ? ' (' . $euro($p['omzet']) . ' omzet)' : '' }}
                                </p>
                                @if($p['notitie'])
                                    <p class="text-xs font-semibold text-cacao/55 mt-1 bg-crema rounded-lg px-2 py-1 inline-block"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> {{ \Illuminate\Support\Str::limit($p['notitie'], 90) }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="mailto:{{ $p['email'] }}?subject={{ rawurlencode('We missen je bij MijnPizzeria') }}" class="btn-primary !text-sm !px-4 !py-2">Mail</a>
                                @if($p['telefoon'])
                                    <a href="tel:{{ preg_replace('/\s+/', '', $p['telefoon']) }}" class="btn-primary btn-grey !text-sm !px-4 !py-2">Bel</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-sm font-semibold text-cacao/40">Niemand afgehaakt op dit moment.</p>
                    @endforelse
                </div>
            </div>

            <!-- Stille abonnees: betalend maar geen activiteit, dreigende opzeggers -->
            <div class="dash-card rise mt-4" style="--d:.13s">
                <p class="font-display text-xl"><i class="fa-solid fa-moon" aria-hidden="true"></i> Stille abonnees</p>
                <p class="text-sm font-semibold text-cacao/50 mb-3">Betalen wel, maar kregen al 14 dagen geen bestelling. Bel voordat ze opzeggen.</p>
                <div class="divide-y divide-crema-dark">
                    @forelse($stil as $p)
                        <div class="py-3 flex flex-col md:flex-row md:items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.pizzeria', $p['id']) }}" class="font-semibold hover:underline underline-offset-2">{{ $p['naam'] }}</a>
                                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-cacao/5 text-cacao/50">{{ $p['online'] ? 'wel online' : 'staat offline' }}</span>
                                <p class="text-xs font-semibold text-cacao/45 mt-0.5">
                                    {{ $p['persoon'] }}{{ $p['plaats'] ? ', ' . $p['plaats'] : '' }}
                                    &middot; laatste bestelling: {{ $p['laatste'] ? \Illuminate\Support\Carbon::parse($p['laatste'])->locale('nl')->isoFormat('D MMM') : 'nog nooit' }}
                                    &middot; {{ $p['gerechten'] }} gerechten
                                </p>
                                @if($p['notitie'])
                                    <p class="text-xs font-semibold text-cacao/55 mt-1 bg-crema rounded-lg px-2 py-1 inline-block"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> {{ \Illuminate\Support\Str::limit($p['notitie'], 90) }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="mailto:{{ $p['email'] }}?subject={{ rawurlencode('Hoe gaat het met je bestelpagina?') }}" class="btn-primary !text-sm !px-4 !py-2">Mail</a>
                                @if($p['telefoon'])
                                    <a href="tel:{{ preg_replace('/\s+/', '', $p['telefoon']) }}" class="btn-primary btn-grey !text-sm !px-4 !py-2">Bel</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-sm font-semibold text-cacao/40">Alle abonnees zijn actief.</p>
                    @endforelse
                </div>
            </div>

            <!-- Feedback uit de mailformulieren: de stem van de klant bij het sales-werk -->
            <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-10"><i class="fa-solid fa-comment" aria-hidden="true"></i> Feedback uit de formulieren</p>
            @php
                $feedbackSoorten = [
                    'winback' => ['label' => 'Afgehaakt', 'uitleg' => 'Waarom zaken niet doorgingen', 'kleur' => 'var(--color-tomato)', 'zacht' => 'bg-tomato/10 text-tomato', 'icoon' => 'fa-rotate-left'],
                    'proef' => ['label' => 'Twijfel in de proef', 'uitleg' => 'Waar proefzaken over twijfelen', 'kleur' => 'var(--color-gold)', 'zacht' => 'bg-gold/15 text-cacao/70', 'icoon' => 'fa-scale-balanced'],
                    'hulp' => ['label' => 'Hulpvragen', 'uitleg' => 'Vragen, ideeën en complimenten', 'kleur' => 'var(--color-basil)', 'zacht' => 'bg-basil/10 text-basil', 'icoon' => 'fa-circle-question'],
                    'verlenging' => ['label' => 'Verlengingen', 'uitleg' => 'Zelf proeftijd bijgeclaimd', 'kleur' => 'var(--color-cacao)', 'zacht' => 'bg-cacao/10 text-cacao/70', 'icoon' => 'fa-clock-rotate-left'],
                ];
            @endphp
            <!-- Tellers per soort, tegelijk de filters -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                @foreach($feedbackSoorten as $soort => $conf)
                    <button type="button" data-feedback-filter="{{ $soort }}" class="dash-card rise text-left cursor-pointer transition-transform hover:-translate-y-0.5" style="--d:{{ 0.03 + $loop->index * 0.03 }}s">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full grid place-items-center text-white text-sm" style="background:{{ $conf['kleur'] }}"><i class="fa-solid {{ $conf['icoon'] }}" aria-hidden="true"></i></span>
                            <p class="font-display text-3xl">{{ $feedbackLijst->where('context', $soort)->count() }}</p>
                        </div>
                        <p class="text-sm font-semibold mt-2">{{ $conf['label'] }}</p>
                        <p class="text-xs font-semibold text-cacao/40">{{ $conf['uitleg'] }}</p>
                    </button>
                @endforeach
            </div>

            <div class="dash-card rise" style="--d:.15s">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <p class="font-display text-xl flex-1">Inzendingen</p>
                    <button type="button" data-feedback-filter="alle" class="keuze-chip aan !py-1.5 !px-3.5 !text-xs">Alles tonen</button>
                </div>
                <div class="divide-y divide-crema-dark" id="feedbackLijst">
                    @forelse($feedbackLijst as $reactie)
                        @php $conf = $feedbackSoorten[$reactie->context] ?? ['label' => $reactie->context, 'kleur' => 'var(--color-cacao)', 'zacht' => 'bg-crema-dark text-cacao/60', 'icoon' => 'fa-comment']; @endphp
                        <div class="py-3 flex gap-3" data-feedback-context="{{ $reactie->context }}">
                            <span class="w-8 h-8 mt-0.5 shrink-0 rounded-full grid place-items-center text-white text-sm" style="background:{{ $conf['kleur'] }}"><i class="fa-solid {{ $conf['icoon'] }}" aria-hidden="true"></i></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ $reactie->user ? route('admin.pizzeria', $reactie->user) : '#' }}" class="font-semibold hover:underline underline-offset-2">{{ $reactie->user->onboarding['name'] ?? $reactie->user->name ?? 'Onbekende zaak' }}</a>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $conf['zacht'] }}">{{ $conf['label'] }}</span>
                                    <span class="text-xs font-semibold text-cacao/40">{{ $reactie->created_at->locale('nl')->isoFormat('D MMM, HH:mm') }}</span>
                                </div>
                                @if($reactie->antwoord)
                                    <p class="text-sm font-semibold mt-1">{{ $reactie->antwoord }}</p>
                                @endif
                                @if($reactie->toelichting)
                                    <p class="text-sm font-bold text-cacao/60 mt-0.5 bg-crema rounded-xl px-3 py-2 inline-block">"{{ $reactie->toelichting }}"</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-sm font-semibold text-cacao/40">Nog geen feedback binnengekomen.</p>
                    @endforelse
                </div>
            </div>
        </section>

        @include('admin.deel.blog')

        </div>
    </main>

    @include('admin.deel.bevestig-modal')

    <script>
        /* Panelen wisselen, zelfde patroon als het pizzeria-dashboard */
        const $$ = (s) => Array.from(document.querySelectorAll(s));
        $$('[data-nav]').forEach((btn) => btn.addEventListener('click', (e) => {
            e.preventDefault();   /* de links zijn voor de detailpagina; hier wisselen we client-side */
            const doel = btn.dataset.nav;
            $$('[data-nav]').forEach((b) => b.classList.toggle('actief', b.dataset.nav === doel));
            $$('[data-panel]').forEach((p) => {
                const actief = p.dataset.panel === doel;
                p.classList.remove('actief');
                if (actief) requestAnimationFrame(() => p.classList.add('actief'));
            });
            history.replaceState(null, '', doel === 'overzicht' ? '/admin' : '/admin/' + doel);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }));
        const startPaneel = location.pathname.split('/')[2];
        if (startPaneel) document.querySelector('[data-nav="' + startPaneel + '"]')?.click();

        /* Zoeken in de pizzeria-tabel */
        document.getElementById('zaakZoek')?.addEventListener('input', (e) => {
            const zoek = e.target.value.toLowerCase();
            $$('[data-panel="pizzerias"] tbody tr').forEach((rij) => {
                rij.style.display = rij.textContent.toLowerCase().includes(zoek) ? '' : 'none';
            });
        });

        /* Feedback filteren op soort via de tellers; nogmaals klikken of "Alles tonen" heft op */
        let feedbackFilter = 'alle';
        $$('[data-feedback-filter]').forEach((knop) => knop.addEventListener('click', () => {
            const soort = knop.dataset.feedbackFilter;
            feedbackFilter = (soort === 'alle' || feedbackFilter === soort) ? 'alle' : soort;
            $$('[data-feedback-filter]').forEach((k) => {
                const actief = k.dataset.feedbackFilter === feedbackFilter;
                k.style.borderColor = actief && feedbackFilter !== 'alle' ? 'var(--color-tomato)' : '';
            });
            $$('[data-feedback-context]').forEach((rij) => {
                rij.style.display = (feedbackFilter === 'alle' || rij.dataset.feedbackContext === feedbackFilter) ? '' : 'none';
            });
        }));

        /* De klok in de topbalk tikt per kwart minuut */
        const beheerKlok = document.getElementById('beheerKlok');
        if (beheerKlok) {
            const beheerTik = () => {
                beheerKlok.textContent = new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
            };
            beheerTik();
            setInterval(beheerTik, 15000);
        }
    </script>
</body>
</html>
