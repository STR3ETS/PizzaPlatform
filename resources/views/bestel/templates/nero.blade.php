<!-- NERO: donker fine-dining ontwerp, gecentreerd, gouden lijnen en een klassieke kaart -->
<style>
    .n-lijn { width: 5rem; height: 1px; background: var(--prijs); margin: 1.4rem auto; position: relative; }
    .n-lijn::after { content: "✦"; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); background: var(--pagina); padding: 0 .6rem; color: var(--prijs); font-size: .7rem; }
    .n-stip { flex: 1; border-bottom: 2px dotted color-mix(in srgb, var(--prijs) 45%, transparent); margin: 0 .6rem; transform: translateY(-.3rem); }
    .n-monogram {
        width: 4.5rem; height: 4.5rem; border: 1px solid var(--prijs); border-radius: 50%;
        display: grid; place-items: center; margin: 0 auto 1rem;
        font-family: Georgia, serif; font-size: 1.6rem; color: var(--prijs);
    }
</style>

<section data-anim class="relative overflow-hidden">
    <img class="b-foto absolute inset-0 opacity-35" src="{{ asset('assets/eten/interieur.jpg') }}" alt="" aria-hidden="true">
    <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgb(23 19 16 / .55), var(--pagina))"></div>
    <div class="max-w-3xl mx-auto px-4 pt-20 pb-14 text-center relative">
        <p class="text-xs font-extrabold tracking-[.35em] uppercase" style="color: var(--prijs)">Ristorante Pizzeria{{ $plaats ? ' | ' . $plaats : '' }}</p>
        <h1 class="b-kop text-5xl sm:text-6xl mt-4 leading-tight">{{ $naam }}</h1>
        <div class="n-lijn"></div>
        <p class="opacity-75 max-w-lg mx-auto text-lg">Ambachtelijke pizza's{{ $plaats ? ' in het hart van ' . $plaats : '' }}, bereid met liefde en de beste ingrediënten. {{ $alleenAfhalen ? 'Vanavond alleen afhalen.' : 'Bezorgen of afhalen.' }}</p>
        <div class="flex flex-wrap gap-3 justify-center mt-7">
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !text-lg !px-9 !py-3.5">Bestel nu</a>
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop stil !text-lg !px-7 !py-3.5">De kaart</a>
        </div>
        @if($vandaag['open'])<p class="text-sm opacity-55 mt-6">Vanavond geopend van {{ $vandaag['van'] }} tot {{ $vandaag['tot'] }}</p>@endif
    </div>
</section>

<section data-anim class="max-w-4xl mx-auto px-4 pt-10">
    <div class="grid grid-cols-3 gap-4">
        @foreach($toppers->take(3) as $m)
            <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $m->id }}" class="block group">
                <div class="rounded-full overflow-hidden aspect-square border" style="border-color: color-mix(in srgb, var(--prijs) 45%, transparent)">
                    <img class="b-foto group-hover:scale-105 transition-transform duration-300" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                </div>
                <p class="b-kop text-center mt-3">{{ $m->naam }}</p>
                <p class="b-prijs text-center text-sm">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</p>
            </a>
        @endforeach
    </div>
</section>

@if($toppers->isNotEmpty())
    <section data-anim class="max-w-4xl mx-auto px-4 py-10">
        <div class="text-center mb-8">
            <p class="text-xs font-extrabold tracking-[.35em] uppercase" style="color: var(--prijs)">Assaggi</p>
            <h2 class="b-kop text-3xl mt-2">Van onze kaart</h2>
        </div>
        <div class="grid sm:grid-cols-2 gap-x-12 gap-y-6">
            @foreach($toppers as $m)
                <a href="{{ route('bestel.menu', $slug) }}" class="block group">
                    <div class="flex items-baseline">
                        <span class="b-kop text-lg group-hover:underline underline-offset-4">{{ $m->naam }}</span>
                        <span class="n-stip"></span>
                        <span class="b-kop b-prijs">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                    </div>
                    @if($m->beschrijving)
                        <p class="text-sm opacity-55 mt-1">{{ $m->beschrijving }}</p>
                    @elseif($m->ingredienten)
                        <p class="text-sm opacity-55 mt-1">{{ implode(', ', $m->ingredienten) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="text-center mt-9">
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop stil !px-7">Bekijk de volledige kaart</a>
        </div>
    </section>
@endif

<section data-anim class="max-w-2xl mx-auto px-4 py-12 text-center">
    <div class="n-monogram">{{ mb_strtoupper(mb_substr($naam, 0, 1)) }}</div>
    <p class="text-xl italic opacity-80" style="font-family: Georgia, serif">"Een goede pizza vraagt om rust, vakmanschap en een hete oven. Aan alle drie geen gebrek."</p>
    <div class="n-lijn"></div>
    <div class="text-sm opacity-60 space-y-1">
        @if($adres)<p>{{ $adres }}</p>@endif
        @if($telefoon)<p>Reserveren of bestellen: <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
    </div>
    <a href="{{ route('bestel.menu', $slug) }}" class="b-knop mt-7">Online bestellen</a>
</section>
