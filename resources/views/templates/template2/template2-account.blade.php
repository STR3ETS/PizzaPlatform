<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mijn account bij {{ $naam }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
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
    </style>
</head>
<body class="bg-wit-warm min-h-screen">
    {{-- Geen balk: een rustige terug-link met de paginatitel in serif --}}
    <div class="max-w-5xl mx-auto px-6 pt-10">
        <a href="{{ $basis }}" class="inline-flex items-center gap-2 text-sm font-semibold text-secundair/60 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors">
            <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i> Terug naar het menu
        </a>
        <h1 class="text-4xl text-secundair mt-4">Mijn account</h1>
    </div>

    <main class="max-w-5xl mx-auto px-6 py-8 grid lg:grid-cols-[1fr_22rem] gap-6 items-start">
        {{-- Vorige bestellingen --}}
        <section class="bg-white rounded-sm border border-black/10 p-6 min-w-0">
            <h2 class="text-lg font-extrabold text-secundair">Je bestellingen</h2>
            @if($bestellingen->isEmpty())
                <div class="py-12 text-center">
                    <i class="fa-solid fa-receipt text-3xl text-secundair/25" aria-hidden="true"></i>
                    <p class="mt-3 text-sm font-bold text-secundair">Nog geen bestellingen</p>
                    <p class="mt-1 text-sm font-semibold text-secundair/50">Alles wat je bestelt vind je hier terug.</p>
                    <a href="{{ $basis }}" class="mt-5 inline-block px-6 py-3 rounded-sm bg-primair font-bold text-white hover:bg-primair-donker transition-colors">Naar het menu</a>
                </div>
            @else
                <div class="mt-2 divide-y divide-black/5">
                    @foreach($bestellingen as $bestelling)
                        <div class="py-4 flex items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <p class="text-sm font-bold text-secundair">Bestelling #{{ $bestelling['nummer'] }}</p>
                                    <span class="px-2.5 py-0.5 rounded-sm text-[11px] font-bold {{ $bestelling['afgerond'] ? 'bg-secundair/5 text-secundair/55' : 'bg-primair/10 text-primair-donker' }}">{{ $bestelling['status'] }}</span>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-secundair/50">{{ $bestelling['datum'] }}</p>
                                <p class="mt-1.5 text-sm font-semibold text-secundair/70 truncate">{{ $bestelling['items'] }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-secundair">&euro; {{ number_format($bestelling['totaal'] / 100, 2, ',', '.') }}</p>
                                @if($bestelling['token'])
                                    <a href="{{ $basis }}/bestelling/{{ $bestelling['token'] }}" class="mt-1 inline-block text-xs font-bold text-secundair/50 underline underline-offset-2 hover:text-secundair transition-colors">Bekijk</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            {{-- Gegevens --}}
            <section class="bg-white rounded-sm border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Je gegevens</h2>
                <dl class="mt-3 space-y-2.5 text-sm">
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-secundair/40">Naam</dt>
                        <dd class="font-bold text-secundair">{{ $klant->naam }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-secundair/40">E-mailadres</dt>
                        <dd class="font-bold text-secundair break-all">{{ $klant->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-secundair/40">Telefoon</dt>
                        <dd class="font-bold text-secundair">{{ $klant->telefoon ?: 'Nog niet ingevuld' }}</dd>
                    </div>
                </dl>
                <form method="POST" action="{{ $basis }}/klant-uitloggen" class="mt-5">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-secundair/50 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors cursor-pointer">Uitloggen</button>
                </form>
            </section>

            @if($spaarpunten)
            {{-- Spaarpunten --}}
            <section class="rounded-sm bg-secundair p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 rounded-sm bg-primair grid place-items-center">
                        <i class="fa-solid fa-star text-white text-lg" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-white leading-none">{{ $klant->punten }}</p>
                        <p class="mt-1 text-sm font-bold text-white/60">spaarpunten</p>
                    </div>
                </div>
                <p class="mt-4 text-sm font-semibold text-white/60">Je spaart automatisch bij elke bestelling: 1 punt per euro. Elke 100 punten zijn € 5 korting bij het afrekenen.</p>
            </section>
            @endif

            {{-- Adressen --}}
            <section class="bg-white rounded-sm border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Je adressen</h2>
                @if(empty($klant->adressen))
                    <p class="mt-3 text-sm font-semibold text-secundair/50">Nog geen adressen bewaard. Bij het afrekenen kun je ze toevoegen.</p>
                @else
                    <ul class="mt-3 space-y-2.5">
                        @foreach($klant->adressen as $adres)
                            <li class="flex items-start gap-3 text-sm font-semibold text-secundair/75">
                                <i class="fa-solid fa-location-dot text-primair mt-0.5" aria-hidden="true"></i>
                                <span class="min-w-0">{{ $adres['label'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </main>
</body>
</html>
