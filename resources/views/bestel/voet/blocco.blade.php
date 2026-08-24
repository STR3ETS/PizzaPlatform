<!-- BLOCCO-footer: zwart blok met mega-naam -->
<footer class="mt-14" style="background: #111; color: #fff">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <p class="b-kop leading-none" style="font-size: clamp(2.4rem, 9vw, 5.5rem); color: transparent; -webkit-text-stroke: 2px var(--accent)">{{ $naam }}</p>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-6 text-xs font-black uppercase tracking-widest">
            <a href="{{ route('bestel.home', $slug) }}" style="color: var(--accent)">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" style="color: var(--accent)">Menu</a>
            <a href="{{ route('bestel.contact', $slug) }}" style="color: var(--accent)">Contact</a>
            <span class="opacity-50">{{ $vandaag['open'] ? 'Vandaag ' . $vandaag['van'] . ' - ' . $vandaag['tot'] : 'Vandaag dicht' }}</span>
            @if($telefoon)<a href="tel:{{ $telefoon }}" class="opacity-50 underline">{{ $telefoon }}</a>@endif
        </div>
        <p class="text-[11px] font-black uppercase opacity-40 mt-6">@if($adres){{ $adres }} ★ @endif&copy; {{ date('Y') }} {{ $naam }}</p>
    </div>
</footer>
