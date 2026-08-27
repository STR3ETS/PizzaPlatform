<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beheer 🛠️</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: #FFF6E8;
            --color-crema-dark: #F9EBD2;
            --color-tomato: #E63946;
            --color-tomato-dark: #B71F2E;
            --color-basil: #2F8F46;
            --color-gold: #F5B301;
            --color-cacao: #38221A;
            --font-display: "Lilita One", cursive;
            --font-body: "Nunito", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen antialiased">
@php
    $euro = fn ($centen) => '€ ' . number_format($centen / 100, 2, ',', '.');
@endphp

    <div class="max-w-6xl mx-auto px-5 py-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl">Beheer 🛠️</h1>
                <p class="text-sm font-extrabold text-cacao/50">Alle aangesloten pizzeria's op een rij.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-full bg-white border-2 border-crema-dark text-sm font-extrabold text-cacao/60 hover:text-tomato hover:border-tomato/40 transition-colors cursor-pointer">Uitloggen</button>
            </form>
        </div>

        {{-- Platform-totalen --}}
        <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['🏪', 'Pizzeria\'s', $totalen['pizzerias']],
                ['🧾', 'Bestellingen', $totalen['bestellingen']],
                ['💶', 'Omzet totaal', $euro($totalen['omzet'])],
                ['📬', 'Bestellingen vandaag', $totalen['vandaag']],
            ] as [$icoon, $label, $waarde])
                <div class="bg-white rounded-3xl border-2 border-crema-dark p-5">
                    <p class="text-2xl">{{ $icoon }}</p>
                    <p class="mt-2 font-display text-2xl">{{ $waarde }}</p>
                    <p class="text-xs font-extrabold text-cacao/45">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Alle pizzeria's --}}
        <div class="mt-6 bg-white rounded-3xl border-2 border-crema-dark p-6">
            <h2 class="font-display text-xl">Pizzeria's</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm font-bold text-left">
                    <thead>
                        <tr class="text-xs font-extrabold text-cacao/40 uppercase tracking-wide border-b-2 border-crema-dark">
                            <th class="py-2.5 pr-4">Zaak</th>
                            <th class="py-2.5 pr-4">Status</th>
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
                                        <span class="w-10 h-10 rounded-xl overflow-hidden border-2 border-crema-dark bg-crema grid place-items-center shrink-0">
                                            @if($p['logo'])
                                                <img src="{{ $p['logo'] }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span>🍕</span>
                                            @endif
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block font-extrabold group-hover:underline underline-offset-2 truncate">{{ $p['naam'] }}</span>
                                            <span class="block text-xs font-extrabold text-cacao/40">{{ $p['plaats'] ?: 'Plaats onbekend' }} · sinds {{ $p['aangemeld']->format('d-m-Y') }}</span>
                                        </span>
                                    </a>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold {{ $p['online'] ? 'bg-basil/10 text-basil' : 'bg-cacao/5 text-cacao/45' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $p['online'] ? 'bg-basil' : 'bg-cacao/30' }}"></span>
                                        {{ $p['online'] ? 'Online' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">{{ $p['template'] }}</td>
                                <td class="py-3 pr-4 text-right">{{ $p['gerechten'] }}</td>
                                <td class="py-3 pr-4 text-right">{{ $p['bestellingen'] }}</td>
                                <td class="py-3 pr-4 text-right">{{ $euro($p['omzet']) }}</td>
                                <td class="py-3 pr-4 text-cacao/60">{{ $p['laatste'] ? \Illuminate\Support\Carbon::parse($p['laatste'])->format('d-m H:i') : '-' }}</td>
                                <td class="py-3 text-right whitespace-nowrap">
                                    <a href="/bestellen/{{ $p['slug'] }}" target="_blank" rel="noopener" title="Bekijk bestelpagina"
                                        class="inline-grid w-8 h-8 rounded-full place-items-center border-2 border-crema-dark text-cacao/50 hover:text-cacao hover:bg-crema transition-colors">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-cacao/45">Nog geen pizzeria's aangemeld.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recente bestellingen over het hele platform --}}
        <div class="mt-6 bg-white rounded-3xl border-2 border-crema-dark p-6">
            <h2 class="font-display text-xl">Recente bestellingen</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm font-bold text-left">
                    <thead>
                        <tr class="text-xs font-extrabold text-cacao/40 uppercase tracking-wide border-b-2 border-crema-dark">
                            <th class="py-2.5 pr-4">Nummer</th>
                            <th class="py-2.5 pr-4">Zaak</th>
                            <th class="py-2.5 pr-4">Klant</th>
                            <th class="py-2.5 pr-4">Type</th>
                            <th class="py-2.5 pr-4 text-right">Totaal</th>
                            <th class="py-2.5 pr-4">Status</th>
                            <th class="py-2.5">Geplaatst</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-crema-dark">
                        @forelse($recent as $order)
                            <tr>
                                <td class="py-3 pr-4">#{{ $order->nummer }}</td>
                                <td class="py-3 pr-4">{{ ($order->user->onboarding ?? [])['name'] ?? $order->user->name }}</td>
                                <td class="py-3 pr-4">{{ $order->klant }}</td>
                                <td class="py-3 pr-4 capitalize">{{ $order->type }}</td>
                                <td class="py-3 pr-4 text-right">{{ $euro($order->totaal) }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold {{ $order->status === 'bezorgd' ? 'bg-basil/10 text-basil' : 'bg-gold/15 text-cacao/70' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                                </td>
                                <td class="py-3 text-cacao/60">{{ $order->created_at->format('d-m H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-cacao/45">Nog geen bestellingen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
