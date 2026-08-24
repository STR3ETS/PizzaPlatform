<!-- NAPOLI-nav: trattoria, vlag-stripe boven, logo in het midden tussen de links -->
<nav class="b-nav">
    <div style="height: .4rem; background: repeating-linear-gradient(90deg, #C0392B 0 3rem, #FFFDF6 3rem 6rem, #2F8F46 6rem 9rem)"></div>
    <div class="max-w-5xl mx-auto px-4 py-3 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
        <div class="hidden sm:flex items-center gap-5 text-sm">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link italic {{ request()->routeIs('bestel.home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link italic {{ request()->routeIs('bestel.menu') ? 'actief' : '' }}">La carta</a>
        </div>
        <a href="{{ route('bestel.home', $slug) }}" class="text-center min-w-0">
            <span class="b-kop text-xl sm:text-2xl block truncate">{{ $naam }}</span>
            <span class="text-[10px] tracking-[.3em] uppercase opacity-55 block">Cucina Italiana</span>
        </a>
        <div class="hidden sm:flex items-center justify-end gap-4 text-sm">
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link italic {{ request()->routeIs('bestel.contact') ? 'actief' : '' }}">Contact</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-1.5 !px-4 !text-sm">Bestel</a>
        </div>
    </div>
    <div class="sm:hidden flex items-center justify-center gap-5 px-4 pb-2.5 text-sm">
        <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link italic">Home</a>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link italic">La carta</a>
        <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link italic">Contact</a>
    </div>
</nav>
