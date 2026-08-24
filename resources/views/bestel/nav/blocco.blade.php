<!-- BLOCCO-nav: zwart, hard, uppercase, met de marquee erboven -->
<div class="b-marquee" aria-hidden="true"><div>
    @for($i = 0; $i < 2; $i++)
        Vers uit de oven ★ {{ $alleenAfhalen ? 'Snel afhalen' : 'Bezorgen en afhalen' }} ★ Direct bij de zaak bestellen ★ {{ $naam }} ★&nbsp;
    @endfor
</div></div>
<nav class="b-nav !shadow-none" style="background: #111; color: #fff; border-bottom: 4px solid var(--accent)">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-4">
        <a href="{{ route('bestel.home', $slug) }}" class="flex items-center gap-2.5 min-w-0">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo {{ $naam }}" class="h-9 w-9 object-cover shrink-0" style="border: 2px solid var(--accent)">
            @endif
            <span class="b-kop text-lg truncate" style="color: var(--accent)">{{ $naam }}</span>
        </a>
        <div class="flex-1"></div>
        <div class="hidden sm:flex items-center gap-5 text-xs font-black uppercase tracking-widest mr-2">
            <a href="{{ route('bestel.home', $slug) }}" class="{{ request()->routeIs('bestel.home') ? '' : 'opacity-60' }}" style="{{ request()->routeIs('bestel.home') ? 'color: var(--accent)' : '' }}">Home</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="{{ request()->routeIs('bestel.menu') ? '' : 'opacity-60' }}" style="{{ request()->routeIs('bestel.menu') ? 'color: var(--accent)' : '' }}">Menu</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="{{ request()->routeIs('bestel.contact') ? '' : 'opacity-60' }}" style="{{ request()->routeIs('bestel.contact') ? 'color: var(--accent)' : '' }}">Contact</a>
        </div>
        <a href="{{ route('bestel.menu', $slug) }}" class="shrink-0 font-black uppercase text-sm px-4 py-2" style="background: var(--accent); color: #111; border: 2px solid var(--accent)">Bestel nu</a>
    </div>
</nav>
