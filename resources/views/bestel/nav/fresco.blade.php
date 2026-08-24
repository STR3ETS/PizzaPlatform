<!-- FRESCO-nav: speels, wit, ronde pillen -->
<nav class="b-nav">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-4">
        <a href="{{ route('bestel.home', $slug) }}" class="flex items-center gap-2.5 min-w-0">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-10 w-10 rounded-full object-cover shrink-0">
            @else
                <span class="w-9 h-9 rounded-full grid place-items-center text-white shrink-0" style="background: var(--accent)"><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i></span>
            @endif
            <span class="b-kop text-xl truncate">{{ $naam }}</span>
        </a>
        <div class="flex-1"></div>
        <div class="hidden sm:flex items-center gap-1.5 text-sm mr-2">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link rounded-full px-3 py-1.5 {{ request()->routeIs('bestel.home') ? 'actief' : '' }}" style="{{ request()->routeIs('bestel.home') ? 'background: var(--accent-zacht)' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link rounded-full px-3 py-1.5 {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}" style="{{ request()->routeIs('bestel.menu') ? 'background: var(--accent-zacht)' : '' }}">Menukaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link rounded-full px-3 py-1.5 {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}" style="{{ request()->routeIs('bestel.contact') ? 'background: var(--accent-zacht)' : '' }}">Contact</a>
        </div>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-4 !text-sm shrink-0">Bestel nu</a>
    </div>
    <div class="sm:hidden flex gap-5 px-4 pb-2.5 text-sm">
        <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">Menukaart</a>
        <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
    </div>
</nav>
