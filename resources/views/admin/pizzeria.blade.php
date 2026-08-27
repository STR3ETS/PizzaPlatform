<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ob['name'] ?? $pizzeria->name }} · Beheer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --color-crema: #FFF6E8;
            --color-crema-dark: #F9EBD2;
            --color-tomato: #E63946;
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
    $dagen = ['ma' => 'Maandag', 'di' => 'Dinsdag', 'wo' => 'Woensdag', 'do' => 'Donderdag', 'vr' => 'Vrijdag', 'za' => 'Zaterdag', 'zo' => 'Zondag'];
@endphp

    <div class="max-w-6xl mx-auto px-5 py-8">
        <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 text-sm font-extrabold text-cacao/50 hover:text-cacao transition-colors">
            <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i> Terug naar overzicht
        </a>

        {{-- Kop met zaakgegevens --}}
        <div class="mt-4 bg-white rounded-3xl border-2 border-crema-dark p-6 flex flex-wrap items-center gap-4">
            <span class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-crema-dark bg-crema grid place-items-center shrink-0">
                @if($ob['logo'] ?? null)
                    <img src="{{ $ob['logo'] }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl">🍕</span>
                @endif
            </span>
            <div class="flex-1 min-w-0">
                <h1 class="font-display text-2xl truncate">{{ $ob['name'] ?? $pizzeria->name }}</h1>
                <p class="text-sm font-extrabold text-cacao/50">
                    {{ $ob['city'] ?? 'Plaats onbekend' }} · sinds {{ $pizzeria->created_at->format('d-m-Y') }} ·
                    <span class="{{ $pizzeria->is_online ? 'text-basil' : 'text-cacao/45' }}">{{ $pizzeria->is_online ? 'Online' : 'Offline' }}</span>
                </p>
            </div>
            <a href="/bestellen/{{ $pizzeria->slug }}" target="_blank" rel="noopener"
                class="px-4 py-2 rounded-full bg-tomato text-white text-sm font-extrabold hover:opacity-90 transition-opacity">
                Bekijk bestelpagina <i class="fa-solid fa-arrow-up-right-from-square text-xs ml-1" aria-hidden="true"></i>
            </a>
        </div>

        {{-- Cijfers --}}
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['🍽️', 'Gerechten', $stats['gerechten']],
                ['🧾', 'Bestellingen', $stats['bestellingen']],
                ['💶', 'Omzet totaal', $euro($stats['omzet'])],
                ['📅', 'Omzet 7 dagen', $euro($stats['omzet7'])],
            ] as [$icoon, $label, $waarde])
                <div class="bg-white rounded-3xl border-2 border-crema-dark p-5">
                    <p class="text-2xl">{{ $icoon }}</p>
                    <p class="mt-2 font-display text-2xl">{{ $waarde }}</p>
                    <p class="text-xs font-extrabold text-cacao/45">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 grid lg:grid-cols-3 gap-4">
            {{-- Contact en bedrijf --}}
            <div class="bg-white rounded-3xl border-2 border-crema-dark p-6">
                <h2 class="font-display text-lg">Contact</h2>
                <dl class="mt-3 space-y-2 text-sm font-bold">
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">Contactpersoon</dt><dd>{{ $ob['person'] ?? '-' }}</dd></div>
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">E-mail</dt><dd class="break-all">{{ $pizzeria->email }}</dd></div>
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">Telefoon</dt><dd>{{ $ob['phone'] ?? '-' }}</dd></div>
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">Adres</dt><dd>{{ trim(($ob['street'] ?? '') . ', ' . ($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? ''), ', ') ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">KVK</dt><dd>{{ $ob['kvk'] ?? '-' }}</dd></div>
                </dl>
            </div>

            {{-- Stijl en bezorging --}}
            <div class="bg-white rounded-3xl border-2 border-crema-dark p-6">
                <h2 class="font-display text-lg">Stijl en bezorging</h2>
                <dl class="mt-3 space-y-2 text-sm font-bold">
                    <div><dt class="text-xs font-extrabold text-cacao/40 uppercase">Template</dt><dd>{{ $template }}</dd></div>
                    <div>
                        <dt class="text-xs font-extrabold text-cacao/40 uppercase">Kleurstijl</dt>
                        <dd class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-cacao/15" style="background: {{ $ob['color'] ?? '#E63946' }}"></span>
                            {{ $kleurNaam ?? ($ob['color'] ?? '-') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-extrabold text-cacao/40 uppercase">Bezorgkosten</dt>
                        <dd>
                            @forelse(collect($pizzeria->bezorgkosten ?? [])->sortBy('km') as $tier)
                                <span class="block">tot {{ $tier['km'] }} km: {{ $euro($tier['kosten'] ?? 0) }} (min. {{ $euro($tier['minimum'] ?? 0) }})</span>
                            @empty
                                <span>-</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Openingstijden --}}
            <div class="bg-white rounded-3xl border-2 border-crema-dark p-6">
                <h2 class="font-display text-lg">Openingstijden</h2>
                <dl class="mt-3 space-y-1.5 text-sm font-bold">
                    @foreach($dagen as $kort => $vol)
                        @php
                            $open = ($ob['days'] ?? [])[$kort] ?? false;
                            $tijd = ($ob['hoursMode'] ?? '') === 'perday' && isset($ob['dayTimes'][$kort])
                                ? $ob['dayTimes'][$kort]
                                : ['open' => $ob['open'] ?? '16:00', 'close' => $ob['close'] ?? '21:30'];
                        @endphp
                        <div class="flex items-center justify-between gap-3">
                            <dt class="{{ $open ? '' : 'text-cacao/40' }}">{{ $vol }}</dt>
                            <dd class="{{ $open ? '' : 'text-cacao/40' }}">{{ $open ? ($tijd['open'] . ' - ' . $tijd['close']) : 'Gesloten' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        {{-- Bestellingen --}}
        <div class="mt-4 bg-white rounded-3xl border-2 border-crema-dark p-6">
            <h2 class="font-display text-lg">Laatste bestellingen</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm font-bold text-left">
                    <thead>
                        <tr class="text-xs font-extrabold text-cacao/40 uppercase tracking-wide border-b-2 border-crema-dark">
                            <th class="py-2.5 pr-4">Nummer</th>
                            <th class="py-2.5 pr-4">Klant</th>
                            <th class="py-2.5 pr-4">Type</th>
                            <th class="py-2.5 pr-4">Inhoud</th>
                            <th class="py-2.5 pr-4 text-right">Totaal</th>
                            <th class="py-2.5 pr-4">Status</th>
                            <th class="py-2.5">Geplaatst</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-crema-dark">
                        @forelse($orders as $order)
                            <tr>
                                <td class="py-3 pr-4">#{{ $order->nummer }}</td>
                                <td class="py-3 pr-4">{{ $order->klant }}</td>
                                <td class="py-3 pr-4 capitalize">{{ $order->type }}</td>
                                <td class="py-3 pr-4 max-w-64">
                                    <span class="block truncate text-cacao/60">{{ collect($order->items)->map(fn ($i) => ($i['aantal'] ?? 1) . 'x ' . ($i['naam'] ?? '?'))->join(', ') }}</span>
                                </td>
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
