<!-- RETRO-footer: golfrand en badge, gecentreerd -->
<footer class="mt-14">
    <svg class="block w-full h-9" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true" style="color: var(--kaart)"><path d="M0 20 Q 150 0 300 20 T 600 20 T 900 20 T 1200 20 V 40 H 0 Z" fill="currentColor"/></svg>
    <div style="background: var(--kaart)">
        <div class="max-w-3xl mx-auto px-4 pb-10 pt-2 text-center">
            <div class="inline-grid place-content-center w-24 h-24 rounded-full -rotate-6 mb-3" style="border: 4px double var(--tekst); background: var(--pagina)">
                <i class="fa-solid fa-pizza-slice text-lg" aria-hidden="true" style="color: var(--accent)"></i>
                <span class="b-kop text-[11px] leading-tight px-2">{{ $naam }}</span>
            </div>
            <div class="text-sm opacity-70 space-y-1">
                @if($adres)<p><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $adres }}</p>@endif
                @if($telefoon)<p><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
                <p>{{ $vandaag['open'] ? 'Vandaag draaien de ovens van ' . $vandaag['van'] . ' tot ' . $vandaag['tot'] : 'Vandaag gesloten' }}</p>
            </div>
            <div class="flex justify-center gap-5 mt-5 text-sm font-extrabold">
                <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link">Home</a>
                <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link">Menukaart</a>
                <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link">Contact</a>
            </div>
            <p class="text-[11px] opacity-40 mt-6">&copy; {{ date('Y') }} {{ $naam }}, al jaren lekker</p>
        </div>
    </div>
</footer>
