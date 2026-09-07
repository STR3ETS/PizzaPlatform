@php
    $siteMerk = 'MijnPizzeria';
@endphp
{{-- Voet: donker, uitgebreid, met het logo als anker --}}
<footer style="background:var(--color-cacao)">
    <div class="max-w-6xl mx-auto px-5 pt-14 pb-12 text-white">
        <div class="grid gap-12 lg:grid-cols-12">
            {{-- Merk: logo, belofte en het subdomein dat elke zaak krijgt --}}
            <div class="lg:col-span-4">
                <a href="/" class="inline-block">
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteMerk }}" class="h-28 sm:h-32 w-auto drop-shadow-lg">
                </a>
                <p class="mt-5 text-sm font-bold text-white/55 max-w-sm">Jouw eigen bestelpagina, spaarprogramma en dozen met jouw opdruk. Zonder commissie per bestelling, maandelijks opzegbaar.</p>
                <ul class="mt-5 space-y-2 text-sm font-semibold text-white/50">
                    <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/25 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Geen technische kennis nodig</li>
                    <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/25 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Je menukaart zet je zelf in elkaar</li>
                    <li class="flex items-center gap-2.5"><span class="w-5 h-5 rounded-full bg-basil/25 grid place-items-center text-basil text-[10px]"><i class="fa-solid fa-check" aria-hidden="true"></i></span> Het geld gaat rechtstreeks naar jou</li>
                </ul>
                <div class="mt-6 flex items-center gap-2.5">
                    @foreach([['fa-instagram', 'Instagram'], ['fa-facebook-f', 'Facebook'], ['fa-tiktok', 'TikTok']] as [$icoon, $label])
                        <a href="#" aria-label="{{ $label }}, volgt binnenkort" class="w-10 h-10 rounded-xl bg-white/5 border border-white/15 grid place-items-center text-white/60 hover:text-white hover:bg-white/10 transition-colors">
                            <i class="fa-brands {{ $icoon }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                    <span class="ml-1 text-xs font-semibold text-white/30">Volgt binnenkort</span>
                </div>
            </div>

            {{-- Linkkolommen --}}
            <div class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-x-8 gap-y-10 lg:pt-3">
                <div>
                    <p class="font-display text-lg mb-3.5">Product</p>
                    <ul class="space-y-2.5 text-sm font-bold text-white/55">
                        <li><a href="/#functies" class="hover:text-white transition-colors">Functies</a></li>
                        <li><a href="/#templates" class="hover:text-white transition-colors">Templates</a></li>
                        <li><a href="/#spaarprogramma" class="hover:text-white transition-colors">Spaarprogramma</a></li>
                        <li><a href="/#prijzen" class="hover:text-white transition-colors">Prijzen</a></li>
                        <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="/#functies" class="hover:text-white transition-colors">Pizza Coach</a></li>
                        <li><a href="/#functies" class="hover:text-white transition-colors">Dozen met opdruk</a></li>
                        <li><a href="/#functies" class="hover:text-white transition-colors">Live bezorgstatus</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-display text-lg mb-3.5">Proef de stijlen</p>
                    <ul class="space-y-2.5 text-sm font-bold text-white/55">
                        <li><a href="/bestellen/pizzeriasole?thema=template1&kleur=%23E63946" target="_blank" rel="noopener" class="hover:text-white transition-colors"><i class="fa-solid fa-moped w-4 mr-1" aria-hidden="true"></i> Presto, licht en snel</a></li>
                        <li><a href="/bestellen/pizzeriasole?thema=template2&kleur=%237D1D3F" target="_blank" rel="noopener" class="hover:text-white transition-colors"><i class="fa-solid fa-wine-glass w-4 mr-1" aria-hidden="true"></i> Notte, klassiek</a></li>
                        <li><a href="/bestellen/pizzeriasole?thema=template3&kleur=%231F2937" target="_blank" rel="noopener" class="hover:text-white transition-colors"><i class="fa-solid fa-bolt w-4 mr-1" aria-hidden="true"></i> Forza, bold</a></li>
                        <li><a href="/bestellen/pizzeriasole?thema=template4&kleur=%23F5B301" target="_blank" rel="noopener" class="hover:text-white transition-colors"><i class="fa-solid fa-camera w-4 mr-1" aria-hidden="true"></i> Giro, met foto's</a></li>
                        <li class="pt-1"><span class="text-white/35">Allemaal live, klik gerust</span></li>
                    </ul>
                </div>
                <div>
                    <p class="font-display text-lg mb-3.5">Voor jouw zaak</p>
                    <ul class="space-y-2.5 text-sm font-bold text-white/55">
                        <li><a href="/onboarding" class="hover:text-white transition-colors">Gratis starten</a></li>
                        <li><a href="/login" class="hover:text-white transition-colors">Inloggen</a></li>
                        <li><a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="hover:text-white transition-colors">Voorbeeld bestelpagina</a></li>
                        <li><a href="/bestellen/pizzeriasole/afrekenen?voorbeeld=1" target="_blank" rel="noopener" class="hover:text-white transition-colors">Voorbeeld afrekenen</a></li>
                        <li><a href="/bestellen/pizzeriasole/bestelling?voorbeeld=1" target="_blank" rel="noopener" class="hover:text-white transition-colors">Voorbeeld bezorgstatus</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-display text-lg mb-3.5">Praktisch</p>
                    <ul class="space-y-2.5 text-sm font-bold text-white/55">
                        <li><span class="text-white/35">Contact, volgt binnenkort</span></li>
                        <li><a href="#" class="hover:text-white transition-colors">Algemene voorwaarden</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><span class="text-white/35">KVK, volgt binnenkort</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Betaalstrook: wat klanten van de pizzeria kunnen gebruiken --}}
        <div class="relative mt-14 rounded-2xl bg-white/5 border border-white/10 px-6 py-5 flex flex-wrap items-center gap-x-8 gap-y-4">
            <p class="text-sm font-semibold text-white/70">Klanten betalen veilig via Stripe</p>
            <div class="flex flex-wrap items-center gap-4 text-3xl text-white/50">
                <i class="fa-brands fa-ideal" title="iDEAL" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-apple-pay" title="Apple Pay" aria-hidden="true"></i>
                <i class="fa-brands fa-google-pay" title="Google Pay" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-visa" title="Visa" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-mastercard" title="Mastercard" aria-hidden="true"></i>
            </div>
            <p class="ml-auto text-sm font-semibold text-white/45">Gemaakt in Nederland</p>
        </div>
    </div>

    {{-- Onderbalk --}}
    <div class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-5 py-5 flex flex-col sm:flex-row items-center gap-2.5 justify-between text-xs font-semibold text-white/35">
            <p>&copy; {{ date('Y') }} {{ $siteMerk }}. Alle rechten voorbehouden.</p>
            <p><span class="text-white/50">mijnpizzeria.nl</span> en <span class="text-white/50">mijnpizzeria.com</span></p>
            <p>Veilig betalen via Stripe</p>
        </div>
    </div>
</footer>
