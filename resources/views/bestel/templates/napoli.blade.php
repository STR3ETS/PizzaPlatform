<!-- NAPOLI: klassieke trattoria, de homepage ís een papieren menukaart in een kader -->
<style>
    .p-kader { border: 2px solid var(--tekst); position: relative; }
    .p-kader::before { content: ""; position: absolute; inset: 5px; border: 1px solid var(--tekst); pointer-events: none; }
    .p-stip { flex: 1; border-bottom: 2px dotted color-mix(in srgb, var(--tekst) 40%, transparent); margin: 0 .6rem; transform: translateY(-.3rem); }
    .p-stripe { height: .5rem; background: repeating-linear-gradient(90deg, #C0392B 0 2.2rem, transparent 2.2rem 4.4rem, #2F8F46 4.4rem 6.6rem, transparent 6.6rem 8.8rem); }
</style>

<section data-anim class="max-w-3xl mx-auto px-4 pt-10 pb-4">
    <div class="p-kader p-8 sm:p-12 text-center" style="background: var(--kaart)">
        <div class="rounded overflow-hidden aspect-[3/1] mb-7 border" style="border-color: #E8DCC2">
            <img class="b-foto" src="{{ asset('assets/eten/margherita.jpg') }}" alt="Onze pizza" loading="lazy">
        </div>
        <p class="text-xs font-extrabold tracking-[.3em] uppercase opacity-60">Cucina Italiana{{ $plaats ? ' | ' . $plaats : '' }}</p>
        <h1 class="b-kop text-4xl sm:text-5xl mt-3">{{ $naam }}</h1>
        <div class="p-stripe max-w-56 mx-auto my-5"></div>
        <p class="opacity-70 max-w-md mx-auto">Pizza zoals in Napoli: dunne bodem, verse ingrediënten en een oven die net iets te heet staat. {{ $alleenAfhalen ? 'Vandaag alleen afhalen.' : 'Bezorgen of afhalen.' }}</p>

        @if($toppers->isNotEmpty())
            <div class="text-left max-w-md mx-auto mt-8">
                <p class="b-kop text-center text-xl mb-4 uppercase tracking-widest">Le nostre pizze</p>
                <div class="space-y-4">
                    @foreach($toppers as $m)
                        <a href="{{ route('bestel.menu', $slug) }}" class="block group">
                            <div class="flex items-baseline">
                                <span class="b-kop group-hover:underline underline-offset-4">{{ $m->naam }}</span>
                                <span class="p-stip"></span>
                                <span class="b-kop b-prijs">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                            </div>
                            @if($m->ingredienten)
                                <p class="text-xs opacity-55 italic">{{ implode(', ', $m->ingredienten) }}</p>
                            @elseif($m->beschrijving)
                                <p class="text-xs opacity-55 italic">{{ $m->beschrijving }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-stripe max-w-56 mx-auto my-6"></div>
        <a href="{{ route('bestel.menu', $slug) }}" class="b-knop !text-lg !px-9 !py-3">Bestel online</a>
        @if($vandaag['open'])<p class="text-xs opacity-55 mt-4">Vandaag geopend van {{ $vandaag['van'] }} tot {{ $vandaag['tot'] }}</p>@endif
    </div>
</section>

<section data-anim class="max-w-3xl mx-auto px-4 mt-6 grid sm:grid-cols-3 gap-3 text-center text-sm">
    <div class="b-kaart p-4"><i class="fa-solid fa-wheat-awn text-xl block mb-1.5" aria-hidden="true" style="color: var(--prijs)"></i><span class="b-kop">Napoletaans deeg</span><p class="opacity-55 text-xs">48 uur gerezen</p></div>
    <div class="b-kaart p-4"><i class="fa-solid fa-seedling text-xl block mb-1.5" aria-hidden="true" style="color: #2F8F46"></i><span class="b-kop">San Marzano</span><p class="opacity-55 text-xs">Echte Italiaanse tomaten</p></div>
    <div class="b-kaart p-4"><i class="fa-solid fa-utensils text-xl block mb-1.5" aria-hidden="true" style="color: var(--prijs)"></i><span class="b-kop">Familierecepten</span><p class="opacity-55 text-xs">Van generatie op generatie</p></div>
</section>

<section data-anim class="max-w-3xl mx-auto px-4 mt-8 text-center text-sm opacity-70">
    @if($adres)<p>{{ $adres }}</p>@endif
    @if($telefoon)<p class="mt-1">Telefonisch bestellen: <a href="tel:{{ $telefoon }}" class="underline underline-offset-2">{{ $telefoon }}</a></p>@endif
</section>
