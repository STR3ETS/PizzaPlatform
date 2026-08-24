<!-- PURO-footer: minimaal, één strakke regel -->
<footer class="mt-16" style="border-top: 1px solid rgb(0 0 0 / .08)">
    <div class="max-w-5xl mx-auto px-4 py-8 flex flex-col sm:flex-row sm:items-center gap-3 text-sm">
        <p class="b-kop">{{ $naam }}</p>
        <span class="hidden sm:inline opacity-30">|</span>
        @if($adres)<p class="opacity-60">{{ $adres }}</p>@endif
        @if($telefoon)<p class="opacity-60"><a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
        <div class="flex-1"></div>
        <p class="opacity-60">{{ $vandaag['open'] ? 'Vandaag ' . $vandaag['van'] . ' - ' . $vandaag['tot'] : 'Vandaag gesloten' }}</p>
        <a href="{{ route('bestel.menu', $slug) }}" class="font-black" style="color: var(--accent)">Bestellen &rarr;</a>
    </div>
</footer>
