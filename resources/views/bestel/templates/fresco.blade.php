<!-- FRESCO, gemodelleerd naar het Foodies-template: promo-strip, hero met sticker,
     categorie-showcase, special van vandaag met aftelklok, features en reviews -->
<style>
    .f-strip { background: var(--accent); color: #fff; text-align: center; font-weight: 800; font-size: .85rem; padding: .5rem 1rem; }
    .f-sticker {
        position: absolute; top: 6%; right: 4%; width: 5.2rem; height: 5.2rem; border-radius: 50%;
        background: var(--accent); color: #fff; display: grid; place-content: center; text-align: center;
        font-weight: 900; font-size: .8rem; line-height: 1.1; transform: rotate(10deg);
        box-shadow: 0 6px 14px rgb(0 0 0 / .18); z-index: 2;
    }
    .f-klok { font-variant-numeric: tabular-nums; letter-spacing: .06em; }
</style>

@php
    $held = $toppers->first();
    $perCategorie = $menu->groupBy('categorie');
@endphp

<div class="f-strip">
    🛵 {{ $tiers->isNotEmpty() ? 'Bezorgen al vanaf ' . '€ ' . number_format($tiers->first()['kosten'] / 100, 2, ',', '') : 'Vers bereid' }}
    en afhalen is altijd gratis. Bestel direct online!
</div>

<!-- Hero -->
<section data-anim class="b-hero">
    <div class="b-hero-blob"></div>
    <div class="max-w-5xl mx-auto px-4 pt-10 sm:pt-14 pb-12 grid lg:grid-cols-2 gap-10 items-center relative">
        <div class="text-center lg:text-left">
            @if($held)
                <span class="inline-block text-xs font-extrabold tracking-widest uppercase rounded-full px-3 py-1 mb-4" style="background: var(--accent-zacht); color: var(--accent)">{{ $held->naam }}, vandaag vers gedraaid</span>
            @endif
            <h1 class="b-kop text-4xl sm:text-5xl leading-[1.05] mb-3">Échte pizza van {{ $naam }}{{ $plaats ? ' in ' . $plaats : '' }}</h1>
            @if($held && $held->ingredienten)
                <p class="text-xs font-extrabold tracking-[.18em] uppercase opacity-50 mb-4">{{ implode(', ', array_slice($held->ingredienten, 0, 4)) }}</p>
            @endif
            <p class="opacity-75 max-w-md mx-auto lg:mx-0 mb-6">Vers bereid op het moment dat jij bestelt. {{ $alleenAfhalen ? 'Nu alleen afhalen.' : 'Bezorgen of afhalen, jij kiest.' }}</p>
            <div class="flex flex-wrap items-center gap-4 justify-center lg:justify-start">
                <a href="{{ route('bestel.menu', $slug) }}{{ $held ? '?gerecht=' . $held->id : '' }}" class="b-knop !text-lg !px-8 !py-3.5">Bestel nu</a>
                @if($held)<span class="b-kop text-2xl b-prijs">vanaf &euro; {{ number_format($menu->min('prijs') / 100, 2, ',', '') }}</span>@endif
            </div>
        </div>
        <div class="relative py-6">
            <div class="f-sticker">VERS<br>&amp; WARM</div>
            <div class="b-fotobord">
                <img class="b-foto" src="{{ $held ? $fotos[$held->id] : asset('assets/eten/pepperoni.jpg') }}" alt="{{ $held?->naam ?: 'Pizza' }}">
            </div>
            <span class="b-zweef" style="top: 4%; left: 12%;">🍅</span>
            <span class="b-zweef" style="top: 16%; right: 8%; animation-delay: -2s;">🌿</span>
            <span class="b-zweef" style="bottom: 10%; left: 6%; animation-delay: -4s;">🧀</span>
            <span class="b-zweef" style="bottom: 2%; right: 14%; animation-delay: -1s;">🌶️</span>
        </div>
    </div>
</section>

<!-- Categorie-showcase -->
@if($perCategorie->count() > 1)
    <section data-anim class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($perCategorie as $categorie => $items)
                <a href="{{ route('bestel.menu', $slug) }}#cat-{{ Str::slug($categorie) }}" class="b-kaart b-gerecht p-3 text-center block">
                    <div class="b-fotovak aspect-[4/3] mb-2.5">
                        <img class="b-foto" src="{{ $fotos[$items->first()->id] }}" alt="{{ $categorie }}" loading="lazy">
                    </div>
                    <p class="b-kop text-lg">{{ $categorie }}</p>
                    <p class="text-xs opacity-55">{{ $items->count() }} {{ $items->count() === 1 ? 'gerecht' : 'gerechten' }} vanaf &euro; {{ number_format($items->min('prijs') / 100, 2, ',', '') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

<!-- Special van vandaag met aftelklok -->
@if($held)
    <section data-anim class="max-w-5xl mx-auto px-4 mt-10">
        <div class="b-band p-6 sm:p-9 grid sm:grid-cols-[auto_1fr_auto] gap-6 items-center">
            <div class="w-24 h-24 rounded-full overflow-hidden mx-auto shadow-lg shrink-0">
                <img class="b-foto" src="{{ $fotos[$held->id] }}" alt="{{ $held->naam }}" loading="lazy">
            </div>
            <div class="text-center sm:text-left">
                <p class="text-xs font-extrabold tracking-widest uppercase opacity-80">Special van vandaag</p>
                <p class="b-kop text-2xl sm:text-3xl">{{ $held->naam }} voor &euro; {{ number_format($held->prijs / 100, 2, ',', '') }}</p>
                <p class="opacity-90 text-sm mt-1">Vandaag nog bestellen kan tot sluitingstijd: nog <span class="f-klok font-black" data-aftellen data-tot="{{ $vandaag['tot'] }}" data-open="{{ $vandaag['open'] && $online ? 1 : 0 }}"></span></p>
            </div>
            <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $held->id }}" class="shrink-0 inline-flex items-center justify-center font-black px-7 py-3" style="background: #fff; color: var(--accent); border-radius: var(--knop-radius)">Bestel de special</a>
        </div>
    </section>
@endif

<!-- Toppers -->
@if($toppers->isNotEmpty())
    <section data-anim class="max-w-5xl mx-auto px-4 mt-12">
        <div class="text-center mb-6">
            <h2 class="b-kop text-3xl">Onze toppers</h2>
            <p class="opacity-65 mt-1">Waar {{ $plaats ?: 'de buurt' }} steeds voor terugkomt</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            @foreach($toppers->take(3) as $m)
                <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $m->id }}" class="b-kaart b-gerecht p-3 text-center block">
                    <div class="b-fotovak aspect-[4/3] mb-3">
                        <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                    </div>
                    <p class="b-kop text-xl">{{ $m->naam }}</p>
                    @if($m->beschrijving)
                        <p class="text-sm opacity-65 mt-1 min-h-10">{{ $m->beschrijving }}</p>
                    @elseif($m->ingredienten)
                        <p class="text-sm opacity-65 mt-1 min-h-10">{{ implode(', ', $m->ingredienten) }}</p>
                    @endif
                    <div class="flex items-center justify-center gap-3 mt-3">
                        <span class="b-kop b-prijs text-lg">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                        <span class="b-knop !py-1.5 !px-3.5 !text-sm">Bestel</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

