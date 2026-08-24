<!-- PURO: strak en minimaal, veel wit, grote tegel-grid -->
<style>
    .u-tegel { aspect-ratio: 1; display: grid; place-items: center; font-size: clamp(3rem, 8vw, 5rem); transition: transform .18s ease; }
    .u-tegel:hover { transform: scale(1.03); }
    .u-lijn { width: 3.5rem; height: 4px; background: var(--accent); }
</style>

<section data-anim class="max-w-5xl mx-auto px-4 pt-14 pb-10">
    <div class="max-w-2xl">
        <div class="u-lijn mb-6"></div>
        <h1 class="b-kop text-5xl sm:text-6xl leading-[1.02] mb-5">Goed eten.<br>Puur en simpel.</h1>
        <p class="opacity-70 text-lg max-w-lg mb-7">{{ $naam }}{{ $plaats ? ', ' . $plaats : '' }}. Verse pizza zonder poespas: goede ingrediënten, eerlijk bereid. {{ $alleenAfhalen ? 'Nu alleen afhalen.' : 'Bezorgen of afhalen.' }}</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !text-lg !px-8 !py-3.5">Bestel nu</a>
            <a href="{{ route('bestel.contact', $slug) }}" class="b-knop stil !text-lg !px-6 !py-3.5">Contact</a>
        </div>
    </div>
</section>

@if($toppers->isNotEmpty())
    <section data-anim class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($toppers as $m)
                <a href="{{ route('bestel.menu', $slug) }}?gerecht={{ $m->id }}" class="block group">
                    <div class="b-fotovak aspect-square">
                        <img class="b-foto group-hover:scale-105 transition-transform duration-300" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                    </div>
                    <div class="flex items-baseline justify-between gap-3 mt-2 px-1">
                        <p class="b-kop truncate group-hover:underline underline-offset-4">{{ $m->naam }}</p>
                        <span class="b-prijs font-extrabold shrink-0">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

<section data-anim class="max-w-5xl mx-auto px-4 mt-14">
    <div class="grid sm:grid-cols-3 gap-8 text-sm">
        <div><div class="u-lijn mb-3"></div><p class="b-kop text-lg">Vers bereid</p><p class="opacity-60 mt-1">Elke bestelling gaat pas de oven in als jij hem plaatst.</p></div>
        <div><div class="u-lijn mb-3"></div><p class="b-kop text-lg">{{ $alleenAfhalen ? 'Snel afhalen' : 'Thuisbezorgd door ons' }}</p><p class="opacity-60 mt-1">{{ $alleenAfhalen ? 'Klaar op het moment dat jij er bent.' : 'Onze eigen bezorger brengt hem warm bij je thuis.' }}</p></div>
        <div><div class="u-lijn mb-3"></div><p class="b-kop text-lg">{{ $vandaag['open'] ? 'Vandaag open' : 'Vandaag gesloten' }}</p><p class="opacity-60 mt-1">{{ $vandaag['open'] ? 'Van ' . $vandaag['van'] . ' tot ' . $vandaag['tot'] . '.' : 'Bekijk de kaart alvast voor de volgende keer.' }}@if($telefoon) Bellen kan ook: <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a>.@endif</p></div>
    </div>
</section>

<section data-anim class="max-w-5xl mx-auto px-4 mt-14">
    <div class="b-band p-8 sm:p-10 text-center">
        <p class="b-kop text-3xl">Honger?</p>
        <p class="opacity-90 mt-1 mb-5">Je bestelling ligt binnen een minuut in onze keuken.</p>
        <a href="{{ route('bestel.menu', $slug) }}" class="inline-flex items-center justify-center font-black px-8 py-3.5 text-lg" style="background: #fff; color: var(--accent); border-radius: var(--knop-radius)">Naar de menukaart</a>
    </div>
</section>
