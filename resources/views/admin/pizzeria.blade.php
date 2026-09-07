<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ob['name'] ?? 'Pizzeria' }}, beheer | MijnPizzeria</title>

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
    $dagen = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
    $tijdVoor = function (string $dag) use ($ob) {
        if (empty($ob['days'][$dag])) return null;
        if (($ob['hoursMode'] ?? 'same') === 'perday') {
            $t = $ob['dayTimes'][$dag] ?? null;
            return $t ? $t['open'] . ' tot ' . $t['close'] : null;
        }
        return ($ob['open'] ?? '16:00') . ' tot ' . ($ob['close'] ?? '21:30');
    };
    $domein = ($ob['domainMode'] ?? 'sub') === 'own' && ! empty($ob['ownDomain'])
        ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $ob['ownDomain']))
        : $pizzeria->slug . '.mijnpizzeria.nl';
@endphp

    @include('admin.deel.nav', ['actiefPaneel' => 'pizzerias'])

    <main class="relative z-10 lg:ml-[16rem] px-5 md:px-8 pt-7 pb-28 lg:pb-10">
        <div class="max-w-6xl mx-auto">

        <a href="/admin/pizzerias" class="inline-flex items-center gap-2 text-sm font-semibold text-cacao/50 hover:text-tomato transition-colors mb-5">
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> Terug naar alle pizzeria's
        </a>

        <!-- Kop: wie is dit, hoe staan ze ervoor, en directe acties -->
        <div class="dash-card rise mb-4" style="--d:.02s">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <span class="w-16 h-16 rounded-2xl overflow-hidden border border-crema-dark bg-crema grid place-items-center shrink-0">
                    @if($ob['logo'] ?? null)<img src="{{ $ob['logo'] }}" alt="" class="w-full h-full object-cover">@else<i class="fa-solid fa-pizza-slice text-2xl text-cacao/30" aria-hidden="true"></i>@endif
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-display text-2xl leading-tight">{{ $ob['name'] ?? $pizzeria->name }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $pizzeria->is_online ? 'bg-basil/10 text-basil' : 'bg-cacao/5 text-cacao/45' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $pizzeria->is_online ? 'bg-basil' : 'bg-cacao/30' }}"></span>{{ $pizzeria->is_online ? 'Online' : 'Offline' }}
                        </span>
                        @if($abonnement === 'actief')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-basil/10 text-basil">Abonnement sinds {{ $pizzeria->abonnement_sinds?->format('d-m-Y') ?? '?' }}</span>
                        @elseif($abonnement === 'proef')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gold/15 text-cacao/70">Proef tot {{ $pizzeria->proef_tot?->format('d-m-Y') ?? '?' }}</span>
                        @else
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-tomato/10 text-tomato">Proef verlopen op {{ $pizzeria->proef_tot?->format('d-m-Y') ?? '?' }}</span>
                        @endif
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ ($ob['setup_compleet'] ?? false) ? 'bg-crema-dark text-cacao/60' : 'bg-tomato/10 text-tomato' }}">{{ ($ob['setup_compleet'] ?? false) ? 'Zaak compleet' : 'Zaak nog niet af' }}</span>
                    </div>
                    <p class="text-xs font-semibold text-cacao/40 mt-1.5">Aangemeld op {{ $pizzeria->created_at->locale('nl')->isoFormat('D MMMM YYYY') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="mailto:{{ $pizzeria->email }}" class="btn-primary !text-sm !px-4 !py-2">Mail</a>
                    @if(($ob['phone'] ?? '') !== '')
                        <a href="tel:{{ preg_replace('/\s+/', '', $ob['phone']) }}" class="btn-primary btn-grey !text-sm !px-4 !py-2">Bel</a>
                    @endif
                    <a href="/bestellen/{{ $pizzeria->slug }}" target="_blank" rel="noopener" class="btn-primary btn-grey !text-sm !px-4 !py-2">Bestelpagina</a>
                    @unless($pizzeria->abonnement_actief)
                        <form method="POST" action="{{ route('admin.verleng', $pizzeria) }}"
                              data-bevestig="De proefperiode wordt met 14 dagen verlengd en de winbackmails worden gereset."
                              data-bevestig-titel="Proef verlengen?" data-bevestig-icoon="fa-gift"
                              data-bevestig-knop="Ja, verleng 14 dagen">
                            @csrf
                            <button type="submit" class="btn-primary !text-sm !px-4 !py-2" style="background:var(--color-basil); box-shadow:0 1px 2px rgb(36 23 18 / .1)">Verleng proef 14 dagen</button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('admin.inloggenals', $pizzeria) }}"
                          data-bevestig="Je verlaat het beheer en kijkt mee als deze zaak. Log daarna opnieuw in als beheerder."
                          data-bevestig-titel="Inloggen als deze zaak?" data-bevestig-icoon="fa-eye"
                          data-bevestig-knop="Ja, log in als zaak">
                        @csrf
                        <button type="submit" class="btn-primary btn-grey !text-sm !px-4 !py-2">Inloggen als zaak</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kerncijfers -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="dash-card rise" style="--d:.04s">
                <p class="lbl !ml-0">Omzet totaal</p>
                <p class="font-display text-3xl mt-1">{{ $euro($stats['omzet']) }}</p>
                <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $stats['bestellingen'] }} bestellingen</p>
            </div>
            <div class="dash-card rise" style="--d:.07s">
                <p class="lbl !ml-0">Laatste 30 dagen</p>
                <p class="font-display text-3xl mt-1">{{ $euro($stats['omzet30']) }}</p>
                <p class="text-xs font-semibold text-cacao/40 mt-1">Waarvan {{ $euro($stats['omzet7']) }} deze week</p>
            </div>
            <div class="dash-card rise" style="--d:.1s">
                <p class="lbl !ml-0">Gem. bestelling</p>
                <p class="font-display text-3xl mt-1">{{ $euro($stats['gemBestelling']) }}</p>
                <p class="text-xs font-semibold text-cacao/40 mt-1">Laatste bestelling: {{ $stats['laatste'] ? \Illuminate\Support\Carbon::parse($stats['laatste'])->locale('nl')->isoFormat('D MMM HH:mm') : 'nog geen' }}</p>
            </div>
            <div class="dash-card rise" style="--d:.13s">
                <p class="lbl !ml-0">Klantaccounts</p>
                <p class="font-display text-3xl mt-1">{{ $stats['klanten'] }}</p>
                <p class="text-xs font-semibold text-cacao/40 mt-1">Samen {{ $stats['spaarpunten'] }} spaarpunten</p>
            </div>
        </div>

        <!-- Beheernotities: het geheugen van je sales-gesprekken -->
        <div class="dash-card rise mb-4" style="--d:.14s; border-color:color-mix(in srgb, var(--color-gold) 45%, transparent)">
            <p class="font-display text-xl mb-1"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> Beheernotities</p>
            <p class="text-sm font-semibold text-cacao/50 mb-3">Alleen zichtbaar voor beheer. Met een opvolgdatum verdwijnt de zaak tot die dag uit de sales-lijsten.</p>
            <form method="POST" action="{{ route('admin.notitie', $pizzeria) }}" class="space-y-3">
                @csrf
                <textarea name="notitie" rows="3" placeholder="Bijv. gebeld op 2 september, wil eerst de website af. Volgende week terugbellen." class="inp !text-sm w-full">{{ $pizzeria->beheer_notitie }}</textarea>
                <div class="flex flex-wrap items-center gap-3">
                    <label class="text-sm font-semibold text-cacao/50" for="opvolgen">Opvolgen vanaf</label>
                    <input id="opvolgen" type="date" name="opvolgen" value="{{ $pizzeria->opvolgen_vanaf?->format('Y-m-d') }}" class="inp !text-sm !py-2 !w-44">
                    <button type="submit" class="btn-primary !text-sm !px-5 !py-2">Opslaan</button>
                    @if($pizzeria->opvolgen_vanaf?->isFuture())
                        <span class="text-xs font-semibold text-cacao/45">Gesnoozed tot {{ $pizzeria->opvolgen_vanaf->format('d-m-Y') }}</span>
                    @endif
                </div>
            </form>
        </div>

        <!-- Gegevens en instellingen -->
        <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-8"><i class="fa-solid fa-address-card" aria-hidden="true"></i> Gegevens en instellingen</p>
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="dash-card rise" style="--d:.16s">
                <p class="font-display text-xl mb-3">Contact en bedrijf</p>
                <div class="space-y-2 text-sm font-semibold">
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Contactpersoon</span><span class="text-right truncate">{{ $pizzeria->name }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">E-mail</span><a href="mailto:{{ $pizzeria->email }}" class="text-right truncate hover:underline underline-offset-2">{{ $pizzeria->email }}</a></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Telefoon</span><span class="text-right truncate">{{ ($ob['phone'] ?? '') !== '' ? $ob['phone'] : 'Niet ingevuld' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Adres</span><span class="text-right truncate">{{ ($ob['street'] ?? '') !== '' ? $ob['street'] . ', ' . trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? '')) : 'Niet ingevuld' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">KvK</span><span class="text-right truncate">{{ ($ob['kvk'] ?? '') !== '' ? $ob['kvk'] : 'Nog niet ingevuld' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Locatie op kaart</span><span class="text-right truncate">{{ $pizzeria->lat ? round($pizzeria->lat, 4) . ', ' . round($pizzeria->lng, 4) : 'Nog niet gezet' }}</span></div>
                </div>
            </div>

            <div class="dash-card rise" style="--d:.19s">
                <p class="font-display text-xl mb-3">Abonnement</p>
                <div class="space-y-2 text-sm font-semibold">
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Status</span><span class="text-right">{{ ['actief' => 'Actief, 24,95 per maand', 'proef' => 'In proefperiode', 'verlopen' => 'Proef verlopen, geen abonnement'][$abonnement] }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Proefperiode tot</span><span class="text-right">{{ $pizzeria->proef_tot?->locale('nl')->isoFormat('D MMMM YYYY') ?? '-' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Abonnement sinds</span><span class="text-right">{{ $pizzeria->abonnement_sinds?->locale('nl')->isoFormat('D MMMM YYYY') ?? '-' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Stripe-klant</span><span class="text-right truncate text-xs">{{ $pizzeria->stripe_customer_id ?? 'Nog niet gekoppeld' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Stripe-abonnement</span><span class="text-right truncate text-xs">{{ $pizzeria->stripe_subscription_id ?? 'Nog niet gekoppeld' }}</span></div>
                </div>
            </div>

            <div class="dash-card rise" style="--d:.22s">
                <p class="font-display text-xl mb-3">Zaak en stijl</p>
                <div class="space-y-2 text-sm font-semibold">
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Bestel-adres</span><a href="/bestellen/{{ $pizzeria->slug }}" target="_blank" rel="noopener" class="text-right truncate hover:underline underline-offset-2">{{ $domein }}</a></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Template</span><span class="text-right">{{ $template }}</span></div>
                    <div class="flex justify-between gap-3 items-center"><span class="text-cacao/45 shrink-0">Kleurstijl</span><span class="text-right inline-flex items-center gap-2">{{ $kleurNaam ?? 'Eigen kleur' }}<span class="inline-block w-4 h-4 rounded-full border border-crema-dark" style="background:{{ $ob['color'] ?? '#B04A3F' }}"></span></span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Spaarpunten</span><span class="text-right">{{ ($ob['spaarpunten'] ?? false) ? 'Aan' : 'Uit' }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-cacao/45 shrink-0">Logo</span><span class="text-right">{{ ($ob['logo'] ?? null) ? 'Geüpload' : 'Nog geen logo' }}</span></div>
                </div>
            </div>

            <div class="dash-card rise" style="--d:.25s">
                <p class="font-display text-xl mb-3">Openingstijden</p>
                <div class="space-y-1.5 text-sm font-semibold">
                    @if(empty($ob['days']))
                        <p class="text-cacao/45">Nog niet ingesteld.</p>
                    @else
                        @foreach($dagen as $dag)
                            <div class="flex justify-between gap-3">
                                <span class="text-cacao/45 uppercase text-xs pt-0.5">{{ $dag }}</span>
                                <span class="{{ $tijdVoor($dag) ? '' : 'text-cacao/35' }}">{{ $tijdVoor($dag) ?? 'Gesloten' }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
                @if(! empty($pizzeria->bezorgkosten))
                    <p class="font-display text-base mt-4 mb-1.5">Bezorgkosten</p>
                    <div class="space-y-1 text-xs font-semibold text-cacao/60">
                        @foreach($pizzeria->bezorgkosten as $staffel)
                            <div class="flex justify-between gap-3">
                                <span>Tot {{ str_replace('.', ',', (string) $staffel['km']) }} km</span>
                                <span>{{ $euro($staffel['kosten']) }}{{ ! empty($staffel['min']) ? ', vanaf ' . $euro($staffel['min']) : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($feedback->isNotEmpty())
        <!-- Binnengekomen feedback uit de mailformulieren -->
        <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-8"><i class="fa-solid fa-comment" aria-hidden="true"></i> Feedback</p>
        <div class="dash-card rise mb-4" style="--d:.27s">
            <div class="divide-y divide-crema-dark">
                @foreach($feedback as $reactie)
                    <div class="py-2.5">
                        <p class="text-sm font-semibold">{{ $reactie->antwoord ?? 'Geen keuze gemaakt' }}
                            <span class="text-xs text-cacao/40">via {{ $reactie->context }}, {{ $reactie->created_at->locale('nl')->isoFormat('D MMM YYYY') }}</span>
                        </p>
                        @if($reactie->toelichting)
                            <p class="text-sm font-bold text-cacao/60 mt-0.5">"{{ $reactie->toelichting }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Menukaart -->
        <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-8"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i> Menukaart ({{ $stats['gerechten'] }} gerechten)</p>
        <div class="dash-card rise mb-4" style="--d:.28s">
            @forelse($menu as $categorie => $items)
                <p class="font-display text-lg {{ $loop->first ? '' : 'mt-4' }} mb-1.5">{{ $categorie }}</p>
                <div class="divide-y divide-crema-dark">
                    @foreach($items as $item)
                        <div class="py-1.5 flex justify-between gap-3 text-sm font-semibold">
                            <span class="truncate {{ $item->actief ? '' : 'text-cacao/35 line-through' }}">{{ $item->naam }}</span>
                            <span class="shrink-0 text-cacao/60">{{ $euro($item->prijs) }}</span>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="text-sm font-semibold text-cacao/45">Nog geen menukaart gevuld.</p>
            @endforelse
        </div>

        <!-- Bestellingen -->
        <p class="text-[.8rem] font-semibold uppercase tracking-wider text-cacao/35 mb-3 mt-8"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Laatste bestellingen</p>
        <div class="dash-card rise" style="--d:.31s">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-bold text-left">
                    <thead>
                        <tr class="text-xs font-semibold text-cacao/40 uppercase tracking-wide border-b border-crema-dark">
                            <th class="py-2.5 pr-4">Nummer</th>
                            <th class="py-2.5 pr-4">Klant</th>
                            <th class="py-2.5 pr-4">Type</th>
                            <th class="py-2.5 pr-4">Status</th>
                            <th class="py-2.5 pr-4 text-right">Bedrag</th>
                            <th class="py-2.5 text-right">Wanneer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-crema-dark">
                        @forelse($orders as $order)
                            <tr>
                                <td class="py-2.5 pr-4 font-semibold">#{{ $order->nummer }}</td>
                                <td class="py-2.5 pr-4 truncate max-w-40">{{ $order->klant }}</td>
                                <td class="py-2.5 pr-4 text-xs font-semibold text-cacao/50">{{ $order->type === 'bezorgen' ? 'Bezorgen' : 'Afhalen' }}</td>
                                <td class="py-2.5 pr-4 text-xs font-semibold text-cacao/50">{{ $statusLabels[$order->status] ?? $order->status }}</td>
                                <td class="py-2.5 pr-4 text-right whitespace-nowrap">{{ $euro($order->totaal) }}</td>
                                <td class="py-2.5 text-right text-xs font-semibold text-cacao/40 whitespace-nowrap">{{ $order->created_at->locale('nl')->isoFormat('D MMM HH:mm') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-cacao/40">Nog geen bestellingen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        </div>
    </main>

    @include('admin.deel.bevestig-modal')
</body>
</html>
