{{-- Zaak afmaken: na de registratie (deel 1 van de onboarding) ben je direct ingelogd en
     doorloop je de laatste stappen hier, in een venster boven je (wazige) dashboard.
     Niet weg te klikken: pas als de zaak compleet is gaat het dashboard open.
     De stappen draaien op dezelfde wizard-JS als /onboarding (assets/onboarding/app.js),
     in modal-modus: alleen de vervolgstappen, en na afronden herlaadt de pagina. --}}
<div id="setupOverlay" class="fixed inset-0 z-[120] overflow-y-auto bg-cacao/45 backdrop-blur-md" role="dialog" aria-modal="true" aria-labelledby="setupTitel">
    <div class="min-h-full grid place-items-center p-4 md:p-8">
        <div class="w-full max-w-5xl">
            <div class="dash-card rise !p-0 overflow-hidden !shadow-2xl">

                {{-- Compacte kop: alleen de titel, de teller en de stappenbalk --}}
                <div class="px-6 md:px-8 pt-5 pb-5 border-b border-crema-dark bg-white">
                    <div class="flex items-center justify-between gap-4">
                        <h2 id="setupTitel" class="font-display text-lg md:text-xl leading-tight">Je zaak afmaken</h2>
                        <span id="stepCounter" class="text-xs font-semibold tracking-wide bg-crema border border-crema-dark px-3 py-1 rounded-full text-cacao/60 shrink-0"></span>
                    </div>
                    {{-- Stappenbalk: een segment per stap met de naam eronder (gevuld door de wizard-JS) --}}
                    <div id="progressWrap" class="mt-4 hidden">
                        <div id="stapBalk" class="grid gap-2"></div>
                    </div>
                </div>

                {{-- De stappen --}}
                <main id="wizard" class="relative px-5 py-7 md:px-10 md:py-9 flex justify-center">

                    <!-- STAP: openingstijden -->
                    <section data-step="hours" class="step hidden w-full max-w-xl text-center">
                        <p class="step-kicker"><i class="fa-solid fa-fire" aria-hidden="true"></i> Oven aan</p>
                        <h3 class="step-title">Wanneer kunnen mensen bestellen?</h3>
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
                            <button data-next type="button" class="btn-primary">Volgende</button>
                        </div>
                    </section>

                    <!-- STAP: spaarpunten -->
                    <section data-step="punten" class="step hidden w-full max-w-xl text-center">
                        <p class="step-kicker"><i class="fa-solid fa-star" aria-hidden="true"></i> Vaste klanten</p>
                        <h3 class="step-title">Klanten laten sparen?</h3>
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
                        <h3 class="step-title">Kies je bestel-adres</h3>
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
                                @if(! auth()->user()->abonnement_actief)
                                    <span class="badge-gold">Met abonnement</span>
                                @endif
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

                    <!-- STAP: huisstijl: templates op een rij, dan de kleuren, dan het logo -->
                    <section data-step="style" class="step hidden w-full text-center">
                        <p class="step-kicker"><i class="fa-solid fa-palette" aria-hidden="true"></i> De finishing touch</p>
                        <h3 class="step-title">Maak 'm helemaal van jou</h3>
                        <p class="step-sub">Kies een template dat bij jouw zaak past en maak 'm af met jouw kleur en logo. Wisselen kan later altijd nog.</p>

                        <div class="text-left space-y-9">
                            <div>
                                <p class="lbl !ml-0 mb-3">Jouw template</p>
                                <div id="themeGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                            </div>

                            <div>
                                <p class="lbl !ml-0 mb-3">Jouw kleurstijl</p>
                                <div id="colorGrid"></div>
                            </div>

                            <div>
                                <p class="lbl !ml-0 mb-3">Jouw logo</p>
                                <div class="relative">
                                    <label for="logoInp" class="logo-drop !flex flex-col items-center justify-center min-h-[7.5rem]">
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

                        <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                            <button data-back type="button" class="btn-primary btn-grey">Terug</button>
                            <button id="submitBtn" type="button" class="btn-primary !text-xl !px-10 !py-4">Zaak afronden</button>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-cacao/40">Daarna gaat je dashboard open. Alles is later nog aan te passen.</p>
                    </section>

                </main>
            </div>

            <div class="mt-4 flex items-center justify-center gap-4 text-xs font-semibold text-white/80">
                <span>Ingelogd als {{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="underline hover:text-gold transition-colors cursor-pointer">Uitloggen</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- De melding onderin, boven het venster --}}
<div id="toast" class="fixed bottom-6 left-1/2 z-[150] bg-cacao text-crema font-semibold text-sm px-5 py-3 rounded-full shadow-lg"></div>

<script>window.PP_MODAL = true;</script>
<script>window.PP_AUTH = true;</script>
<script>window.PP_PROEF = @json(! auth()->user()->abonnement_actief);</script>
<script>window.PP_KLEUREN = @json(\App\Http\Controllers\BestelController::KLEUREN);</script>
<script>window.PP_SAVED = @json(auth()->user()->onboarding);</script>
