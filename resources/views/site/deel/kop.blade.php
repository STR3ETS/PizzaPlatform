{{-- Topbar: de sterkste belofte plus een bewijslink, boven alle site-pagina's --}}
<div style="background:var(--color-cacao)">
    <div class="max-w-6xl mx-auto px-5 py-2 flex items-center justify-between gap-4 text-xs font-semibold text-white/75">
        <a href="/onboarding" class="flex items-center gap-2 hover:text-white transition-colors">
            <i class="fa-solid fa-gift text-gold" aria-hidden="true"></i>
            <span>Probeer MijnPizzeria 30 dagen gratis, zonder betaalgegevens</span>
            <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
        </a>
        <a href="/bestellen/pizzeriasole" target="_blank" rel="noopener" class="hidden sm:flex items-center gap-2 hover:text-white transition-colors">
            <i class="fa-solid fa-eye" aria-hidden="true"></i>
            <span>Bekijk een live bestelpagina</span>
        </a>
    </div>
</div>

{{-- Kop: logo, navigatie en de twee knoppen die er echt toe doen --}}
<header class="sticky top-0 z-40 border-b border-crema-dark" style="background:color-mix(in srgb, var(--color-crema) 92%, transparent); backdrop-filter:blur(8px)">
    <div class="max-w-6xl mx-auto px-5 h-[6rem] flex items-center gap-6">
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <img src="logo.png" alt="MijnPizzeria" class="max-h-22">
        </a>

        <nav class="hidden md:flex items-center gap-1 mx-auto">
            @foreach([['/#functies', 'Functies'], ['/#templates', 'Templates'], ['/#spaarprogramma', 'Spaarprogramma'], ['/#prijzen', 'Prijzen'], ['/blog', 'Blog']] as [$link, $label])
                <a href="{{ $link }}" class="px-3.5 py-2 rounded-full text-sm font-semibold {{ $link === '/blog' && request()->is('blog*') ? 'text-cacao bg-crema-dark' : 'text-cacao/60' }} hover:text-cacao hover:bg-crema-dark transition-colors">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden md:flex items-center gap-3 shrink-0">
            <a href="/login" class="text-sm font-semibold text-cacao/60 hover:text-cacao transition-colors">Inloggen</a>
            <a href="/onboarding" class="btn-primary !text-sm !px-5 !py-2.5">Gratis starten</a>
        </div>

        <button type="button" id="menuKnop" class="md:hidden ml-auto w-11 h-11 rounded-xl grid place-items-center bg-crema-dark text-cacao cursor-pointer" aria-label="Menu">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Mobiel menu --}}
    <div id="mobielMenu" class="hidden md:hidden border-t border-crema-dark px-5 py-4 space-y-1" style="background:var(--color-crema)">
        @foreach([['/#functies', 'Functies'], ['/#templates', 'Templates'], ['/#spaarprogramma', 'Spaarprogramma'], ['/#prijzen', 'Prijzen'], ['/blog', 'Blog'], ['/login', 'Inloggen']] as [$link, $label])
            <a href="{{ $link }}" class="block px-3.5 py-2.5 rounded-xl font-semibold text-cacao/70 hover:bg-crema-dark transition-colors">{{ $label }}</a>
        @endforeach
        <a href="/onboarding" class="btn-primary block text-center !py-3 mt-2">Gratis starten</a>
    </div>
</header>
