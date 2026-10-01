{{-- Zaak afmaken: na de registratie (deel 1 van de onboarding) ben je direct ingelogd en
     doorloop je de laatste stappen hier, in een venster boven je (wazige) dashboard.
     Niet weg te klikken: pas als de zaak compleet is gaat het dashboard open.
     De stappen draaien op dezelfde wizard-JS als /onboarding (assets/onboarding/app.js),
     in modal-modus: alleen de vervolgstappen, en na afronden herlaadt de pagina.
     De stappen zelf staan als partials in resources/views/onboarding/. --}}
<link rel="stylesheet" href="{{ asset('assets/stijl/onboarding.css') }}?v={{ filemtime(public_path('assets/stijl/onboarding.css')) }}">
<div id="setupOverlay" class="fixed inset-0 z-[120] overflow-y-auto" style="background:rgb(0 0 0 / .55); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px)" role="dialog" aria-modal="true" aria-labelledby="setupTitel">
    <div class="min-h-full grid place-items-center p-3 md:p-6">
        <div class="w-full max-w-5xl">
            <div class="vlak wizard-vlak rise" style="width:100%">

                {{-- Compacte kop: de titel, de teller en de stappenbalk --}}
                <div class="wizard-kop" style="border-bottom:1px solid var(--line); flex-wrap:wrap">
                    <h2 id="setupTitel" class="venster-titel">Je zaak afmaken</h2>
                    <span id="stepCounter" class="tag shrink-0"></span>
                    {{-- Stappenbalk: een segment per stap met de naam eronder (gevuld door de wizard-JS) --}}
                    <div id="progressWrap" class="hidden w-full mt-3">
                        <div id="stapBalk" class="grid gap-2"></div>
                    </div>
                </div>

                {{-- De stappen --}}
                <main id="wizard" class="wizard wizard-lijf">

                    @include('onboarding.stap-hours', ['terug' => false])
                    @include('onboarding.stap-punten')
                    @include('onboarding.stap-domain')

                    <!-- STAP: huisstijl: templates op een rij, dan de kleuren, dan het logo -->
                    <section data-step="style" class="step hidden w-full text-center">
                        <p class="label">De finishing touch</p>
                        <h2 class="stap-titel">Maak 'm helemaal van jou</h2>
                        <p class="lead stap-sub">Kies een template dat bij jouw zaak past en maak 'm af met jouw kleur en logo. Wisselen kan later altijd nog.</p>

                        <div class="text-left flex flex-col gap-9">
                            <div>
                                <p class="label mb-3">Jouw template</p>
                                <div id="themeGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                            </div>

                            <div>
                                <p class="label mb-3">Jouw kleurstijl</p>
                                <div id="colorGrid"></div>
                            </div>

                            <div>
                                <p class="label mb-3">Jouw logo</p>
                                <div class="relative">
                                    <label for="logoInp" class="logo-drop !flex flex-col items-center justify-center min-h-[7.5rem] cursor-pointer">
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

                        <div class="stap-knoppen">
                            <button data-back type="button" class="btn line cursor-pointer">Terug</button>
                            <button id="submitBtn" type="button" class="btn dark cursor-pointer" style="min-height:52px; padding:0 34px; font-size:15px">Zaak afronden</button>
                        </div>
                        <p class="small mt-4">Daarna gaat je dashboard open. Alles is later nog aan te passen.</p>
                    </section>

                </main>
            </div>

            <div class="mt-4 flex items-center justify-center gap-4 text-xs" style="color:rgba(255,255,255,.8)">
                <span>Ingelogd als {{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link-knop cursor-pointer" style="color:#fff">Uitloggen</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- De melding onderin, boven het venster --}}
<div id="toast" class="fixed bottom-6 left-1/2 z-[150] text-sm px-5 py-3 rounded-full"></div>

<script>window.PP_MODAL = true;</script>
<script>window.PP_AUTH = true;</script>
<script>window.PP_PROEF = @json(! auth()->user()->abonnement_actief);</script>
<script>window.PP_KLEUREN = @json(\App\Http\Controllers\BestelController::KLEUREN);</script>
<script>window.PP_SAVED = @json(auth()->user()->onboarding);</script>
