<!-- FRESCO-footer: warm, drie kolommen, ronde CTA -->
<footer class="mt-14" style="background: var(--kaart)">
    <div class="max-w-5xl mx-auto px-4 py-10 grid sm:grid-cols-3 gap-8 text-sm">
        <div>
            <p class="b-kop text-xl mb-2"><i class="fa-solid fa-pizza-slice" aria-hidden="true" style="color: var(--accent)"></i> {{ $naam }}</p>
            <p class="opacity-70">Vers bereide pizza's{{ $plaats ? ' in ' . $plaats : '' }}. Direct bij ons besteld, dus meteen in onze keuken.</p>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-4 !text-sm mt-4">Online bestellen</a>
        </div>
        <div>
            <p class="b-kop text-lg mb-2">Openingstijden</p>
            @foreach($dagen as $dag)
                <div class="flex justify-between gap-4 {{ $dag['open'] ? 'opacity-80' : 'opacity-50' }}">
                    <span>{{ $dag['naam'] }}</span>
                    <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                </div>
            @endforeach
        </div>
        <div>
            <p class="b-kop text-lg mb-2">Contact</p>
            @if($adres)<p class="opacity-80"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $adres }}</p>@endif
            @if($telefoon)<p class="mt-1"><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:{{ $telefoon }}" class="underline underline-offset-2 opacity-80">{{ $telefoon }}</a></p>@endif
        </div>
    </div>
    <div class="border-t px-4 py-4 text-center text-xs opacity-55" style="border-color: color-mix(in srgb, var(--tekst) 12%, transparent)">
        &copy; {{ date('Y') }} {{ $naam }}. Alle rechten voorbehouden.
    </div>
</footer>
