{{-- Kop: op de homepagina fixed en doorzichtig boven de pizza, wit zodra je scrolt; elders gewoon wit --}}
@php $kopDoorzichtig = request()->is('/'); @endphp
<header id="siteKop" @class([
    'site-kop-doorzichtig fixed inset-x-0 top-0 z-40' => $kopDoorzichtig,
    'sticky top-0 z-40 border-b border-cacao/10 bg-white/90 backdrop-blur-lg' => ! $kopDoorzichtig,
])>
    <div class="max-w-6xl mx-auto px-5 h-[5.5rem] flex items-center gap-6">
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <img src="{{ asset('logo.png') }}" alt="MijnPizzeria" class="max-h-20">
        </a>

        <nav class="hidden lg:flex items-center gap-8 mx-auto" aria-label="Hoofdnavigatie">
            @foreach([['/#functies', 'Functies'], ['/#templates', 'Templates'], ['/#spaarprogramma', 'Spaarprogramma'], ['/#prijzen', 'Prijzen'], ['/blog', 'Blog']] as [$link, $label])
                <a href="{{ $link }}" class="kop-tekst text-sm font-semibold {{ str_starts_with($link, '/blog') && request()->is('blog*') ? 'text-cacao' : 'text-cacao/60' }} hover:text-cacao transition-colors focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-tomato rounded-sm">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden lg:flex items-center gap-5 shrink-0">
            <a href="/login" class="kop-tekst text-sm font-semibold text-cacao/60 hover:text-cacao transition-colors focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-tomato rounded-sm">Inloggen</a>
            <a href="/onboarding" class="kop-knop rounded-lg bg-cacao text-white text-sm font-bold px-5 py-2.5 hover:bg-cacao/85 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato">Gratis starten</a>
        </div>

        <button type="button" id="menuKnop" class="kop-menu lg:hidden ml-auto w-11 h-11 rounded-xl grid place-items-center border border-cacao/15 text-cacao cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tomato" aria-label="Menu openen" aria-expanded="false" aria-controls="mobielMenu">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Mobiel menu --}}
    <div id="mobielMenu" class="hidden lg:hidden border-t border-cacao/10 bg-white px-5 py-4 space-y-1">
        @foreach([['/#functies', 'Functies'], ['/#templates', 'Templates'], ['/#spaarprogramma', 'Spaarprogramma'], ['/#prijzen', 'Prijzen'], ['/blog', 'Blog'], ['/login', 'Inloggen']] as [$link, $label])
            <a href="{{ $link }}" class="block px-3.5 py-2.5 rounded-xl font-semibold text-cacao/70 hover:bg-crema transition-colors">{{ $label }}</a>
        @endforeach
        <a href="/onboarding" class="btn-primary block text-center !py-3 mt-2">Gratis starten</a>
    </div>
</header>
