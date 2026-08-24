@extends('bestel.layout')

@section('titel', 'Contact | ' . $naam . ($plaats ? ' in ' . $plaats : ''))

@section('inhoud')
    @switch($themaId)
        @case('nero')
            <!-- NERO: statig en gecentreerd -->
            <section data-anim class="max-w-2xl mx-auto px-4 pt-16 pb-10 text-center">
                <p class="text-xs font-extrabold tracking-[.35em] uppercase" style="color: var(--prijs)">Contact</p>
                <h1 class="b-kop text-5xl mt-3">Tot snel</h1>
                <div class="w-20 h-px mx-auto my-6" style="background: var(--prijs)"></div>
                @if($adres)<p class="text-lg opacity-80">{{ $adres }}</p>@endif
                @if($telefoon)<p class="mt-2 opacity-70">Reserveren of bestellen: <a href="tel:{{ $telefoon }}" class="underline underline-offset-4">{{ $telefoon }}</a></p>@endif
                @if($adres)<p class="mt-3"><a class="text-sm uppercase tracking-widest underline underline-offset-4" style="color: var(--prijs)" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($naam . ', ' . $adres) }}">Route</a></p>@endif
                <div class="w-20 h-px mx-auto my-6" style="background: var(--prijs)"></div>
                <div class="max-w-xs mx-auto text-sm space-y-1.5">
                    @foreach($dagen as $dag)
                        <div class="flex justify-between gap-6 {{ $dag['open'] ? 'opacity-80' : 'opacity-40' }}">
                            <span class="uppercase tracking-widest text-xs pt-0.5">{{ $dag['naam'] }}</span>
                            <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('bestel.menu', $slug) }}" class="b-knop mt-8">Online bestellen</a>
            </section>
            @break

        @case('napoli')
            <!-- NAPOLI: alles in het papieren kader -->
            <section data-anim class="max-w-3xl mx-auto px-4 pt-10">
                <div class="p-8 sm:p-12 text-center" style="background: var(--kaart); border: 2px solid var(--tekst); outline: 1px solid var(--tekst); outline-offset: -6px">
                    <p class="text-xs tracking-[.3em] uppercase opacity-55">Contatti</p>
                    <h1 class="b-kop text-4xl mt-2">Kom eens langs</h1>
                    <div class="max-w-40 mx-auto my-5" style="height: .4rem; background: repeating-linear-gradient(90deg, #C0392B 0 2rem, transparent 2rem 4rem, #2F8F46 4rem 6rem, transparent 6rem 8rem)"></div>
                    @if($adres)<p class="italic opacity-75">{{ $adres }}</p>@endif
                    @if($telefoon)<p class="italic opacity-75 mt-1">Telefono: <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
                    <div class="max-w-xs mx-auto mt-6 text-sm space-y-1">
                        @foreach($dagen as $dag)
                            <div class="flex items-baseline {{ $dag['open'] ? '' : 'opacity-45' }}">
                                <span class="b-kop">{{ $dag['naam'] }}</span>
                                <span class="flex-1 mx-2" style="border-bottom: 2px dotted color-mix(in srgb, var(--tekst) 40%, transparent); transform: translateY(-.25rem)"></span>
                                <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Chiuso' }}</span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('bestel.menu', $slug) }}" class="b-knop mt-7 !px-8">Bestel online</a>
                </div>
            </section>
            @break

        @case('puro')
            <!-- PURO: minimaal, twee kolommen met accentlijnen -->
            <section data-anim class="max-w-5xl mx-auto px-4 pt-14 pb-6">
                <div class="w-14 h-1 mb-6" style="background: var(--accent)"></div>
                <h1 class="b-kop text-5xl mb-10">Contact</h1>
                <div class="grid sm:grid-cols-2 gap-10 max-w-3xl text-sm">
                    <div>
                        <div class="w-10 h-1 mb-3" style="background: var(--accent)"></div>
                        <p class="b-kop text-lg mb-2">Bezoek ons</p>
                        @if($adres)<p class="opacity-70">{{ $adres }}</p>@endif
                        @if($telefoon)<p class="opacity-70 mt-1"><a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
                        @if($adres)<p class="mt-3"><a class="font-black" style="color: var(--accent)" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($naam . ', ' . $adres) }}">Route &rarr;</a></p>@endif
                    </div>
                    <div>
                        <div class="w-10 h-1 mb-3" style="background: var(--accent)"></div>
                        <p class="b-kop text-lg mb-2">Openingstijden</p>
                        @foreach($dagen as $dag)
                            <div class="flex justify-between gap-4 {{ $dag['open'] ? 'opacity-70' : 'opacity-40' }}">
                                <span>{{ $dag['naam'] }}</span>
                                <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('blocco')
            <!-- BLOCCO: harde blokken -->
            <section data-anim class="max-w-5xl mx-auto px-4 pt-10 pb-6">
                <h1 class="b-kop leading-none" style="font-size: clamp(3rem, 12vw, 7rem)">Contact<span style="color: var(--accent)">.</span></h1>
                <div class="grid sm:grid-cols-3 gap-4 mt-8">
                    <div class="b-kaart p-5"><p class="b-kop mb-2">Adres</p><p class="text-sm font-black uppercase opacity-70">{{ $adres ?: 'Volgt binnenkort' }}</p>@if($adres)<a class="inline-block mt-3 text-xs font-black uppercase underline" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($naam . ', ' . $adres) }}">Route ★</a>@endif</div>
                    <div class="b-kaart p-5"><p class="b-kop mb-2">Tijden</p>
                        @foreach($dagen as $dag)
                            <div class="flex justify-between text-xs font-black uppercase {{ $dag['open'] ? 'opacity-70' : 'opacity-35' }}"><span>{{ mb_substr($dag['naam'], 0, 2) }}</span><span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Dicht' }}</span></div>
                        @endforeach
                    </div>
                    <div class="b-kaart p-5"><p class="b-kop mb-2">Bellen</p>
                        @if($telefoon)<a href="tel:{{ $telefoon }}" class="b-kop text-2xl underline">{{ $telefoon }}</a>@else<p class="text-sm font-black uppercase opacity-70">Bestel online</p>@endif
                    </div>
                </div>
                <a href="{{ route('bestel.menu', $slug) }}" class="block mt-6 text-center font-black uppercase text-2xl py-5" style="background: #111; color: var(--accent); border: 2px solid #111">Of gewoon bestellen ★</a>
            </section>
            @break

        @case('retro')
            <!-- RETRO: badge en golf -->
            <section data-anim class="b-hero relative overflow-hidden text-center">
                <div class="max-w-3xl mx-auto px-4 pt-12 pb-10 relative">
                    <div class="inline-grid place-content-center w-28 h-28 rounded-full -rotate-6 mb-4" style="border: 4px double var(--tekst); background: var(--kaart)">
                        <i class="fa-solid fa-hand text-2xl" aria-hidden="true" style="color: var(--accent)"></i>
                    </div>
                    <h1 class="b-kop text-4xl">Kom je gedag zeggen?</h1>
                    <p class="opacity-70 mt-2 max-w-md mx-auto">We staan al jaren op dezelfde plek, met dezelfde oven.</p>
                </div>
                <svg class="block w-full h-9" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true" style="color: var(--kaart)"><path d="M0 20 Q 150 40 300 20 T 600 20 T 900 20 T 1200 20 V 40 H 0 Z" fill="currentColor"/></svg>
            </section>
            <section data-anim class="pb-10" style="background: var(--kaart)">
                <div class="max-w-3xl mx-auto px-4 grid sm:grid-cols-2 gap-6 pt-2 text-center sm:text-left">
                    <div class="b-kaart p-6" style="background: var(--pagina)">
                        <p class="b-kop text-xl mb-2">Hier zitten we</p>
                        @if($adres)<p class="opacity-75 text-sm"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $adres }}</p>@endif
                        @if($telefoon)<p class="opacity-75 text-sm mt-1"><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
                        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !py-2 !px-5 !text-sm mt-4">Of bestel gewoon</a>
                    </div>
                    <div class="b-kaart p-6" style="background: var(--pagina)">
                        <p class="b-kop text-xl mb-2">Openingstijden</p>
                        @foreach($dagen as $dag)
                            <div class="flex justify-between gap-4 text-sm {{ $dag['open'] ? 'opacity-80' : 'opacity-45' }}">
                                <span>{{ $dag['naam'] }}</span>
                                <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @default
            <!-- FRESCO: speels met kaarten -->
            <section data-anim class="b-hero">
                <div class="b-hero-blob"></div>
                <div class="max-w-5xl mx-auto px-4 pt-8 pb-6 relative text-center">
                    <h1 class="b-kop text-4xl mb-2">Contact</h1>
                    <p class="opacity-70">Langskomen, bellen of gewoon online bestellen: alles kan.</p>
                </div>
            </section>
            <section data-anim class="max-w-5xl mx-auto px-4">
                <div class="grid sm:grid-cols-2 gap-4 items-start">
                    <div class="b-kaart p-6">
                        <h2 class="b-kop text-xl mb-3">Hier vind je ons</h2>
                        @if($adres)
                            <p class="opacity-80"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $adres }}</p>
                            <p class="mt-2"><a class="underline underline-offset-2 font-extrabold" style="color: var(--accent)" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($naam . ', ' . $adres) }}">Route via Google Maps</a></p>
                        @else
                            <p class="opacity-70">Adres volgt binnenkort.</p>
                        @endif
                        @if($telefoon)
                            <p class="mt-5 opacity-80">Liever telefonisch bestellen?</p>
                            <a href="tel:{{ $telefoon }}" class="b-knop mt-2">Bel {{ $telefoon }}</a>
                        @endif
                    </div>
                    <div class="b-kaart p-6">
                        <h2 class="b-kop text-xl mb-3">Openingstijden</h2>
                        @foreach($dagen as $dag)
                            <div class="flex justify-between gap-4 py-0.5 {{ $dag['open'] ? '' : 'opacity-50' }}">
                                <span>{{ $dag['naam'] }}</span>
                                <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
    @endswitch
@endsection
