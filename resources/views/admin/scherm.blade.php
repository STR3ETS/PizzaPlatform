<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Het scherm hangt zonder toetsenbord aan de muur: elke 2 minuten verse data --}}
    <meta http-equiv="refresh" content="120">
    <title>MijnPizzeria zaakscherm</title>

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
</head>
<body class="bg-crema font-body text-cacao w-screen h-screen overflow-hidden antialiased">
@php
    $euro = fn ($centen) => '€ ' . number_format($centen / 100, 2, ',', '.');
    $euroKort = fn ($centen) => '€ ' . number_format($centen / 100, 0, ',', '.');
@endphp

    <main class="w-screen h-screen flex flex-col p-[1.1vw] gap-[.9vw]">

        <!-- Kop: merk, live-indicator en klok -->
        <header class="flex items-center gap-[1vw] shrink-0">
            <span class="text-[1.9vw]"><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i></span>
            <span class="font-display text-[1.7vw]">MijnPizzeria <span class="text-cacao/40">beheer</span></span>
            <span class="inline-flex items-center gap-[.4vw] bg-white rounded-full border border-crema-dark px-[.8vw] py-[.25vw] text-[.8vw] font-semibold text-basil">
                <span class="w-[.55vw] h-[.55vw] rounded-full bg-basil animate-pulse"></span> Live
            </span>
            <div class="flex-1"></div>
            <span id="klokDatum" class="text-[1vw] font-semibold text-cacao/45"></span>
            <span id="klok" class="font-display text-[1.9vw] tabular-nums"></span>
        </header>

        <!-- Kerncijfers -->
        <div class="grid grid-cols-7 gap-[.9vw] shrink-0">
            @foreach([
                ['Zaken', $cijfers['zaken'], $cijfers['online'] . ' online'],
                ['Abonnementen', $cijfers['abonnementen'], 'betalend'],
                ['In proef', $cijfers['proef'], 'aan het proberen'],
                ['Maandinkomsten', $euroKort($cijfers['mrr']), 'uit abonnementen'],
                ['Bestellingen vandaag', $cijfers['vandaag'], 'over alle zaken'],
                ['Omzet vandaag', $euroKort($cijfers['omzetVandaag']), 'over alle zaken'],
                ['Nu online', $cijfers['online'], 'nemen bestellingen aan'],
            ] as [$label, $waarde, $sub])
                <div class="bg-white rounded-[1vw] border-[.2vw] border-crema-dark px-[1vw] py-[.8vw] overflow-hidden">
                    <p class="text-[.7vw] font-semibold uppercase tracking-wide text-cacao/45 truncate">{{ $label }}</p>
                    <p class="font-display text-[2vw] leading-tight truncate">{{ $waarde }}</p>
                    <p class="text-[.7vw] font-semibold text-cacao/35 truncate">{{ $sub }}</p>
                </div>
            @endforeach
        </div>

        <!-- Drie kolommen die samen de rest van het scherm vullen -->
        <div class="flex-1 min-h-0 grid grid-cols-3 gap-[.9vw]">

            <!-- Kolom 1: sales-urgentie -->
            <div class="flex flex-col gap-[.9vw] min-h-0">
                <div class="flex-1 min-h-0 bg-white rounded-[1vw] border-[.2vw] px-[1.1vw] py-[.9vw] overflow-hidden" style="border-color:color-mix(in srgb, var(--color-gold) 55%, transparent)">
                    <p class="font-display text-[1.15vw] mb-[.5vw]"><i class="fa-solid fa-fire" aria-hidden="true"></i> Proef loopt bijna af</p>
                    @forelse($bijnaAf as $z)
                        @php $dagen = max(0, (int) ceil(now()->diffInDays($z['proef_tot'], false))); @endphp
                        <div class="flex items-center gap-[.6vw] py-[.35vw] border-b border-crema-dark last:border-0">
                            <span class="flex-1 min-w-0 truncate text-[.95vw] font-semibold">{{ $z['naam'] }}<span class="text-cacao/40 font-bold">{{ $z['plaats'] ? ', ' . $z['plaats'] : '' }}</span></span>
                            <span class="shrink-0 text-[.8vw] font-semibold px-[.6vw] py-[.15vw] rounded-full bg-gold/20">{{ $dagen }}d</span>
                            @if($z['telefoon'])<span class="shrink-0 text-[.8vw] font-semibold text-cacao/50">{{ $z['telefoon'] }}</span>@endif
                        </div>
                    @empty
                        <p class="text-[.9vw] font-semibold text-cacao/40">Niemand deze week</p>
                    @endforelse
                </div>
                <div class="flex-1 min-h-0 bg-white rounded-[1vw] border-[.2vw] px-[1.1vw] py-[.9vw] overflow-hidden" style="border-color:color-mix(in srgb, var(--color-tomato) 45%, transparent)">
                    <p class="font-display text-[1.15vw] mb-[.5vw]"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Afgehaakt, terugwinnen</p>
                    @forelse($afgehaakt as $z)
                        <div class="flex items-center gap-[.6vw] py-[.35vw] border-b border-crema-dark last:border-0">
                            <span class="flex-1 min-w-0 truncate text-[.95vw] font-semibold">{{ $z['naam'] }}<span class="text-cacao/40 font-bold">{{ $z['plaats'] ? ', ' . $z['plaats'] : '' }}</span></span>
                            <span class="shrink-0 text-[.8vw] font-semibold px-[.6vw] py-[.15vw] rounded-full bg-tomato/10 text-tomato">{{ $z['proef_tot'] ? (int) floor($z['proef_tot']->diffInDays(now())) . 'd weg' : '?' }}</span>
                            @if($z['telefoon'])<span class="shrink-0 text-[.8vw] font-semibold text-cacao/50">{{ $z['telefoon'] }}</span>@endif
                        </div>
                    @empty
                        <p class="text-[.9vw] font-semibold text-cacao/40">Geen afgehaakte zaken</p>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 2: signalen en de stem van de klant -->
            <div class="flex flex-col gap-[.9vw] min-h-0">
                <div class="min-h-0 bg-white rounded-[1vw] border-[.2vw] border-crema-dark px-[1.1vw] py-[.9vw] overflow-hidden">
                    <p class="font-display text-[1.15vw] mb-[.5vw]"><i class="fa-solid fa-moon" aria-hidden="true"></i> Stille abonnees</p>
                    @forelse($stil as $z)
                        <div class="flex items-center gap-[.6vw] py-[.35vw] border-b border-crema-dark last:border-0">
                            <span class="flex-1 min-w-0 truncate text-[.95vw] font-semibold">{{ $z['naam'] }}</span>
                            <span class="shrink-0 text-[.8vw] font-semibold text-cacao/45">laatste order: {{ $z['laatste'] ? \Illuminate\Support\Carbon::parse($z['laatste'])->locale('nl')->isoFormat('D MMM') : 'nooit' }}</span>
                        </div>
                    @empty
                        <p class="text-[.9vw] font-semibold text-cacao/40">Alle abonnees actief</p>
                    @endforelse
                </div>
                <div class="flex-1 min-h-0 bg-white rounded-[1vw] border-[.2vw] border-crema-dark px-[1.1vw] py-[.9vw] overflow-hidden">
                    <p class="font-display text-[1.15vw] mb-[.5vw]"><i class="fa-solid fa-comment" aria-hidden="true"></i> Laatste feedback</p>
                    @forelse($feedback as $reactie)
                        <div class="py-[.35vw] border-b border-crema-dark last:border-0">
                            <p class="text-[.85vw] font-semibold truncate">{{ $reactie->user->onboarding['name'] ?? $reactie->user->name ?? '?' }} <span class="text-cacao/40">({{ $reactie->context }})</span></p>
                            <p class="text-[.85vw] font-bold text-cacao/60 truncate">{{ $reactie->toelichting ?: $reactie->antwoord }}</p>
                        </div>
                    @empty
                        <p class="text-[.9vw] font-semibold text-cacao/40">Nog geen feedback</p>
                    @endforelse
                </div>
            </div>

            <!-- Kolom 3: de kassa -->
            <div class="flex flex-col gap-[.9vw] min-h-0">
                <div class="flex-1 min-h-0 bg-white rounded-[1vw] border-[.2vw] border-crema-dark px-[1.1vw] py-[.9vw] overflow-hidden">
                    <p class="font-display text-[1.15vw] mb-[.5vw]"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Laatste bestellingen</p>
                    @forelse($recent as $order)
                        <div class="flex items-center gap-[.6vw] py-[.35vw] border-b border-crema-dark last:border-0">
                            <span class="flex-1 min-w-0 truncate text-[.95vw] font-semibold">{{ $order->user->onboarding['name'] ?? $order->user->name }}</span>
                            <span class="shrink-0 text-[.85vw] font-semibold">{{ $euro($order->totaal) }}</span>
                            <span class="shrink-0 text-[.75vw] font-semibold text-cacao/40 tabular-nums">{{ $order->created_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-[.9vw] font-semibold text-cacao/40">Nog geen bestellingen vandaag</p>
                    @endforelse
                </div>
                <div class="min-h-0 bg-white rounded-[1vw] border-[.2vw] border-crema-dark px-[1.1vw] py-[.9vw] overflow-hidden">
                    <p class="font-display text-[1.15vw] mb-[.4vw]"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i> Omzet per week</p>
                    @php $weekMax = max(1, $weekOmzet->max('bedrag')); @endphp
                    <div class="flex items-end gap-[.4vw] h-[8vh]">
                        @foreach($weekOmzet as $week)
                            <div class="flex-1 flex flex-col items-center gap-[.2vw] min-w-0">
                                <div class="w-full rounded-t-[.4vw] {{ $loop->last ? 'bg-tomato' : 'bg-gold/70' }}" style="height:{{ max(5, (int) round($week['bedrag'] / $weekMax * 100)) }}%"></div>
                                <span class="text-[.65vw] font-semibold text-cacao/40">{{ $week['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        /* Klok rechtsboven; de data ververst via de meta-refresh */
        const tik = () => {
            const nu = new Date();
            document.getElementById('klok').textContent = nu.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('klokDatum').textContent = nu.toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' });
        };
        tik();
        setInterval(tik, 10000);
    </script>
</body>
</html>
