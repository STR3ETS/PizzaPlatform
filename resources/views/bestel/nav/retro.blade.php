<!-- RETRO-nav: zwevende afgeronde balk met badge-logo -->
<div class="sticky top-0 z-30 px-3 pt-3">
    <nav class="max-w-4xl mx-auto rounded-full px-4 py-2 flex items-center gap-3" style="background: var(--kaart); box-shadow: 0 6px 0 0 rgb(91 58 33 / .18)">
        <a href="{{ route('bestel.home', $slug) }}" class="flex items-center gap-2.5 min-w-0">
            <span class="w-9 h-9 rounded-full grid place-items-center shrink-0 -rotate-6" style="border: 3px double var(--tekst); background: var(--pagina)">
                @if($logo)<img src="{{ $logo }}" alt="" class="w-full h-full rounded-full object-cover">@else <i class="fa-solid fa-pizza-slice text-sm" aria-hidden="true"></i> @endif
            </span>
            <span class="b-kop truncate">{{ $naam }}</span>
        </a>
        <div class="flex-1"></div>
        <div class="hidden sm:flex items-center gap-4 text-sm mr-1">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">Menukaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
        </div>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-1.5 !px-4 !text-sm shrink-0">Bestel</a>
    </nav>
</div>
