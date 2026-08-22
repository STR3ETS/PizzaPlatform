<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zet je pizzeria online 🍕</title>

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
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen overflow-x-hidden antialiased">

    <!-- Zwevende deco-emoji's op de achtergrond -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden select-none" aria-hidden="true">
        <span class="float-emoji" style="top:12%; left:6%;  --delay:0s;   --size:2.6rem;">🍕</span>
        <span class="float-emoji" style="top:70%; left:10%; --delay:1.2s; --size:2rem;">🍅</span>
        <span class="float-emoji" style="top:22%; left:88%; --delay:.6s;  --size:2.2rem;">🧀</span>
        <span class="float-emoji" style="top:78%; left:85%; --delay:2s;   --size:2.4rem;">🌿</span>
        <span class="float-emoji" style="top:45%; left:94%; --delay:2.8s; --size:1.8rem;">🔥</span>
        <span class="float-emoji" style="top:55%; left:3%;  --delay:1.8s; --size:1.8rem;">🫒</span>
    </div>

    <!-- Mini-logo, geen afleiding (tijdelijk merkloos) -->
    <div class="fixed top-5 left-6 z-40 flex items-center gap-2">
        <span class="text-2xl">🍕</span>
    </div>

    @auth
    <!-- Ingelogd: wijzigingen direct kunnen opslaan zonder alle stappen af te lopen -->
    <div class="fixed top-4 right-4 z-40 text-right">
        <button id="quickSave" type="button" class="btn-primary !text-base !px-6 !py-2.5">Opslaan ✓</button>
        <p class="mt-1.5 text-[11px] font-extrabold text-cacao/40">en terug naar je dashboard</p>
    </div>
    @endauth

    <!-- Voortgang -->
    <div id="progressWrap" class="fixed top-0 left-0 right-0 z-30 hidden">
        <div class="h-2 bg-crema-dark">
            <div id="progressBar" class="progress-bar h-full bg-tomato rounded-r-full" style="width:0%"></div>
        </div>
        <div class="flex justify-center mt-3">
            <span id="stepCounter" class="text-xs font-extrabold tracking-wide bg-white/80 backdrop-blur px-3 py-1 rounded-full shadow-sm text-cacao/60"></span>
        </div>
    </div>

    <!-- ══════════════════ DE WIZARD ══════════════════ -->
    <main id="wizard" class="relative z-10 min-h-screen flex items-center justify-center px-5 py-24">

        <!-- STAP: naam pizzeria -->
        <section data-step="name" class="step hidden w-full max-w-xl text-center">
            <div class="mascot mascot-left" aria-hidden="true">
                <div class="bubble">Ciao! Ik help je er even doorheen 👋</div>
                <img src="{{ asset('stickers') }}/waving-hello.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Eerst even voorstellen 👋</p>
            <h2 class="step-title">Hoe heet jouw pizzeria?</h2>
            <p class="step-sub">De naam die straks in vette letters op je bestelpagina staat.</p>
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
            <div class="mascot mascot-right" aria-hidden="true">
                <div class="bubble">Aangenaam! En jij bent? 😄</div>
                <img src="{{ asset('stickers') }}/kneading-dough.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Piacere! 🤝</p>
            <h2 class="step-title">En wie ben jij?</h2>
            <p class="step-sub">De baas, de pizzaiolo, of allebei tegelijk? 😄</p>
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
            <div class="mascot mascot-left" aria-hidden="true">
                <div class="bubble">Geen spam, promesso 🤞</div>
                <img src="{{ asset('stickers') }}/showing-pizza-order.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Blijven we in contact? 📬</p>
            <h2 class="step-title">Waar kunnen we je bereiken?</h2>
            <p class="step-sub">Alleen voor het regelen van jouw pagina. Geen spam, beloofd. 🤞</p>
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
            <p class="step-kicker">Jouw sleutel tot de zaak 🔐</p>
            <h2 class="step-title">Kies een wachtwoord</h2>
            <p class="step-sub">Hiermee log je straks in op je eigen dashboard. Verder hoef je niks te regelen, je account staat direct klaar.</p>
            <div class="space-y-4 text-left">
                <div>
                    <label for="inpPassword" class="lbl">Wachtwoord</label>
                    <input id="inpPassword" type="password" placeholder="Minimaal 8 tekens" autocomplete="new-password" class="inp">
                    <p class="err" data-err="password"></p>
                </div>
                <div>
                    <label for="inpPassword2" class="lbl">Nog een keer</label>
                    <input id="inpPassword2" type="password" placeholder="Zelfde als hierboven" autocomplete="new-password" class="inp">
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
            <div class="mascot mascot-right" aria-hidden="true">
                <div class="bubble">Saaie klusjes doen we samen ✍️</div>
                <img src="{{ asset('stickers') }}/writing-chalkboard.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Het saaie-maar-nodige stukje 📋</p>
            <h2 class="step-title">Bedrijfsgegevens</h2>
            <p class="step-sub">Niet bij de hand? Geen stress: sla over, dan regelen we het later samen.</p>
            <div class="space-y-4 text-left">
                <div>
                    <label for="inpKvk" class="lbl">KvK-nummer</label>
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
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <button data-skip type="button" class="skip-link">Doe ik later
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </button>
        </section>

        <!-- STAP: openingstijden -->
        <section data-step="hours" class="step hidden w-full max-w-xl text-center">
            <div class="mascot mascot-left" aria-hidden="true">
                <div class="bubble">Ik stook de oven alvast op 🔥</div>
                <img src="{{ asset('stickers') }}/pizza-in-oven.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">🔥 Oven aan!</p>
            <h2 class="step-title">Wanneer kunnen mensen bestellen?</h2>
            <p class="step-sub">Tik de dagen aan dat je open bent.</p>
            <div id="dayChips" class="flex flex-wrap justify-center gap-2.5 mb-6"></div>

            <div class="flex justify-center mb-6">
                <div class="mode-switch">
                    <button type="button" data-hoursmode="same" class="mode-opt">Elke dag dezelfde tijden</button>
                    <button type="button" data-hoursmode="perday" class="mode-opt">Per dag apart</button>
                </div>
            </div>

            <div id="sameTimes" class="flex items-center justify-center gap-3 font-extrabold text-lg">
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
            <button data-skip type="button" class="skip-link">Doe ik later
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </button>
        </section>

        <!-- STAP: menu -->
        <section data-step="menu" class="step hidden w-full max-w-2xl text-center">
            <div class="mascot mascot-right" aria-hidden="true">
                <div class="bubble">Kies je toppers, de rest maken we later af 🧀</div>
                <img src="{{ asset('stickers') }}/sprinkling-cheese.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Nu het lekkerste deel 🤌</p>
            <h2 class="step-title">Wat ligt er straks in je oven?</h2>
            <p class="step-sub">Bouw je menukaart met categorieën, net als op je echte kaart. Alles kun je later nog aanpassen.</p>

            <!-- Categorieën: toegevoegd (vol) + suggesties (gestippeld) + eigen categorie -->
            <div id="catBar" class="flex flex-wrap justify-center items-center gap-2 mb-2"></div>
            <p class="text-xs font-extrabold text-cacao/35 mb-7">Tik een categorie aan en zet er je gerechten in 👇</p>

            <div id="presetGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6"></div>

            <div id="menuList" class="space-y-2 mb-4 text-left"></div>

            <div class="flex gap-2 items-center">
                <input id="customName" type="text" placeholder="Zelf toevoegen? Bijv. Pizza Mario" class="inp !text-base flex-1">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-extrabold text-cacao/40">€</span>
                    <input id="customPrice" type="text" inputmode="decimal" placeholder="12,50" class="inp !text-base !pl-8 w-28">
                </div>
                <button id="customAdd" type="button"
                    class="shrink-0 self-stretch w-[3.8rem] rounded-2xl bg-basil text-white font-display text-2xl shadow-[0_4px_0_0_var(--color-basil-dark)] hover:translate-y-0.5 hover:shadow-[0_2px_0_0_var(--color-basil-dark)] active:translate-y-1 active:shadow-none transition-all cursor-pointer">+</button>
            </div>
            <p class="mt-3 text-xs font-extrabold text-cacao/35">Tip: tik op het icoontje voor een gerecht om een ander plaatje of eigen foto te kiezen 📷</p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
            <button data-skip type="button" class="skip-link">Doe ik later
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </button>
        </section>

        <!-- STAP: betalingen -->
        <section data-step="payment" class="step hidden w-full max-w-xl text-center">
            <div class="mascot mascot-left" aria-hidden="true">
                <div class="bubble">Zo staat je geld zo op je rekening 💶</div>
                <img src="{{ asset('stickers') }}/scanning-qr.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Cha-ching 💶</p>
            <h2 class="step-title">Hoe wil je betaald worden?</h2>
            <p class="step-sub">Koppelen doen wij samen met jou. Jij hoeft niks technisch te doen.</p>
            <div class="space-y-3">
                <button type="button" data-pay="mollie" class="choice-card">
                    <span class="text-3xl">🇳🇱</span>
                    <span class="flex-1 text-left">
                        <span class="block font-display text-xl">iDEAL <span class="text-cacao/40 text-sm font-body font-extrabold">via Mollie</span></span>
                        <span class="block text-sm font-bold text-cacao/50">Dé standaard in Nederland</span>
                    </span>
                    <span class="badge-gold">Aanbevolen</span>
                </button>
                <button type="button" data-pay="stripe" class="choice-card">
                    <span class="text-3xl">💳</span>
                    <span class="flex-1 text-left">
                        <span class="block font-display text-xl">Creditcard &amp; meer <span class="text-cacao/40 text-sm font-body font-extrabold">via Stripe</span></span>
                        <span class="block text-sm font-bold text-cacao/50">Ook Apple Pay &amp; Google Pay</span>
                    </span>
                </button>
                <button type="button" data-pay="later" class="choice-card">
                    <span class="text-3xl">🤷</span>
                    <span class="flex-1 text-left">
                        <span class="block font-display text-xl">Regel ik later</span>
                        <span class="block text-sm font-bold text-cacao/50">Prima! We helpen je er straks mee.</span>
                    </span>
                </button>
            </div>
            <div class="mt-8">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
            </div>
        </section>

        <!-- STAP: subdomein -->
        <section data-step="domain" class="step hidden w-full max-w-xl text-center">
            <div class="mascot mascot-right" aria-hidden="true">
                <div class="bubble">Het technische regelen wij 🤓</div>
                <img src="{{ asset('stickers') }}/behind-laptop.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Jouw plekje op het internet 🌐</p>
            <h2 class="step-title">Kies je bestel-adres</h2>
            <p class="step-sub">Hier komen je klanten straks bestellen.</p>
            <div class="space-y-3 text-left">
                <button type="button" data-domain="sub" class="choice-card">
                    <span class="text-3xl">⚡</span>
                    <span class="flex-1">
                        <span class="block font-display text-xl break-all"><span data-slug>jouwpizzeria</span>.bestelpagina.nl</span>
                        <span class="block text-sm font-bold text-cacao/50">Direct live, je hebt niks nodig</span>
                    </span>
                </button>
                <button type="button" data-domain="own" class="choice-card">
                    <span class="text-3xl">🏠</span>
                    <span class="flex-1">
                        <span class="block font-display text-xl">Op mijn eigen website</span>
                        <span class="block text-sm font-bold text-cacao/50">Bijv. bestellen.jouwdomein.nl, wij koppelen 'm gratis</span>
                    </span>
                </button>
            </div>
            <div id="ownDomainWrap" class="hidden mt-4 text-left">
                <label for="inpOwnDomain" class="lbl">Jouw huidige website</label>
                <input id="inpOwnDomain" type="text" placeholder="pizzeriamario.nl" class="inp">
                <p id="ownDomainPreview" class="mt-2 text-sm font-extrabold text-basil hidden">
                    ✓ Wordt: <span class="underline decoration-wavy decoration-gold"></span>
                </p>
            </div>
            <p id="domainCheck" class="mt-4 h-6 text-sm font-extrabold"></p>
            <p class="err" data-err="domain"></p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Volgende</button>
            </div>
        </section>

        <!-- STAP: huisstijl -->
        <section data-step="style" class="step hidden w-full max-w-3xl lg:max-w-4xl text-center">
            <p class="step-kicker">De finishing touch 🎨</p>
            <h2 class="step-title">Maak 'm helemaal van jou</h2>
            <p class="step-sub">Kies een template dat bij jouw zaak past, maak 'm af met jouw kleur en logo. Je ziet meteen hoe je bestelpagina eruit gaat zien.</p>

            <div class="flex flex-col lg:flex-row items-center justify-center gap-10">
                <!-- Links: de twee vragen -->
                <div class="flex-1 w-full max-w-xl">
                    <p class="lbl !ml-0 !text-center mb-3">Kies een template</p>
                    <div id="themeGrid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8"></div>

                    <p class="lbl !ml-0 !text-center mb-3">Jouw kleur</p>
                    <div id="colorGrid" class="grid grid-cols-7 gap-3 mb-8"></div>
                    <input id="customColor" type="color" class="sr-only" tabindex="-1" aria-label="Eigen kleur kiezen">

                    <p class="lbl !ml-0 !text-center mb-3">Jouw logo</p>
                    <div class="relative">
                        <label for="logoInp" class="logo-drop">
                            <div id="logoDropThumb" class="hidden w-12 h-12 rounded-full border-2 border-crema-dark overflow-hidden bg-white mx-auto mb-2">
                                <img class="w-full h-full object-cover" alt="Jouw logo">
                            </div>
                            <span id="logoDropText">Logo uploaden</span>
                            <span id="logoDropHint" class="block text-xs font-extrabold text-cacao/35 mt-1">Nog geen logo bij de hand? Mag ook later.</span>
                        </label>
                        <button id="logoRemove" type="button" title="Logo verwijderen" aria-label="Logo verwijderen" style="display:none"
                            class="absolute -top-2.5 -right-2.5 z-10 w-7 h-7 rounded-full bg-white border-2 border-crema-dark place-items-center text-sm font-extrabold text-cacao/45 shadow-sm hover:text-tomato hover:border-tomato/50 transition-colors cursor-pointer">✕</button>
                    </div>
                    <input id="logoInp" type="file" accept="image/*" class="hidden">
                </div>

                <!-- Rechts: telefoon-preview; elk template rendert zijn eigen lay-out -->
                <div class="phone-frame shrink-0" id="pvFrame">
                    <div class="phone-notch"></div>
                    <div id="pvScreen" class="flex-1 flex flex-col overflow-hidden text-left"></div>
                </div>
            </div>

            <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button data-next type="button" class="btn-primary">Bijna klaar!
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                </button>
            </div>
        </section>

        <!-- STAP: overzicht -->
        <section data-step="overview" class="step hidden w-full max-w-xl text-center">
            <div class="mascot mascot-left" aria-hidden="true">
                <div class="bubble">Kijk het rustig na, chef 😌</div>
                <img src="{{ asset('stickers') }}/holding-3-pizzas.png" alt="" loading="lazy">
            </div>
            <p class="step-kicker">Mamma mia, kijk nou 😍</p>
            <h2 class="step-title">Klopt dit allemaal?</h2>
            <p class="step-sub">Tik op een regel om iets aan te passen.</p>
            <div id="summary" class="bg-white rounded-3xl border-4 border-crema-dark shadow-[0_8px_0_0_var(--color-crema-dark)] p-5 sm:p-7 text-left space-y-1 mb-8"></div>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                <button id="submitBtn" type="button" class="btn-primary text-2xl px-12 py-5">Onboarding afronden 🚀</button>
            </div>
            <p class="mt-4 text-sm font-extrabold text-cacao/40">Daarna sta je meteen in je eigen dashboard.</p>
        </section>

    </main>

    <!-- Icoon-kiezer voor menu-items -->
    <div id="iconPicker" class="fixed inset-0 z-50 hidden items-center justify-center bg-cacao/40 backdrop-blur-sm p-5">
        <div class="bg-white rounded-3xl border-4 border-crema-dark p-6 sm:p-7 max-w-sm w-full text-center shadow-2xl animate-pop">
            <p class="font-display text-2xl mb-1">Kies een plaatje 🖼️</p>
            <p class="text-sm font-bold text-cacao/50 mb-5">Of upload een foto van je eigen gerecht.</p>
            <div id="emojiGrid" class="grid grid-cols-6 gap-1.5 mb-5"></div>
            <label class="btn-photo">
                📷 Eigen foto kiezen
                <input id="photoInp" type="file" accept="image/*" class="hidden">
            </label>
            <div>
                <button id="pickerClose" type="button" class="skip-link !mt-4">sluiten</button>
            </div>
        </div>
    </div>

    <!-- Opnieuw beginnen (alleen zichtbaar als er bewaarde antwoorden zijn) -->
    <button id="resetLink" type="button"
        class="hidden fixed bottom-4 right-4 z-40 text-xs font-extrabold text-cacao/45 bg-white/80 backdrop-blur border-2 border-crema-dark rounded-full px-4 py-2 hover:text-tomato hover:border-tomato/40 transition-colors cursor-pointer">
        🗑️ Opnieuw beginnen
    </button>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden bg-cacao text-crema font-extrabold text-sm px-5 py-3 rounded-full shadow-lg"></div>

    <!-- Confetti -->
    <canvas id="confetti" class="pointer-events-none fixed inset-0 z-50 hidden"></canvas>

    <script>window.PP_AUTH = @json(auth()->check());</script>
    <script>window.PP_SAVED = @json(auth()->check() ? auth()->user()->onboarding : null);</script>
    <script src="{{ asset('assets/onboarding/app.js') }}?v={{ filemtime(public_path('assets/onboarding/app.js')) }}"></script>
</body>
</html>
