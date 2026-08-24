<!-- BLOCCO, gemodelleerd naar het FoodKing-template: uppercase energie, tilted badges,
     product-grid met bestelknoppen, combo-blokken, countdown-promo en een mega-CTA -->
<style>
    .k-mega { font-size: clamp(3.2rem, 11vw, 7.5rem); line-height: .95; }
    .k-badge { display: inline-block; transform: rotate(-6deg); background: var(--accent); border: 2px solid #111; padding: .3rem .8rem; font-weight: 900; text-transform: uppercase; font-size: .75rem; }
    .k-prijsbadge { position: absolute; top: -.8rem; right: -.6rem; transform: rotate(8deg); background: var(--accent); border: 2px solid #111; padding: .15rem .55rem; font-weight: 900; box-shadow: 3px 3px 0 0 #111; }
    .k-blok-cta { display: block; background: #111; color: var(--accent); text-align: center; padding: 1.6rem; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; font-size: clamp(1.4rem, 4vw, 2.4rem); border: 2px solid #111; }
    .k-blok-cta:hover { background: var(--accent); color: #111; }
    .k-klok { font-variant-numeric: tabular-nums; }
</style>

@php
    $held = $toppers->first();
    $perCategorie = $menu->groupBy('categorie');
@endphp

<!-- Hero -->
<section data-anim class="max-w-5xl mx-auto px-4 pt-10 pb-8 grid lg:grid-cols-[1.3fr_1fr] gap-8 items-center">
    <div>
        <span class="k-badge mb-4">Krokant, elke hap raak</span>
        <h1 class="b-kop k-mega mt-3">{{ $held ? $held->naam : 'Pizza' }}<br><span style="color: var(--accent); -webkit-text-stroke: 2px #111;">&euro; {{ number_format(($held?->prijs ?? 950) / 100, 2, ',', '') }}</span></h1>
        <p class="font-black uppercase text-sm opacity-70 mt-4 max-w-md">{{ $naam }}{{ $plaats ? ' ★ ' . $plaats : '' }} ★ Vers gebakken ★ {{ $alleenAfhalen ? 'Afhalen. Klaar.' : 'Bezorgd of afgehaald. Klaar.' }}</p>
        <div class="flex flex-wrap items-center gap-4 mt-6">
            <a href="{{ route('bestel.menu', $slug) }}{{ $held ? '?gerecht=' . $held->id : '' }}" class="b-knop !text-xl !px-9 !py-4">Bestel nu</a>
            @if($vandaag['open'])<span class="font-black uppercase text-xs px-3 py-1.5" style="background: var(--accent); border: 2px solid #111">Vandaag {{ $vandaag['van'] }} - {{ $vandaag['tot'] }}</span>@endif
        </div>
    </div>
    <div class="relative">
        <div class="b-fotovak aspect-square">
            <img class="b-foto" src="{{ $held ? $fotos[$held->id] : asset('assets/eten/pepperoni.jpg') }}" alt="{{ $held?->naam ?: 'Pizza' }}">
        </div>
        <span class="b-zweef" style="top: -4%; left: -3%;">🌶️</span>
        <span class="b-zweef" style="bottom: -5%; right: -3%; animation-delay: -3s;">🔥</span>
    </div>
</section>

<!-- Populaire items -->
@if($toppers->isNotEmpty())
    <section data-anim class="max-w-5xl mx-auto px-4 py-6">
        <h2 class="b-kop text-2xl mb-5">De hitlijst</h2>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($toppers as $m)
                <div class="b-kaart p-3 text-center relative">
                    <span class="k-prijsbadge b-kop text-sm">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                    <div class="aspect-[4/3] overflow-hidden mb-2.5" style="border: 2px solid #111">
                        <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                    </div>
                    <p class="b-kop">{{ $m->naam }}</p>
                    @if($m->ingredienten)<p class="text-xs opacity-60 mt-1 uppercase">{{ implode(' + ', array_slice($m->ingredienten, 0, 3)) }}</p>@endif
                    <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $m->id }}" class="b-knop !py-1.5 !px-4 !text-sm mt-3">In je mandje</a>
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Combo-blokken per categorie -->
@if($perCategorie->count() > 1)
    <section data-anim class="max-w-5xl mx-auto px-4 py-4">
        <div class="grid sm:grid-cols-{{ min(3, $perCategorie->count()) }} gap-4">
            @foreach($perCategorie->take(3) as $categorie => $items)
                <a href="{{ route('bestel.menu', $slug) }}#cat-{{ Str::slug($categorie) }}" class="b-kaart p-5 flex items-center gap-4" style="background: var(--accent)">
                    <span class="text-4xl">{{ $items->first()->icoon ?: '🍕' }}</span>
                    <span>
                        <span class="b-kop block text-lg">{{ $categorie }}</span>
                        <span class="font-black uppercase text-xs opacity-70">vanaf &euro; {{ number_format($items->min('prijs') / 100, 2, ',', '') }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<!-- Cijfers -->
<section data-anim class="max-w-5xl mx-auto px-4 py-6 grid sm:grid-cols-3 gap-4 text-center">
    <div class="b-kaart p-5"><p class="b-kop text-3xl">100%</p><p class="font-black uppercase text-xs opacity-60">Vers, nooit uit de vriezer</p></div>
    <div class="b-kaart p-5"><p class="b-kop text-3xl">450&deg;</p><p class="font-black uppercase text-xs opacity-60">Onze oven staat gloeiend</p></div>
    <div class="b-kaart p-5"><p class="b-kop text-3xl">0</p><p class="font-black uppercase text-xs opacity-60">Tussenpartijen, direct bij ons</p></div>
</section>

<!-- Countdown-promo -->
<section data-anim class="max-w-5xl mx-auto px-4 py-6">
    <div class="p-7 sm:p-9 text-center" style="background: #111; color: #fff; border: 2px solid #111">
        <p class="font-black uppercase text-xs tracking-[.25em]" style="color: var(--accent)">Nu open, straks niet meer</p>
        <p class="b-kop text-3xl sm:text-4xl mt-2">Vandaag nog bestellen kan nog</p>
        <p class="b-kop k-klok text-5xl sm:text-6xl mt-3" style="color: var(--accent)"><span data-aftellen data-tot="{{ $vandaag['tot'] }}" data-open="{{ $vandaag['open'] && $online ? 1 : 0 }}"></span></p>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !text-lg !px-9 !py-3.5 mt-6">Naar de menukaart</a>
    </div>
</section>

<!-- Eén dikke quote -->
<section data-anim class="max-w-3xl mx-auto px-4 py-8 text-center">
    <p class="b-kop text-2xl sm:text-3xl leading-snug">"Dikke bodem, dikke smaak, dik in orde."</p>
    <p class="font-black uppercase text-xs opacity-50 mt-3">Vaste gast ★ ⭐⭐⭐⭐⭐</p>
</section>

<!-- Mega CTA -->
<section data-anim class="max-w-5xl mx-auto px-4 pb-8">
    <a href="{{ route('bestel.menu', $slug) }}" class="k-blok-cta">Bestel nu ★ {{ $naam }}</a>
    <p class="text-center text-xs font-black uppercase opacity-50 mt-3">
        @if($adres){{ $adres }}@endif @if($telefoon) ★ <a href="tel:{{ $telefoon }}" class="underline">{{ $telefoon }}</a>@endif
    </p>
</section>
