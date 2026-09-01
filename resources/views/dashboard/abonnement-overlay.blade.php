{{-- Abonnement verplicht: het dashboard blijft wazig op de achtergrond staan en dit blok is niet weg te klikken.
     Weghalen via devtools heeft geen zin: de actie-routes weigeren ook server-side (middleware 'abonnement')
     en dashboard.js herlaadt de pagina zodra de overlay verdwijnt. --}}
@php
    $abDomein = auth()->user()->slug . '.' . config('app.centraal_domein', 'mijnpizzeria.nl');
    $abNaam = auth()->user()->onboarding['name'] ?? 'je zaak';
    $abProefVoorbij = auth()->user()->proef_tot !== null && auth()->user()->proef_tot->isPast();
@endphp
<div id="abonnementOverlay" class="fixed inset-0 z-[130] overflow-y-auto bg-cacao/45 backdrop-blur-md">
    <div class="min-h-full grid place-items-center p-4 md:p-8">
        <div class="w-full max-w-3xl">
            <div class="dash-card rise !p-0 overflow-hidden !shadow-2xl">
                <div class="grid md:grid-cols-[1fr_24rem]">
                    <div class="p-6 md:p-8">
                        <p class="step-kicker !text-left">{{ $abProefVoorbij ? 'Je proefmaand zit erop 🕒' : 'Bijna live 🚀' }}</p>
                        <h2 class="font-display text-2xl md:text-3xl leading-tight">{{ $abProefVoorbij ? 'Tijd voor je abonnement' : 'Jouw abonnement' }}</h2>
                        <p class="text-sm font-extrabold text-cacao/55 mt-2">{{ $abProefVoorbij ? $abNaam . ' staat helemaal klaar. Start je abonnement en ga direct verder waar je gebleven was.' : $abNaam . ' staat klaar. Met je abonnement gaat je bestelpagina live en kun je meteen bestellingen ontvangen.' }}</p>
                        <ul class="space-y-2.5 text-sm font-extrabold mt-5">
                            @foreach([
                                'Eigen bestelpagina op ' . $abDomein,
                                'Onbeperkt bestellingen, zonder commissie per bestelling',
                                'Dashboard met bestellingen, menukaart en live status voor je klant',
                                'Spaarprogramma met QR op de doos, zo komen klanten terug',
                                'Wij helpen je met alles instellen',
                            ] as $abPunt)
                                <li class="flex items-start gap-2.5">
                                    <span class="shrink-0 w-5 h-5 rounded-full bg-basil text-white grid place-items-center text-[11px] leading-none mt-0.5">✓</span>
                                    <span>{{ $abPunt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="bg-crema-dark/60 p-6 md:p-8 flex flex-col justify-center items-center text-center border-t-4 md:border-t-0 md:border-l-4 border-crema-dark">
                        <img src="{{ asset('stickers') }}/scanning-qr.png" alt="" loading="lazy" class="w-24 -mt-2 mb-3">
                        <p class="font-display text-4xl leading-none">&euro; 24,95</p>
                        <p class="text-sm font-extrabold text-cacao/50 mt-1">per maand, opzegbaar per maand</p>
                        <form method="POST" action="{{ route('abonnement.starten') }}" class="mt-5 w-full">
                            @csrf
                            <button type="submit" class="btn-primary w-full !py-3">Abonnement starten 🍕</button>
                        </form>
                        <p class="text-xs font-extrabold text-cacao/40 mt-3">Betalen gaat straks veilig via Stripe. Tijdens de testfase wordt er nog niks afgeschreven.</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-center gap-4 text-xs font-extrabold text-white/80">
                <span>Ingelogd als {{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="underline hover:text-gold transition-colors cursor-pointer">Uitloggen</button>
                </form>
            </div>
        </div>
    </div>
</div>
