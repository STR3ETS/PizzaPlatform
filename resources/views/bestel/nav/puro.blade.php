<!-- PURO-nav: minimaal, hairline, veel lucht -->
<nav class="b-nav !shadow-none" style="border-bottom: 1px solid rgb(0 0 0 / .08)">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center gap-6">
        <a href="{{ route('bestel.home', $slug) }}" class="flex items-center gap-2.5 min-w-0">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-8 w-8 rounded-full object-cover shrink-0">
            @endif
            <span class="b-kop text-lg truncate">{{ $naam }}</span>
        </a>
        <div class="flex-1"></div>
        <div class="flex items-center gap-6 text-sm">
            <a href="{{ route('bestel.home', $slug) }}" class="b-nav-link !opacity-100 {{ request()->routeIs('bestel.home') ? 'underline underline-offset-8' : 'opacity-60' }}" style="text-decoration-color: var(--accent); text-decoration-thickness: 3px">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-nav-link !opacity-100 {{ request()->routeIs('bestel.menu') ? 'underline underline-offset-8' : 'opacity-60' }}" style="text-decoration-color: var(--accent); text-decoration-thickness: 3px">Menukaart</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-nav-link !opacity-100 hidden sm:inline {{ request()->routeIs('bestel.contact') ? 'underline underline-offset-8' : 'opacity-60' }}" style="text-decoration-color: var(--accent); text-decoration-thickness: 3px">Contact</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="font-black" style="color: var(--accent)">Bestellen &rarr;</a>
        </div>
    </div>
</nav>
