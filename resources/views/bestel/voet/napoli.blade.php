<!-- NAPOLI-footer: papieren kader met vlag-stripe -->
<footer class="mt-14 max-w-3xl mx-auto px-4 pb-10 w-full">
    <div class="p-8 text-center relative" style="background: var(--kaart); border: 2px solid var(--tekst); outline: 1px solid var(--tekst); outline-offset: -6px">
        <p class="b-kop text-2xl">{{ $naam }}</p>
        <p class="text-[10px] tracking-[.3em] uppercase opacity-55 mt-1">Grazie mille e arrivederci</p>
        <div class="max-w-40 mx-auto my-4" style="height: .4rem; background: repeating-linear-gradient(90deg, #C0392B 0 2rem, transparent 2rem 4rem, #2F8F46 4rem 6rem, transparent 6rem 8rem)"></div>
        <div class="text-sm opacity-70 space-y-1 italic">
            @if($adres)<p>{{ $adres }}</p>@endif
            @if($telefoon)<p><a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
            <p>{{ $vandaag['open'] ? 'Oggi aperto: ' . $vandaag['van'] . ' - ' . $vandaag['tot'] : 'Vandaag gesloten' }}</p>
        </div>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-5 !text-sm mt-5">Bestel online</a>
        <p class="text-[11px] opacity-40 mt-5">&copy; {{ date('Y') }} {{ $naam }}</p>
    </div>
</footer>