<!-- Features -->
<section data-anim class="max-w-5xl mx-auto px-4 mt-12">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-center text-sm">
        <div class="b-kaart px-4 py-5"><span class="text-2xl block mb-1.5">🔥</span><span class="b-kop block">Steenoven vers</span><span class="opacity-60 text-xs">Bereid op bestelling</span></div>
        <div class="b-kaart px-4 py-5"><span class="text-2xl block mb-1.5">🥗</span><span class="b-kop block">Verse ingrediënten</span><span class="opacity-60 text-xs">Elke dag geleverd</span></div>
        <div class="b-kaart px-4 py-5"><span class="text-2xl block mb-1.5">👨‍🍳</span><span class="b-kop block">Eigen recepten</span><span class="opacity-60 text-xs">Met liefde gemaakt</span></div>
        <div class="b-kaart px-4 py-5"><span class="text-2xl block mb-1.5">{{ $alleenAfhalen ? '🥡' : '🛵' }}</span><span class="b-kop block">{{ $alleenAfhalen ? 'Snel afhalen' : 'Snel bezorgd' }}</span><span class="opacity-60 text-xs">{{ $alleenAfhalen ? 'Klaar wanneer jij er bent' : 'Warm bij je thuis' }}</span></div>
    </div>
</section>

<!-- Reviews -->
<section data-anim class="max-w-5xl mx-auto px-4 mt-12">
    <h2 class="b-kop text-3xl text-center mb-6">Wat onze gasten zeggen</h2>
    <div class="grid sm:grid-cols-3 gap-4">
        @foreach([
            ['⭐⭐⭐⭐⭐', 'De bodem is nog knapperig als hij aankomt. Dat lukt bijna niemand.'],
            ['⭐⭐⭐⭐⭐', 'Binnen een minuut besteld en precies op tijd. Zo hoort het.'],
            ['⭐⭐⭐⭐⭐', 'Eindelijk direct bij de zaak zelf bestellen. De pizza is er alleen maar beter op geworden.'],
        ] as [$sterren, $quote])
            <div class="b-kaart p-5">
                <p class="text-sm mb-2">{{ $sterren }}</p>
                <p class="text-sm opacity-75">"{{ $quote }}"</p>
                <p class="text-xs font-extrabold opacity-50 mt-3">Vaste gast</p>
            </div>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section data-anim class="max-w-5xl mx-auto px-4 mt-12">
    <div class="b-band p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-5">
        <div class="flex-1 text-center sm:text-left">
            <p class="b-kop text-2xl sm:text-3xl leading-tight">Trek gekregen?</p>
            <p class="opacity-90 mt-1">Bestel in een paar tikken. Je bestelling ligt direct in onze keuken.</p>
        </div>
        <a href="{{ route('bestel.menu', $slug) }}" class="shrink-0 inline-flex items-center justify-center font-black px-7 py-3 text-lg" style="background: #fff; color: var(--accent); border-radius: var(--knop-radius)">Naar de menukaart</a>
    </div>
</section>
