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

{{-- Kop: logo en de twee knoppen die er echt toe doen --}}
<header class="sticky top-0 z-40 border-b border-crema-dark" style="background:color-mix(in srgb, var(--color-crema) 92%, transparent); backdrop-filter:blur(8px)">
    <div class="max-w-6xl mx-auto px-5 h-[6rem] flex items-center justify-between gap-6">
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <img src="{{ asset('logo.png') }}" alt="MijnPizzeria" class="max-h-22">
        </a>

        <div class="flex items-center gap-3 shrink-0">
            <a href="/login" class="text-sm font-semibold text-cacao/60 hover:text-cacao transition-colors">Inloggen</a>
            <a href="/onboarding" class="btn-primary !text-sm !px-5 !py-2.5">Gratis starten</a>
        </div>
    </div>
</header>
