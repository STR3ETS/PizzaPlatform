<!DOCTYPE html>
<html lang="nl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zet je zaak online | Shop &amp; Eat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Figtree voor de wizard zelf; de template-fonts blijven erbij voor de tegeltjes in de stijl-stap --}}
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600&family=Young+Serif&family=Inter+Tight:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* De oude tokennamen wijzen naar het palet van /stijlgids, zodat de
           utilities die app.js nog uitstuurt in dezelfde kleuren meelopen. */
        @theme static {
            --color-crema: var(--soft);
            --color-crema-dark: var(--line);
            --color-tomato: var(--omgekeerd);
            --color-tomato-dark: var(--omgekeerd);
            --color-basil: var(--omgekeerd);
            --color-basil-dark: var(--omgekeerd);
            --color-gold: var(--ink3);
            --color-cacao: var(--ink);
            --color-white: var(--card);
            --font-display: "Figtree", sans-serif;
            --font-body: "Figtree", sans-serif;
        }
    </style>
    {{-- Volgorde: oud systeem (gedrag van de wizard), dan het systeem, dan de brug, dan de wizard-onderdelen --}}
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/systeem.css') }}?v={{ filemtime(public_path('assets/stijl/systeem.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/dashboard.css') }}?v={{ filemtime(public_path('assets/stijl/dashboard.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/onboarding.css') }}?v={{ filemtime(public_path('assets/stijl/onboarding.css')) }}">
</head>
<body class="antialiased">

    <div class="wizard-pagina">
        <div class="vlak wizard-vlak">

            {{-- Kop: het merk links, de stappenteller rechts --}}
            <div class="wizard-kop">
                <a href="{{ route('home') }}" class="merk" title="Naar de site"><i aria-hidden="true"></i>Shop &amp; Eat</a>
                <div id="progressWrap" class="hidden">
                    <span id="stepCounter" class="tag"></span>
                </div>
            </div>
            <div class="wizard-lijn"><div id="progressBar" class="progress-bar" style="width:0%"></div></div>

            <!-- ══════════════════ DE WIZARD ══════════════════ -->
            <main id="wizard" class="wizard wizard-lijf">

                <!-- STAP: naam van de zaak -->
                <section data-step="name" class="step hidden w-full max-w-xl text-center">
                    <p class="label">Eerst even voorstellen</p>
                    <h2 class="stap-titel">Hoe heet jouw zaak?</h2>
                    <p class="lead stap-sub">De naam die straks op je bestelpagina staat.</p>
                    <input id="inpName" type="text" placeholder="Bijv. Pizzeria Mario of Snackbar De Hoek" autocomplete="organization" class="inp text-center" maxlength="50">
                    <p class="err" data-err="name"></p>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                    <p class="enter-hint small">of druk op <b>Enter ↵</b></p>
                </section>

                <!-- STAP: contactpersoon -->
                <section data-step="person" class="step hidden w-full max-w-xl text-center">
                    <p class="label">Even kennismaken</p>
                    <h2 class="stap-titel">En wie ben jij?</h2>
                    <p class="lead stap-sub">De eigenaar, de chef, of allebei tegelijk?</p>
                    <input id="inpPerson" type="text" placeholder="Je voor- en achternaam" autocomplete="name" class="inp text-center" maxlength="60">
                    <p class="err" data-err="person"></p>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                    <p class="enter-hint small">of druk op <b>Enter ↵</b></p>
                </section>

                <!-- STAP: contactgegevens -->
                <section data-step="contact" class="step hidden w-full max-w-xl text-center">
                    <p class="label">Blijven we in contact?</p>
                    <h2 class="stap-titel">Waar kunnen we je bereiken?</h2>
                    <p class="lead stap-sub">Alleen voor het regelen van jouw pagina. Geen spam, beloofd.</p>
                    <div class="flex flex-col gap-5 text-left">
                        <div class="fld">
                            <label for="inpEmail">E-mailadres</label>
                            <input id="inpEmail" type="email" placeholder="jij@jouwzaak.nl" autocomplete="email">
                            <p class="fout" data-err="email"></p>
                        </div>
                        <div class="fld">
                            <label for="inpPhone">Telefoonnummer (mag ook later)</label>
                            <input id="inpPhone" type="tel" placeholder="06 12 34 56 78" autocomplete="tel">
                        </div>
                    </div>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                    <p class="enter-hint small">of druk op <b>Enter ↵</b></p>
                </section>

                @guest
                <!-- STAP: wachtwoord (de onboarding is ook je registratie) -->
                <section data-step="wachtwoord" class="step hidden w-full max-w-xl text-center">
                    <p class="label">Jouw sleutel tot de zaak</p>
                    <h2 class="stap-titel">Kies een wachtwoord</h2>
                    <p class="lead stap-sub">Hiermee log je straks in op je eigen dashboard. Verder hoef je niks te regelen, je account staat direct klaar.</p>
                    <div class="flex flex-col gap-5 text-left">
                        <div class="fld">
                            <label for="inpPassword">Wachtwoord</label>
                            <div class="relative">
                                <input id="inpPassword" type="password" placeholder="Kies iets sterks" autocomplete="new-password" style="padding-right:40px">
                                <button type="button" data-oog="inpPassword" class="pw-oog cursor-pointer" aria-label="Wachtwoord tonen of verbergen"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="fld">
                            <label for="inpPassword2">Nog een keer</label>
                            <div class="relative">
                                <input id="inpPassword2" type="password" placeholder="Zelfde als hierboven" autocomplete="new-password" style="padding-right:40px">
                                <button type="button" data-oog="inpPassword2" class="pw-oog cursor-pointer" aria-label="Wachtwoord tonen of verbergen"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                            </div>
                            <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5" aria-live="polite">
                                <li class="pw-eis" data-pweis="lengte"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 8 tekens</li>
                                <li class="pw-eis" data-pweis="hoofdletter"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 hoofdletter</li>
                                <li class="pw-eis" data-pweis="kleineletter"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 kleine letter</li>
                                <li class="pw-eis" data-pweis="cijfer"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 cijfer</li>
                            </ul>
                            <p class="fout" data-err="password"></p>
                        </div>
                    </div>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                    <p class="enter-hint small">of druk op <b>Enter ↵</b></p>
                </section>
                @endguest

                <!-- STAP: bedrijfsgegevens -->
                <section data-step="company" class="step hidden w-full max-w-xl text-center">
                    <p class="label">Even de administratie</p>
                    <h2 class="stap-titel">Bedrijfsgegevens</h2>
                    <p class="lead stap-sub">Deze gegevens hebben we nodig voor je administratie en de facturatie van je verkopen.</p>
                    <div class="flex flex-col gap-5 text-left">
                        <div class="fld">
                            <label for="inpKvk">KvK-nummer (mag ook later)</label>
                            <input id="inpKvk" type="text" inputmode="numeric" placeholder="12345678" maxlength="8">
                        </div>
                        <div class="fld">
                            <label for="inpStreet">Straat + huisnummer</label>
                            <input id="inpStreet" type="text" placeholder="Dorpsstraat 1" autocomplete="street-address">
                        </div>
                        <div class="grid grid-cols-5 gap-4">
                            <div class="fld col-span-2">
                                <label for="inpZip">Postcode</label>
                                <input id="inpZip" type="text" placeholder="1234 AB" autocomplete="postal-code">
                            </div>
                            <div class="fld col-span-3">
                                <label for="inpCity">Plaats</label>
                                <input id="inpCity" type="text" placeholder="Amsterdam" autocomplete="address-level2">
                            </div>
                        </div>
                        <p class="fout" data-err="company"></p>
                    </div>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                </section>

                @include('onboarding.stap-hours', ['terug' => true])

                <!-- STAP: menu -->
                <section data-step="menu" class="step hidden w-full max-w-2xl text-center">
                    <p class="label">Nu het lekkerste deel</p>
                    <h2 class="stap-titel">Wat komt er op je kaart?</h2>
                    <p class="lead stap-sub">Bouw je menukaart met categorieën, net als op je echte kaart. Alles kun je later nog aanpassen.</p>

                    <!-- Categorieën: toegevoegd (vol) + suggesties (gestippeld) + eigen categorie -->
                    <div id="catBar" class="flex flex-wrap justify-center items-center gap-2 mb-2"></div>
                    <p class="small mb-6">Tik een categorie aan en zet er je gerechten in</p>

                    <div id="presetGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6"></div>

                    <div id="menuList" class="space-y-2 mb-4 text-left"></div>

                    <div class="flex gap-2 items-stretch">
                        <input id="customName" type="text" placeholder="Zelf toevoegen? Bijv. Broodje bal" class="inp !text-base flex-1 min-w-0">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2" style="color:var(--ink3)">€</span>
                            <input id="customPrice" type="text" inputmode="decimal" placeholder="12,50" class="inp !text-base !pl-8 !w-24 sm:!w-28 h-full">
                        </div>
                        <button id="customAdd" type="button" class="btn dark shrink-0 cursor-pointer text-xl" style="min-width:52px; padding:0" aria-label="Toevoegen">+</button>
                    </div>
                    <p class="small mt-3">Tip: tik op het fotovakje voor een gerecht om een eigen foto toe te voegen</p>

                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
                    </div>
                </section>

                @include('onboarding.stap-punten')
                @include('onboarding.stap-domain')

                <!-- STAP: huisstijl -->
                <section data-step="style" class="step hidden w-full max-w-3xl lg:max-w-6xl text-center">
                    <p class="label">De finishing touch</p>
                    <h2 class="stap-titel">Maak 'm helemaal van jou</h2>
                    <p class="lead stap-sub">Kies een template dat bij jouw zaak past, maak 'm af met jouw kleur en logo. Je ziet meteen hoe je bestelpagina eruit gaat zien.</p>

                    <div class="text-left flex flex-col lg:grid lg:grid-cols-[1fr_1.2fr] lg:gap-x-8 lg:gap-y-12">
                        <!-- Links: de templates -->
                        <div class="order-1 lg:col-start-1 lg:row-start-1">
                            <div class="h-8 mb-2 flex items-center">
                                <p class="label">Jouw template</p>
                            </div>
                            <div id="themeGrid" class="grid grid-cols-2 gap-3"></div>
                        </div>

                        <!-- Het live voorbeeld: desktop rechts naast de templates, op mobiel
                             onder de kleuren en standaard ingeklapt achter een knop -->
                        <div class="order-3 mt-8 lg:mt-0 lg:col-start-2 lg:row-start-1 flex flex-col">
                            <button id="pvToggle" type="button" class="btn line breed lg:hidden cursor-pointer">Bekijk live voorbeeld</button>
                            <div id="pvInhoud" class="hidden lg:block flex-1 min-h-0 mt-4 lg:mt-0">
                                <div class="flex flex-col h-full">
                                    <div class="h-8 mb-2 flex items-center justify-between gap-3">
                                        <p class="label">Live voorbeeld</p>
                                        <div class="flex gap-1.5">
                                            <button type="button" data-ob-pagina="menu" class="keuze-chip !py-1 !px-3 !text-xs aan cursor-pointer">Menu</button>
                                            <button type="button" data-ob-pagina="afrekenen" class="keuze-chip !py-1 !px-3 !text-xs cursor-pointer">Afrekenen</button>
                                            <button type="button" data-ob-pagina="status" class="keuze-chip !py-1 !px-3 !text-xs cursor-pointer">Status</button>
                                        </div>
                                    </div>
                                    {{-- De pagina van de klant, altijd op wit --}}
                                    <div class="relative flex-1 min-h-0 w-full overflow-hidden aspect-[9/16] lg:aspect-auto" style="border-radius:var(--r-blok); background:#fff; box-shadow:inset 0 0 0 1px var(--line)">
                                        <iframe id="obStijlPreview" title="Voorbeeld van je bestelpagina" style="width:200%;height:200%;transform:scale(.5);transform-origin:top left;border:0"></iframe>
                                        <div id="obStijlLaad" class="absolute inset-0 grid place-items-center" style="display:none; background:rgba(255,255,255,.75); color:#141414">
                                            <div class="text-center">
                                                <i class="fa-solid fa-circle-notch fa-spin text-2xl" aria-hidden="true"></i>
                                                <p class="mt-2 text-sm">Voorbeeld laden…</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="small mt-2">Scrollen kan, klikken staat uit in het voorbeeld.</p>
                                </div>
                            </div>
                        </div>

                        <div class="order-2 mt-12 lg:mt-0 lg:col-span-2 lg:row-start-2 grid lg:grid-cols-[auto_1fr] gap-10 items-stretch">
                            <div>
                                <p class="label mb-3">Jouw kleurstijl</p>
                                <div id="colorGrid"></div>
                            </div>
                            <div class="flex flex-col">
                                <p class="label mb-3">Jouw logo</p>
                                <div class="relative flex-1">
                                    <label for="logoInp" class="logo-drop !flex flex-col items-center justify-center h-full cursor-pointer">
                                        <div id="logoDropThumb" class="hidden w-12 h-12 rounded-full overflow-hidden mx-auto mb-2" style="background:var(--card); box-shadow:inset 0 0 0 1px var(--line)">
                                            <img class="w-full h-full object-cover" alt="Jouw logo">
                                        </div>
                                        <span id="logoDropText">Logo uploaden</span>
                                        <span id="logoDropHint" class="block mini mt-1">Nog geen logo bij de hand? Mag ook later.</span>
                                    </label>
                                    <button id="logoRemove" type="button" title="Logo verwijderen" aria-label="Logo verwijderen" style="display:none; background:var(--card); box-shadow:inset 0 0 0 1px var(--line)"
                                        class="icoon-knop absolute -top-2.5 -right-2.5 z-10 cursor-pointer"><i class="fa-solid fa-xmark text-[12px]" aria-hidden="true"></i></button>
                                </div>
                                <input id="logoInp" type="file" accept="image/*" class="hidden">
                            </div>
                        </div>
                    </div>

                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button data-next type="button" class="btn dark cursor-pointer">Bijna klaar <i class="ar fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </section>

                <!-- STAP: overzicht -->
                <section data-step="overview" class="step hidden w-full max-w-xl text-center">
                    <p class="label">De laatste check</p>
                    <h2 class="stap-titel">Klopt dit allemaal?</h2>
                    <p class="lead stap-sub">Tik op een regel om iets aan te passen.</p>
                    <div id="summary" class="flex flex-col gap-2 text-left mb-2"></div>
                    <div class="stap-knoppen">
                        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                        <button id="submitBtn" type="button" class="btn dark cursor-pointer" style="min-height:52px; padding:0 34px; font-size:15px">Onboarding afronden</button>
                    </div>
                    <p class="small mt-4">Daarna sta je meteen in je eigen dashboard.</p>
                </section>

            </main>
        </div>

        <!-- Opnieuw beginnen (alleen zichtbaar als er bewaarde antwoorden zijn) -->
        <button id="resetLink" type="button" class="hidden fixed bottom-4 right-4 z-40 btn line klein cursor-pointer" style="background:var(--card)">
            <i class="fa-solid fa-trash-can text-[12px]" aria-hidden="true"></i> Opnieuw beginnen
        </button>
    </div>

    <!-- Foto-kiezer voor menu-items: opent direct de bestandskiezer -->
    <input id="photoInp" type="file" accept="image/*" class="hidden">

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 z-50 text-sm px-5 py-3 rounded-full"></div>

    <script>window.PP_PROEF = @json(auth()->check() && ! auth()->user()->abonnement_actief);</script>
    <script>window.PP_AUTH = @json(auth()->check());</script>
    <script>window.PP_DOMEIN = @json(config('app.centraal_domein'));</script>
    <script>window.PP_KLEUREN = @json(\App\Http\Controllers\BestelController::KLEUREN);</script>
    <script>window.PP_SAVED = @json(auth()->check() ? auth()->user()->onboarding : null);</script>
    @php
        $ppMenu = auth()->check()
            ? auth()->user()->menuItems()->orderBy('categorie')->orderBy('volgorde')->get()
                ->map(fn ($m) => ['id' => $m->id, 'categorie' => $m->categorie, 'naam' => $m->naam, 'prijs' => (int) $m->prijs, 'foto' => $m->foto ?: null])->values()
            : null;
    @endphp
    <script>window.PP_MENU = @json($ppMenu);</script>
    <script src="{{ asset('assets/stijl-tiles.js') }}?v={{ filemtime(public_path('assets/stijl-tiles.js')) }}"></script>
    <script src="{{ asset('assets/onboarding/app.js') }}?v={{ filemtime(public_path('assets/onboarding/app.js')) }}"></script>
</body>
</html>
