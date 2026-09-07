<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zet je pizzeria online</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Alle template-fonts, zodat de previews in de stijl-stap kloppen met de echte bestelpagina's --}}
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&family=Inter+Tight:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* "static" zodat ook kleuren die alleen in style.css gebruikt worden blijven bestaan */
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
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen overflow-x-hidden antialiased">

    <!-- Mini-logo, geen afleiding -->
    <div class="fixed top-5 left-6 z-40 flex items-center gap-2">
        <i class="fa-solid fa-pizza-slice text-2xl text-tomato" aria-hidden="true"></i>
    </div>

    <!-- Voortgang -->
    <div id="progressWrap" class="fixed top-0 left-0 right-0 z-30 hidden">
        <div class="h-2 bg-crema-dark">
            <div id="progressBar" class="progress-bar h-full bg-tomato rounded-r-full" style="width:0%"></div>
        </div>
        <div class="flex justify-center mt-3">
            <span id="stepCounter" class="text-xs font-semibold tracking-wide bg-white/80 backdrop-blur px-3 py-1 rounded-full shadow-sm text-cacao/60"></span>
        </div>
    </div>

    <!-- ══════════════════ DE WIZARD ══════════════════ -->
    <main id="wizard" class="relative z-10 min-h-screen flex items-center justify-center px-5 py-24">

        <!-- STAP: naam pizzeria -->
        <section data-step="name" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker">Eerst even voorstellen</p>
            <h2 class="step-title">Hoe heet jouw pizzeria?</h2>
            <p class="step-sub">De naam die straks op je bestelpagina staat.</p>
            <input id="inpName" type="text" placeholder="Pizzeria Mario" autocomplete="organization"
                class="inp text-center" maxlength="50">
            <p class="err" data-err="name"></p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <p class="enter-hint">of druk op <b>Enter ↵</b></p>
        </section>

        <!-- STAP: contactpersoon -->
        <section data-step="person" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker">Even kennismaken</p>
            <h2 class="step-title">En wie ben jij?</h2>
            <p class="step-sub">De baas, de pizzaiolo, of allebei tegelijk?</p>
            <input id="inpPerson" type="text" placeholder="Je voor- en achternaam" autocomplete="name"
                class="inp text-center" maxlength="60">
            <p class="err" data-err="person"></p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <p class="enter-hint">of druk op <b>Enter ↵</b></p>
        </section>

        <!-- STAP: contactgegevens -->
        <section data-step="contact" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-envelope" aria-hidden="true"></i> Blijven we in contact?</p>
            <h2 class="step-title">Waar kunnen we je bereiken?</h2>
            <p class="step-sub">Alleen voor het regelen van jouw pagina. Geen spam, beloofd.</p>
            <div class="space-y-4 text-left">
                <div>
                    <label for="inpEmail" class="lbl">E-mailadres</label>
                    <input id="inpEmail" type="email" placeholder="mario@pizzeriamario.nl" autocomplete="email" class="inp">
                    <p class="err" data-err="email"></p>
                </div>
                <div>
                    <label for="inpPhone" class="lbl">Telefoonnummer <span class="text-cacao/40">(mag ook later)</span></label>
                    <input id="inpPhone" type="tel" placeholder="06 12 34 56 78" autocomplete="tel" class="inp">
                </div>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <p class="enter-hint">of druk op <b>Enter ↵</b></p>
        </section>

        @guest
        <!-- STAP: wachtwoord (de onboarding is ook je registratie) -->
        <section data-step="wachtwoord" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-lock" aria-hidden="true"></i> Jouw sleutel tot de zaak</p>
            <h2 class="step-title">Kies een wachtwoord</h2>
            <p class="step-sub">Hiermee log je straks in op je eigen dashboard. Verder hoef je niks te regelen, je account staat direct klaar.</p>
            <div class="space-y-4 text-left">
                <div>
                    <label for="inpPassword" class="lbl">Wachtwoord</label>
                    <div class="relative">
                        <input id="inpPassword" type="password" placeholder="Kies iets sterks" autocomplete="new-password" class="inp !pr-14">
                        <button type="button" data-oog="inpPassword" class="pw-oog" aria-label="Wachtwoord tonen of verbergen"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div>
                    <label for="inpPassword2" class="lbl">Nog een keer</label>
                    <div class="relative">
                        <input id="inpPassword2" type="password" placeholder="Zelfde als hierboven" autocomplete="new-password" class="inp !pr-14">
                        <button type="button" data-oog="inpPassword2" class="pw-oog" aria-label="Wachtwoord tonen of verbergen"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                    </div>
                    <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5" aria-live="polite">
                        <li class="pw-eis" data-pweis="lengte"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 8 tekens</li>
                        <li class="pw-eis" data-pweis="hoofdletter"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 hoofdletter</li>
                        <li class="pw-eis" data-pweis="kleineletter"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 kleine letter</li>
                        <li class="pw-eis" data-pweis="cijfer"><span class="pw-dot"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span> Minimaal 1 cijfer</li>
                    </ul>
                    <p class="err" data-err="password"></p>
                </div>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <p class="enter-hint">of druk op <b>Enter ↵</b></p>
        </section>
        @endguest

        <!-- STAP: bedrijfsgegevens -->
        <section data-step="company" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i> Even de administratie</p>
            <h2 class="step-title">Bedrijfsgegevens</h2>
            <p class="step-sub">Deze gegevens hebben we nodig voor je administratie en de facturatie van je verkopen.</p>
            <div class="space-y-4 text-left">
                <div>
                    <label for="inpKvk" class="lbl">KvK-nummer <span class="normal-case tracking-normal text-cacao/35">(mag ook later)</span></label>
                    <input id="inpKvk" type="text" inputmode="numeric" placeholder="12345678" class="inp" maxlength="8">
                </div>
                <div>
                    <label for="inpStreet" class="lbl">Straat + huisnummer</label>
                    <input id="inpStreet" type="text" placeholder="Dorpsstraat 1" autocomplete="street-address" class="inp">
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-2">
                        <label for="inpZip" class="lbl">Postcode</label>
                        <input id="inpZip" type="text" placeholder="1234 AB" autocomplete="postal-code" class="inp">
                    </div>
                    <div class="col-span-3">
                        <label for="inpCity" class="lbl">Plaats</label>
                        <input id="inpCity" type="text" placeholder="Amsterdam" autocomplete="address-level2" class="inp">
                    </div>
                </div>
                <p class="err" data-err="company"></p>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: openingstijden -->
        <section data-step="hours" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-fire" aria-hidden="true"></i> Oven aan</p>
            <h2 class="step-title">Wanneer kunnen mensen bestellen?</h2>
            <p class="step-sub">Tik de dagen aan dat je open bent.</p>
            <div id="dayChips" class="flex flex-wrap justify-center gap-2.5 mb-6"></div>

            <div class="flex justify-center mb-6">
                <div class="mode-switch">
                    <button type="button" data-hoursmode="same" class="mode-opt">Elke dag dezelfde tijden</button>
                    <button type="button" data-hoursmode="perday" class="mode-opt">Per dag apart</button>
                </div>
            </div>

            <div id="sameTimes" class="flex items-center justify-center gap-3 font-semibold text-lg">
                <span class="text-cacao/50 text-base">van</span>
                <input id="inpOpen" type="time" value="16:00" class="inp-time">
                <span class="text-cacao/50 text-base">tot</span>
                <input id="inpClose" type="time" value="21:30" class="inp-time">
            </div>
            <div id="perDayTimes" class="hidden space-y-2"></div>
            <p class="err" data-err="hours"></p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: menu -->
        <section data-step="menu" class="step hidden w-full max-w-2xl text-center">
            <p class="step-kicker">Nu het lekkerste deel</p>
            <h2 class="step-title">Wat ligt er straks in je oven?</h2>
            <p class="step-sub">Bouw je menukaart met categorieën, net als op je echte kaart. Alles kun je later nog aanpassen.</p>

            <!-- Categorieën: toegevoegd (vol) + suggesties (gestippeld) + eigen categorie -->
            <div id="catBar" class="flex flex-wrap justify-center items-center gap-2 mb-2"></div>
            <p class="text-xs font-semibold text-cacao/35 mb-7">Tik een categorie aan en zet er je gerechten in</p>

            <div id="presetGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6"></div>

            <div id="menuList" class="space-y-2 mb-4 text-left"></div>

            <div class="flex gap-2 items-center">
                <input id="customName" type="text" placeholder="Zelf toevoegen? Bijv. Pizza Mario" class="inp !text-base flex-1">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-semibold text-cacao/40">€</span>
                    <input id="customPrice" type="text" inputmode="decimal" placeholder="12,50" class="inp !text-base !pl-8 !w-24 sm:!w-28">
                </div>
                <button id="customAdd" type="button"
                    class="shrink-0 self-stretch w-[3.8rem] rounded-2xl bg-basil text-white font-display text-2xl shadow-[0_1px_2px_rgb(36_23_18_/_.1)] hover:bg-basil-dark transition-colors cursor-pointer">+</button>
            </div>
            <p class="mt-3 text-xs font-semibold text-cacao/35"><i class="fa-solid fa-camera" aria-hidden="true"></i> Tip: tik op het fotovakje voor een gerecht om een eigen foto toe te voegen</p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: spaarpunten -->
        <section data-step="punten" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-star" aria-hidden="true"></i> Vaste klanten</p>
            <h2 class="step-title">Klanten laten sparen?</h2>
            <p class="step-sub">Klanten sparen automatisch punten met elke bestelling. Die punten leveren straks extra korting op bij het afrekenen.</p>
            <div class="space-y-3">
                <button type="button" data-punten="aan" class="choice-card">
                    <span class="w-11 h-11 shrink-0 rounded-full bg-crema border border-crema-dark grid place-items-center text-gold"><i class="fa-solid fa-star" aria-hidden="true"></i></span>
                    <span class="flex-1 text-left">
                        <span class="block font-display text-xl">Ja, spaarpunten aan</span>
                        <span class="block text-sm font-bold text-cacao/50">Punten bij elke bestelling, korting bij het afrekenen</span>
                    </span>
                    <span class="badge-gold">Aanbevolen</span>
                </button>
                <button type="button" data-punten="uit" class="choice-card">
                    <span class="w-11 h-11 shrink-0 rounded-full bg-crema border border-crema-dark grid place-items-center text-cacao/40"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
                    <span class="flex-1 text-left">
                        <span class="block font-display text-xl">Nee, liever niet</span>
                        <span class="block text-sm font-bold text-cacao/50">Je kunt dit later altijd nog aanzetten</span>
                    </span>
                </button>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: subdomein -->
        <section data-step="domain" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-globe" aria-hidden="true"></i> Jouw plekje op het internet</p>
            <h2 class="step-title">Kies je bestel-adres</h2>
            <p class="step-sub">Hier komen je klanten straks bestellen.</p>
            <div class="space-y-3 text-left">
                <button type="button" data-domain="sub" class="choice-card">
                    <span class="w-11 h-11 shrink-0 rounded-full bg-crema border border-crema-dark grid place-items-center text-tomato"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
                    <span class="flex-1">
                        <span class="block font-display text-xl break-all"><span data-slug>jouwpizzeria</span>.mijnpizzeria.nl</span>
                        <span class="block text-sm font-bold text-cacao/50">Direct live, je hebt niks nodig</span>
                    </span>
                </button>
                <button type="button" data-domain="own" class="choice-card">
                    <span class="w-11 h-11 shrink-0 rounded-full bg-crema border border-crema-dark grid place-items-center text-tomato"><i class="fa-solid fa-house" aria-hidden="true"></i></span>
                    <span class="flex-1">
                        <span class="block font-display text-xl">Op mijn eigen website</span>
                        <span class="block text-sm font-bold text-cacao/50">Bijv. bestellen.jouwdomein.nl, wij koppelen 'm gratis</span>
                    </span>
                    @auth
                        @if(! auth()->user()->abonnement_actief)
                            <span class="badge-gold">Met abonnement</span>
                        @endif
                    @endauth
                </button>
            </div>
            <div id="ownDomainWrap" class="hidden mt-4 text-left">
                <label for="inpOwnDomain" class="lbl">Jouw huidige website</label>
                <input id="inpOwnDomain" type="text" placeholder="pizzeriamario.nl" class="inp">
                <p id="ownDomainPreview" class="mt-2 text-sm font-semibold text-basil hidden">
                    <i class="fa-solid fa-check" aria-hidden="true"></i> Wordt: <span class="underline underline-offset-2"></span>
                </p>
            </div>
            <p id="domainCheck" class="mt-4 h-6 text-sm font-semibold"></p>
            <p class="err" data-err="domain"></p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: huisstijl -->
        <section data-step="style" class="step hidden w-full max-w-3xl lg:max-w-6xl text-center">
            <p class="step-kicker"><i class="fa-solid fa-palette" aria-hidden="true"></i> De finishing touch</p>
            <h2 class="step-title">Maak 'm helemaal van jou</h2>
            <p class="step-sub">Kies een template dat bij jouw zaak past, maak 'm af met jouw kleur en logo. Je ziet meteen hoe je bestelpagina eruit gaat zien.</p>

            <div class="text-left flex flex-col lg:grid lg:grid-cols-[1fr_1.2fr] lg:gap-x-8 lg:gap-y-14">
                <!-- Links: de templates -->
                <div class="order-1 lg:col-start-1 lg:row-start-1">
                    <div class="h-8 mb-2 flex items-center">
                        <p class="lbl !ml-0 !mb-0">Jouw template</p>
                    </div>
                    <div id="themeGrid" class="grid grid-cols-2 gap-3"></div>
                </div>

                <!-- Het live voorbeeld: desktop rechts naast de templates, op mobiel
                     onder de kleuren en standaard ingeklapt achter een knop -->
                <div class="order-3 mt-8 lg:mt-0 lg:col-start-2 lg:row-start-1 flex flex-col">
                    <button id="pvToggle" type="button" class="btn-primary w-full !py-3 lg:hidden">Bekijk live voorbeeld</button>
                    <div id="pvInhoud" class="hidden lg:block flex-1 min-h-0 mt-4 lg:mt-0">
                        <div class="flex flex-col h-full">
                            <div class="h-8 mb-2 flex items-center justify-between gap-3">
                                <p class="lbl !ml-0 !mb-0">Live voorbeeld</p>
                                <div class="flex gap-1.5">
                                    <button type="button" data-ob-pagina="menu" class="keuze-chip !py-1 !px-3 !text-xs aan">Menu</button>
                                    <button type="button" data-ob-pagina="afrekenen" class="keuze-chip !py-1 !px-3 !text-xs">Afrekenen</button>
                                    <button type="button" data-ob-pagina="status" class="keuze-chip !py-1 !px-3 !text-xs">Status</button>
                                </div>
                            </div>
                            <div class="relative flex-1 min-h-0 w-full overflow-hidden rounded-2xl border border-crema-dark bg-white aspect-[9/16] lg:aspect-auto">
                                <iframe id="obStijlPreview" title="Voorbeeld van je bestelpagina" style="width:200%;height:200%;transform:scale(.5);transform-origin:top left;border:0"></iframe>
                                <div id="obStijlLaad" class="absolute inset-0 grid place-items-center bg-white/75" style="display:none">
                                    <div class="text-center">
                                        <i class="fa-solid fa-spinner inline-block text-2xl text-tomato animate-spin" aria-hidden="true"></i>
                                        <p class="mt-2 text-sm font-semibold text-cacao/60">Voorbeeld laden…</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs font-semibold text-cacao/45 mt-2">Scrollen kan, klikken staat uit in het voorbeeld.</p>
                        </div>
                    </div>
                </div>

                <div class="order-2 mt-14 lg:mt-0 lg:col-span-2 lg:row-start-2 grid lg:grid-cols-[auto_1fr] gap-10 items-stretch">
                    <div>
                        <p class="lbl !ml-0 mb-3">Jouw kleurstijl</p>
                        <div id="colorGrid"></div>
                    </div>
                    <div class="flex flex-col">
                        <p class="lbl !ml-0 mb-3">Jouw logo</p>
                        <div class="relative flex-1">
                            <label for="logoInp" class="logo-drop !flex flex-col items-center justify-center h-full">
                                <div id="logoDropThumb" class="hidden w-12 h-12 rounded-full border border-crema-dark overflow-hidden bg-white mx-auto mb-2">
                                    <img class="w-full h-full object-cover" alt="Jouw logo">
                                </div>
                                <span id="logoDropText">Logo uploaden</span>
                                <span id="logoDropHint" class="block text-xs font-semibold text-cacao/35 mt-1">Nog geen logo bij de hand? Mag ook later.</span>
                            </label>
                            <button id="logoRemove" type="button" title="Logo verwijderen" aria-label="Logo verwijderen" style="display:none"
                                class="absolute -top-2.5 -right-2.5 z-10 w-7 h-7 rounded-full bg-white border border-crema-dark place-items-center text-sm font-semibold text-cacao/45 shadow-sm hover:text-tomato hover:border-tomato/50 transition-colors cursor-pointer"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                        </div>
                        <input id="logoInp" type="file" accept="image/*" class="hidden">
                    </div>
                </div>
            </div>

            <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Bijna klaar
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>
            </div>
        </section>

        <!-- STAP: overzicht -->
        <section data-step="overview" class="step hidden w-full max-w-xl text-center">
            <p class="step-kicker">De laatste check</p>
            <h2 class="step-title">Klopt dit allemaal?</h2>
            <p class="step-sub">Tik op een regel om iets aan te passen.</p>
            <div id="summary" class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.1)] p-5 sm:p-7 text-left space-y-1 mb-8"></div>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button id="submitBtn" type="button" class="btn-primary text-2xl px-12 py-5">Onboarding afronden</button>
            </div>
            <p class="mt-4 text-sm font-semibold text-cacao/40">Daarna sta je meteen in je eigen dashboard.</p>
        </section>

    </main>

    <!-- Foto-kiezer voor menu-items: opent direct de bestandskiezer -->
    <input id="photoInp" type="file" accept="image/*" class="hidden">

    <!-- Opnieuw beginnen (alleen zichtbaar als er bewaarde antwoorden zijn) -->
    <button id="resetLink" type="button"
        class="hidden fixed bottom-4 right-4 z-40 text-xs font-semibold text-cacao/45 bg-white/80 backdrop-blur border border-crema-dark rounded-full px-4 py-2 hover:text-tomato hover:border-tomato/40 transition-colors cursor-pointer">
        <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Opnieuw beginnen
    </button>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 z-50 bg-cacao text-crema font-semibold text-sm px-5 py-3 rounded-full shadow-lg"></div>

    <!-- Confetti -->
    <canvas id="confetti" class="pointer-events-none fixed inset-0 z-50 hidden"></canvas>

    <script>window.PP_PROEF = @json(auth()->check() && ! auth()->user()->abonnement_actief);</script>
    <script>window.PP_AUTH = @json(auth()->check());</script>
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
