<!-- RETRO, gemodelleerd naar het Caferio-template: hero met badge-tekst, promo-kaarten
     per categorie, about-blok, populaire gerechten met filterchips en reviews -->
<style>
    .r-badge {
        width: 11rem; height: 11rem; border-radius: 50%; margin: 0 auto;
        background: var(--kaart); border: 4px double var(--tekst);
        display: grid; place-content: center; text-align: center; gap: .15rem;
        transform: rotate(-6deg); box-shadow: 0 8px 0 0 rgb(91 58 33 / .2);
    }
    .r-golf { display: block; width: 100%; height: 2.2rem; color: var(--kaart); }
    .r-sticker {
        aspect-ratio: 1; border-radius: 50%; display: grid; place-content: center; text-align: center; gap: .2rem;
        padding: 1.4rem; transition: transform .18s ease;
    }
    .r-sticker:hover { transform: rotate(4deg) scale(1.04); }
</style>

@php $perCategorie = $menu->groupBy('categorie'); @endphp

<!-- Hero -->
<section data-anim class="b-hero relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 pt-12 pb-12 text-center relative">
        <p class="text-xs font-extrabold tracking-[.3em] uppercase opacity-60 mb-5">Eten, slapen en... pizza</p>
        <div class="r-badge mb-7">
            <i class="fa-solid fa-pizza-slice text-2xl" aria-hidden="true" style="color: var(--accent)"></i>
            <span class="b-kop text-lg leading-tight px-3">{{ $naam }}</span>
            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">al jaren lekker</span>
        </div>
        <h1 class="b-kop text-4xl sm:text-5xl leading-tight max-w-xl mx-auto">De lekkerste pizza {{ $plaats ? 'van ' . $plaats : 'uit de buurt' }}!</h1>
        <p class="opacity-70 max-w-md mx-auto mt-3 text-lg">Krokante bodems, gouden kazen en recepten die al generaties meegaan. {{ $alleenAfhalen ? 'Vandaag alleen afhalen.' : 'Bezorgen of afhalen.' }}</p>
        <div class="flex flex-wrap gap-3 justify-center mt-7">
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !text-lg !px-9 !py-3.5">Bestel nu</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-knop stil !text-lg !px-7 !py-3.5">Kom langs</a>
        </div>
        @if($vandaag['open'])<p class="text-sm opacity-60 mt-5">Vandaag draaien de ovens van {{ $vandaag['van'] }} tot {{ $vandaag['tot'] }}</p>@endif
    </div>
    <svg class="r-golf" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true"><path d="M0 20 Q 150 40 300 20 T 600 20 T 900 20 T 1200 20 V 40 H 0 Z" fill="currentColor"/></svg>
</section>

<!-- Promo-kaarten per categorie -->
@if($perCategorie->count() > 1)
    <section data-anim class="pt-6 pb-2" style="background: var(--kaart)">
        <div class="max-w-4xl mx-auto px-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($perCategorie->take(4) as $categorie => $items)
                <a href="{{ route('bestel.menu', $slug) }}#cat-{{ Str::slug($categorie) }}" class="b-kaart b-gerecht p-3 text-center block" style="background: var(--pagina)">
                    <div class="rounded-full overflow-hidden aspect-square w-24 mx-auto mb-2.5 border-4" style="border-color: color-mix(in srgb, var(--tekst) 25%, transparent)">
                        <img class="b-foto" src="{{ $fotos[$items->first()->id] }}" alt="{{ $categorie }}" loading="lazy">
                    </div>
                    <p class="b-kop">{{ $categorie }}</p>
                    <p class="text-xs opacity-55">vanaf &euro; {{ number_format($items->min('prijs') / 100, 2, ',', '') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

<!-- Over ons -->
<section data-anim class="pt-4 pb-8" style="background: var(--kaart)">
    <div class="max-w-4xl mx-auto px-4 grid sm:grid-cols-[auto_1fr] gap-6 items-center">
        <div class="w-24 h-24 rounded-full grid place-items-center mx-auto -rotate-6" style="border: 4px double var(--tekst); background: var(--pagina)"><i class="fa-solid fa-utensils text-3xl" aria-hidden="true" style="color: var(--accent)"></i></div>
        <div>
            <h2 class="b-kop text-2xl mb-2">Huisgemaakt, zoals vroeger</h2>
            <p class="opacity-70">Bij {{ $naam }} draait alles om rustig gerezen deeg, verse ingrediënten en een oven die nooit koud wordt. Bestel online en proef het verschil.@if($telefoon) Liever bellen? {{ $telefoon }}.@endif</p>
        </div>
    </div>
</section>

<!-- Populaire gerechten met filterchips -->
@if($toppers->isNotEmpty())
    <section data-anim class="max-w-4xl mx-auto px-4 py-10">
        <h2 class="b-kop text-3xl text-center mb-4">Onze klassiekers</h2>
        @if($perCategorie->count() > 1)
            <div class="flex flex-wrap justify-center gap-2 mb-7">
                @foreach($perCategorie as $categorie => $items)
                    <a href="{{ route('bestel.menu', $slug) }}#cat-{{ Str::slug($categorie) }}" class="b-chip">{{ $categorie }}</a>
                @endforeach
            </div>
        @endif
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
            @foreach($toppers as $m)
                <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $m->id }}" class="block group text-center">
                    <div class="rounded-full overflow-hidden aspect-square border-4 group-hover:rotate-2 transition-transform" style="border-color: color-mix(in srgb, var(--tekst) 30%, transparent); box-shadow: 0 8px 0 0 rgb(91 58 33 / .18)">
                        <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                    </div>
                    <p class="b-kop mt-2.5 leading-tight">{{ $m->naam }}</p>
                    <p class="b-kop b-prijs text-sm">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

<!-- Reviews -->
<section data-anim class="py-10" style="background: var(--kaart)">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="b-kop text-3xl text-center mb-6">Zij gingen je voor</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            @foreach([
                'Net als vroeger bij ons om de hoek. De Funghi is een aanrader.',
                'Snel besteld, snel in huis en gloeiend heet. Topzaak.',
                'Die krokante bodem... ik bestel nergens anders meer.',
            ] as $quote)
                <div class="b-kaart p-5 text-center" style="background: var(--pagina)">
                    <p class="text-sm mb-2" style="color: var(--accent)">@for($s = 0; $s < 5; $s++)<i class="fa-solid fa-star" aria-hidden="true"></i> @endfor</p>
                    <p class="text-sm opacity-75">"{{ $quote }}"</p>
                    <p class="text-xs font-extrabold opacity-50 mt-3">Vaste gast</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Banner -->
<section data-anim class="max-w-4xl mx-auto px-4 py-12">
    <div class="b-band p-7 sm:p-9 text-center relative overflow-hidden">
        <p class="b-kop text-3xl">Net als vroeger, maar dan bezorgd</p>
        <p class="opacity-90 mt-1 mb-5 max-w-md mx-auto">Bestel online en wij doen de rest.</p>
        <a href="{{ route('bestel.menu', $slug) }}" class="inline-flex items-center justify-center font-black px-8 py-3.5 text-lg" style="background: #fff; color: var(--accent); border-radius: var(--knop-radius)">Naar de menukaart</a>
    </div>
    @if($adres)<p class="text-center text-sm opacity-60 mt-5"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $adres }}</p>@endif
</section>
