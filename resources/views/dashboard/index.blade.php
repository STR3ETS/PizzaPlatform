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
<body class="bg-crema font-body text-cacao min-h-screen antialiased overflow-x-hidden">

    <!-- Zwevende deco-emoji's -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden select-none" aria-hidden="true">
        <span class="float-emoji" style="top:14%; left:30%; --delay:0s;   --size:2.2rem;">🍕</span>
        <span class="float-emoji" style="top:75%; left:35%; --delay:1.4s; --size:1.8rem;">🍅</span>
        <span class="float-emoji" style="top:20%; left:90%; --delay:.7s;  --size:2rem;">🧀</span>
        <span class="float-emoji" style="top:70%; left:88%; --delay:2.2s; --size:2rem;">🌿</span>
    </div>

    <!-- Zwevende navigatie-rail (desktop) -->
    <aside class="dash-rail">
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
            <button type="button" data-nav="dozen" class="rail-item"><span class="r-ico">📦</span> Dozen <span class="n-soon">Binnenkort</span></button>
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
        <button type="button" data-nav="overzicht" class="tab-item actief"><span class="t-ico" style="position:relative">🏠<span id="tabDot" class="status-dot" style="position:absolute; top:-2px; right:-8px; width:.55rem; height:.55rem;"></span></span>Home</button>
        <button type="button" data-nav="bestellingen" class="tab-item"><span class="t-ico">🧾</span>Orders</button>
        <button type="button" data-nav="menukaart" class="tab-item"><span class="t-ico">📋</span>Menu</button>
        <button type="button" data-nav="bestelpagina" class="tab-item"><span class="t-ico">🎨</span>Pagina</button>
        <button type="button" data-nav="instellingen" class="tab-item"><span class="t-ico">⚙️</span>Meer</button>
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
            <!-- Online-status -->
            <div class="dash-card rise mb-5 flex flex-col md:flex-row md:items-center gap-4" style="--d:.03s">
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
                $vandaagBestellingen = auth()->user()->orders()->whereDate('created_at', today())->get();
            @endphp

            <!-- Waarschuwing als status en openingstijden niet kloppen -->
            <div id="statusWaarschuwing" class="hidden mb-5 flex-col sm:flex-row sm:items-center gap-3 rounded-2xl border-4 px-4 py-3"
                style="display:none; background:color-mix(in srgb, var(--color-gold) 16%, #fff); border-color:color-mix(in srgb, var(--color-gold) 55%, transparent)">
                <p id="waarschuwingTekst" class="flex-1 text-sm font-extrabold"></p>
                <button id="waarschuwingActie" type="button" class="btn-primary !text-sm !px-5 !py-2 shrink-0"></button>
            </div>

            <!-- Openstaande bestellingen -->
            <div class="dash-card rise mb-5" style="--d:.04s">
                <p class="font-display text-xl mb-3">Openstaande bestellingen</p>
                <div id="openOrders" class="space-y-2.5"></div>
                <div id="openLeeg" class="empty-box hidden">🎉 Geen openstaande bestellingen, alles is de deur uit!</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div class="dash-card rise" style="--d:.05s">
                    <p class="lbl !ml-0">Bestellingen vandaag</p>
                    <p id="statAantal" class="font-display text-4xl mt-1" data-teller="{{ $vandaagBestellingen->count() }}">0</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">{{ $vandaagBestellingen->count() ? 'Lekker bezig! 💪' : 'Wachten op je eerste 🤞' }}</p>
                </div>
                <div class="dash-card rise" style="--d:.1s">
                    <p class="lbl !ml-0">Omzet vandaag</p>
                    <p id="statOmzet" class="font-display text-4xl mt-1" data-cents="{{ $vandaagBestellingen->sum('totaal') }}">€ {{ number_format($vandaagBestellingen->sum('totaal') / 100, 2, ',', '.') }}</p>
                    <p class="text-xs font-extrabold text-cacao/40 mt-1">{{ $vandaagBestellingen->count() ? 'Van ' . $vandaagBestellingen->count() . ' bestelling' . ($vandaagBestellingen->count() === 1 ? '' : 'en') . ' vandaag' : 'De teller start bij je eerste bestelling' }}</p>
                </div>
                <div class="dash-card rise" style="--d:.15s">
                    <div class="flex items-center justify-between">
                        <p class="lbl !ml-0">Schatting drukte</p>
                        <span id="drukteBadge" class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Vandaag</span>
                    </div>
                    <div id="drukteChart" class="flex items-end gap-[3px] h-20 mt-2"></div>
                    <p id="drukteSub" class="text-xs font-extrabold text-cacao/40 mt-2">…</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Checklist met voortgangsring -->
                <div class="dash-card rise" style="--d:.2s">
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

                <!-- Snel regelen -->
                <div class="dash-card rise" style="--d:.25s">
                    <p class="font-display text-xl mb-3">Snel regelen</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" data-nav="menukaart" class="qa-btn"><span class="q-ico">📋</span> Menu aanpassen</button>
                        <a href="{{ route('onboarding') }}?stap=hours" class="qa-btn"><span class="q-ico">🕐</span> Tijden wijzigen</a>
                        <a href="{{ route('onboarding') }}?stap=style" class="qa-btn"><span class="q-ico">🎨</span> Pagina stylen</a>
                        <span class="qa-btn uit"><span class="q-ico">📦</span> Dozen bestellen <span class="n-soon !absolute !-top-2 !-right-2">Binnenkort</span></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                <!-- Deel je bestelpagina -->
                <div class="dash-card rise" style="--d:.3s">
                    <p class="font-display text-xl mb-3">Deel je bestelpagina</p>
                    <div class="flex items-center gap-4">
                        <div id="qrBox" class="w-[104px] h-[104px] shrink-0 bg-white rounded-xl border-2 border-crema-dark grid place-items-center overflow-hidden p-1.5"></div>
                        <div class="min-w-0 flex-1">
                            <p id="shareLink" class="text-xs font-extrabold text-cacao/60 break-all mb-2">…</p>
                            <button id="copyLink" type="button" class="btn-primary !text-sm !px-4 !py-2">Kopieer link</button>
                            <p class="text-[11px] font-extrabold text-cacao/40 mt-2">Print de QR voor op je toonbank of flyers</p>
                        </div>
                    </div>
                </div>

                <!-- Toppers deze week -->
                <div class="dash-card rise" style="--d:.35s">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-display text-xl">Toppers deze week</p>
                        <span class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Voorbeeld</span>
                    </div>
                    <div id="topGerechten" class="space-y-3"></div>
                </div>

                <!-- Gemiddelde doorlooptijd -->
                <div class="dash-card rise" style="--d:.4s">
                    <div class="flex items-center justify-between">
                        <p class="lbl !ml-0">Gemiddelde doorlooptijd</p>
                        <span class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Voorbeeld</span>
                    </div>
                    <p class="font-display text-4xl mt-1">24 min</p>
                    <div class="mt-2.5 space-y-1.5 text-xs font-extrabold text-cacao/50">
                        <p>🔥 In de oven: gemiddeld 14 min</p>
                        <p>🛵 Onderweg: gemiddeld 10 min</p>
                    </div>
                </div>
            </div>

            <!-- Omzet afgelopen 7 dagen -->
            <div class="dash-card rise mt-4" style="--d:.45s">
                <div class="flex items-center justify-between">
                    <p class="font-display text-xl">Omzet afgelopen 7 dagen</p>
                    <span class="text-[10px] font-extrabold uppercase tracking-wide bg-crema-dark text-cacao/50 rounded-full px-2 py-0.5">Voorbeeld</span>
                </div>
                <div id="omzetWeek" class="flex items-end gap-2 h-24 mt-3"></div>
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
                    <a href="{{ route('onboarding') }}?stap=style" class="btn-primary inline-block !text-lg !px-8 !py-3">Pagina stylen</a>
                </div>
            </div>
        </section>

        <!-- PANEEL: Dozen -->
        <section data-panel="dozen" class="panel">
            <div class="dash-card text-center py-8">
                <img src="{{ asset('stickers/holding-3-pizzas.png') }}" alt="" class="h-32 mx-auto mb-3 select-none pointer-events-none">
                <p class="font-display text-2xl mb-1">Dozen bijbestellen</p>
                <p class="text-sm font-extrabold text-cacao/50 max-w-sm mx-auto">Straks bestel je hier met één tik nieuwe pizzadozen, inclusief jouw eigen opdruk. Nog even geduld! 📦</p>
            </div>
        </section>

        <!-- PANEEL: Instellingen -->
        @php
            $ob = auth()->user()->onboarding ?? [];
            $obSlug = substr(preg_replace('/[^a-z0-9]+/', '', strtolower(\Illuminate\Support\Str::ascii($ob['name'] ?? ''))), 0, 30) ?: 'jouwpizzeria';
            $betaalLabels = ['mollie' => 'Online betalen via Mollie', 'stripe' => 'Online betalen via Stripe', 'later' => 'Nog geen keuze gemaakt'];
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

                <div class="dash-card">
                    <div class="flex items-center gap-3 mb-3">
                        <p class="font-display text-xl flex-1">🕐 Openingstijden</p>
                        <button type="button" data-inst="tijden" class="skip-link !mt-0">wijzig</button>
                    </div>
                    <p id="tijdenSamenvatting" class="text-sm font-extrabold text-cacao/50 mb-3">Nog niet ingesteld.</p>
                    <div id="tijdenLijst" class="space-y-1.5 text-sm font-extrabold"></div>
                </div>

                <div class="space-y-4">
                    <div class="dash-card">
                        <div class="flex items-center gap-3 mb-3">
                            <p class="font-display text-xl flex-1">💶 Betalen</p>
                            <button type="button" data-inst="betalen" class="skip-link !mt-0">wijzig</button>
                        </div>
                        <p id="betaalWaarde" class="text-sm font-extrabold">{{ $betaalLabels[$ob['payment'] ?? 'later'] ?? 'Nog geen keuze gemaakt' }}</p>
                    </div>

                    <div class="dash-card">
                        <div class="flex items-center gap-3 mb-3">
                            <p class="font-display text-xl flex-1">🌐 Jouw domein</p>
                            <button type="button" data-inst="domein" class="skip-link !mt-0">wijzig</button>
                        </div>
                        <p id="domeinWaarde" class="text-sm font-extrabold break-all">{{ $eigenDomein ? 'bestellen.' . strtolower(preg_replace('#^(https?://)?(www\.)?#', '', $ob['ownDomain'])) : $obSlug . '.bestelpagina.nl' }}</p>
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
            <div class="grid lg:grid-cols-[1fr_24rem] gap-4 items-start">
                <div class="dash-card">
                    <p class="font-display text-xl mb-1">🗺️ Jouw bezorggebied</p>
                    <p class="text-sm font-extrabold text-cacao/50 mb-4">Zo ver bezorg je op dit moment. De ringen horen bij de tarieven hiernaast.</p>
                    <div id="bezorgMap" class="aspect-square w-full rounded-2xl border-4 border-crema-dark z-0" style="background:var(--color-crema)"></div>
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
    <div id="instModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-cacao/60 backdrop-blur-sm">
        <div id="instModalMidden" class="min-h-full flex items-center justify-center p-4 py-10">
            <div class="relative w-full max-w-xl">
                <div id="instMascot" class="mascot mascot-right" aria-hidden="true">
                    <div id="instMascotBubble" class="bubble"></div>
                    <img id="instMascotImg" src="{{ asset('stickers/waving-hello.png') }}" alt="">
                </div>
                <div class="relative bg-white rounded-3xl border-4 border-crema-dark shadow-2xl p-6 sm:p-10 text-center animate-pop">
                    <button type="button" id="instModalSluit" class="absolute top-4 right-4 w-10 h-10 rounded-full grid place-items-center cursor-pointer hover:bg-crema transition-colors" style="background:var(--color-crema-dark)" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    <p id="instKicker" class="step-kicker"></p>
                    <h2 id="instTitel" class="step-title"></h2>
                    <p id="instSub" class="step-sub"></p>
                    <div id="instVelden" class="text-left space-y-4 mt-2"></div>
                    <div class="mt-8">
                        <button type="button" id="instOpslaan" class="btn-primary !px-10 !py-3">Opslaan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gerecht toevoegen of bewerken: stap voor stap, net als de onboarding -->
    <div id="menuModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-cacao/60 backdrop-blur-sm">
        <div id="mModalMidden" class="min-h-full flex items-center justify-center p-4 py-10">
            <div class="relative w-full max-w-xl">
                <div id="mMascot" class="mascot mascot-right" aria-hidden="true">
                    <div id="mMascotBubble" class="bubble"></div>
                    <img id="mMascotImg" src="{{ asset('stickers/tossing-dough.png') }}" alt="">
                </div>
                <div class="relative bg-white rounded-3xl border-4 border-crema-dark shadow-2xl p-6 sm:p-10 text-center animate-pop">
                    <button type="button" id="menuModalSluit" class="absolute top-4 right-4 w-10 h-10 rounded-full grid place-items-center cursor-pointer hover:bg-crema transition-colors" style="background:var(--color-crema-dark)" aria-label="Sluiten"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    <p id="mKicker" class="step-kicker"></p>
                    <h2 id="mTitel" class="step-title"></h2>
                    <p id="mSub" class="step-sub"></p>

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

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
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
        <div class="bg-white rounded-3xl border-4 border-crema-dark shadow-2xl max-w-2xl w-full p-6 sm:p-8 text-center animate-pop">
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
    <script>window.PP_MENU = @json(auth()->user()->menuItems()->orderBy('volgorde')->orderBy('id')->get());</script>
    <script>window.PP_INTRO = @json(! auth()->user()->intro_seen);</script>
    <script>window.PP_STATUS = @json(['online' => (bool) auth()->user()->is_online, 'mode' => auth()->user()->order_mode ?? 'bezorgen_afhalen']);</script>
    <script src="{{ asset('assets/dashboard.js') }}?v={{ filemtime(public_path('assets/dashboard.js')) }}"></script>
</body>
</html>
