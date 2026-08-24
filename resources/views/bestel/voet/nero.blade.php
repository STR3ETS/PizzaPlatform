<!-- NERO-footer: gecentreerd en statig, met monogram en hairlines -->
<footer class="mt-16 text-center" style="background: var(--kaart)">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="w-14 h-14 rounded-full grid place-items-center mx-auto mb-4" style="border: 1px solid var(--prijs); color: var(--prijs); font-family: Georgia, serif; font-size: 1.3rem">{{ mb_strtoupper(mb_substr($naam, 0, 1)) }}</div>
        <p class="b-kop text-2xl">{{ $naam }}</p>
        <div class="w-20 h-px mx-auto my-4" style="background: var(--prijs)"></div>
        <div class="text-sm opacity-65 space-y-1">
            @if($adres)<p>{{ $adres }}</p>@endif
            @if($telefoon)<p><a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
            @if($vandaag['open'])<p>Vanavond geopend van {{ $vandaag['van'] }} tot {{ $vandaag['tot'] }}</p>@else<p>Vandaag gesloten</p>@endif
        </div>
        <div class="flex justify-center gap-6 mt-6 text-xs uppercase tracking-[.25em]">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link">De kaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link">Contact</a>
        </div>
        <p class="text-[11px] opacity-40 mt-8">&copy; {{ date('Y') }} {{ $naam }}</p>
    </div>
</footer>
