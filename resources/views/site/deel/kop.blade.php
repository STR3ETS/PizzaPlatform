{{-- Kop van de site: merk links, pilnavigatie in het midden, inloggen en starten rechts.
     Plakt bovenaan het vel en schuift weg bij naar beneden scrollen (script in de layout). --}}
@php
    $links = [
        ['/', 'Home'],
        ['/blog', 'Blog'],
    ];
    $opBlog = request()->is('blog*');
@endphp
<header id="siteKop" class="site-kop">
    <a href="/" aria-label="Shop &amp; Eat, naar de homepagina">@include('deel.merk')</a>

    <nav class="pillnav" aria-label="Hoofdnavigatie">
        @foreach($links as [$link, $label])
            <a href="{{ $link }}" class="{{ $opBlog && $link === '/blog' ? 'aan' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="rechts">
        <a href="/login" class="btn line klein">Inloggen</a>
        <a href="/onboarding" class="btn dark klein">Gratis starten <i class="ar fa-solid fa-chevron-right" aria-hidden="true"></i></a>
        <button type="button" id="menuKnop" class="menu-knop" aria-label="Menu openen" aria-expanded="false" aria-controls="mobielMenu">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Mobiel menu --}}
    <div id="mobielMenu" class="site-menu">
        @foreach($links as [$link, $label])
            <a href="{{ $link }}">{{ $label }}</a>
        @endforeach
        <a href="/login">Inloggen</a>
        <a href="/onboarding" class="btn dark">Gratis starten</a>
    </div>
</header>
