<!-- NERO-nav: klassiek restaurant, gecentreerd en statig -->
<nav class="b-nav" style="border-bottom: 1px solid color-mix(in srgb, var(--prijs) 35%, transparent)">
    <div class="max-w-5xl mx-auto px-4 pt-4 pb-3 text-center">
        <a href="{{ route('bestel.home', $slug) }}" class="inline-flex items-center gap-2.5">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-9 w-9 rounded-full object-cover">
            @endif
            <span class="b-kop text-2xl tracking-wide">{{ $naam }}</span>
        </a>
        <div class="flex items-center justify-center gap-6 mt-2.5 text-sm">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link uppercase tracking-widest !text-xs {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link uppercase tracking-widest !text-xs {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">De kaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link uppercase tracking-widest !text-xs {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-1.5 !px-4 !text-sm">Bestel nu</a>
        </div>
    </div>
</nav>
