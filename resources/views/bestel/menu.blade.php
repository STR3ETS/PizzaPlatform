@extends('bestel.layout')

@section('titel', 'Menukaart | ' . $naam . ($plaats ? ' in ' . $plaats : ''))
@section('omschrijving', 'De menukaart van ' . $naam . ($plaats ? ' in ' . $plaats : '') . ': verse pizza\'s en meer, direct online te bestellen.')

@section('extra-jsonld')
    <script type="application/ld+json">@json($menuJsonld)</script>
@endsection

@section('inhoud')
    @php
        /* Per template een eigen menuweergave: kaarten, klassieke lijst of tegels */
        $menuStijl = ['nero' => 'klassiek', 'napoli' => 'klassiek', 'blocco' => 'tegels', 'puro' => 'tegels'][$themaId] ?? 'kaarten';
        $held = $menu->first();
    @endphp
    <style>
        .m-stip { flex: 1; border-bottom: 2px dotted color-mix(in srgb, var(--prijs) 45%, transparent); margin: 0 .6rem; transform: translateY(-.3rem); }
    </style>

    <!-- Hero-banner zoals een moderne bestelshop -->
    <section class="m-held">
        <img class="b-foto absolute inset-0" data-parallax src="{{ $held ? $fotos[$held->id] : asset('assets/eten/topdown.jpg') }}" alt="">
        <div class="m-held-tint"></div>
        <div class="max-w-7xl mx-auto px-4 relative w-full pt-20 pb-5">
            <div class="flex items-end gap-4">
                <div class="m-logo">
                    @if($logo)
                        <img class="b-foto" src="{{ $logo }}" alt="Logo {{ $naam }}">
                    @else
                        <span class="b-kop text-3xl" style="color: var(--accent)">{{ mb_strtoupper(mb_substr($naam, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0 pb-0.5" style="color: #fff">
                    <h1 class="b-kop text-3xl sm:text-4xl leading-tight">{{ $naam }}</h1>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm font-bold mt-1.5 opacity-95">
                        <span style="color: {{ $online ? '#7ADC96' : '#f3b1b1' }}">● {{ $online ? 'Open' : 'Gesloten' }}</span>
                        @if(! $alleenAfhalen && $tiers->isNotEmpty())
                            <span><i class="fa-solid fa-motorcycle" aria-hidden="true"></i> Bezorging vanaf &euro; {{ number_format($tiers->first()['kosten'] / 100, 2, ',', '') }}</span>
                        @endif
                        <span><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Afhalen gratis</span>
                        @if($vandaag['open'])<span><i class="fa-solid fa-clock" aria-hidden="true"></i> Vandaag {{ $vandaag['van'] }} - {{ $vandaag['tot'] }}</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Zoeken + categorietabs, blijft plakken onder de navigatie -->
    <div class="m-plakbalk">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-3">
            <div class="relative flex-1 min-w-0 max-w-sm">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 opacity-45"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                <input id="menuZoek" class="b-veld !py-2 !pl-10 !text-sm" placeholder="Zoeken bij {{ $naam }}">
            </div>
            @if($categorieen->count() > 1)
                <div class="m-tabs flex gap-2">
                    @foreach($categorieen as $cat)
                        <a href="#cat-{{ Str::slug($cat) }}" class="b-chip !py-1.5">{{ $cat }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-[1fr_21rem] gap-7 items-start">
        <div class="min-w-0">
            @if($menu->isEmpty())
                <div class="b-kaart p-6 text-center opacity-75">De menukaart wordt nog gevuld. Kom snel terug!</div>
            @else
                @if($menu->count() >= 4)
                    <section data-anim data-cat-sectie class="mb-8">
                        <h2 class="b-kop text-2xl mb-4 {{ $menuStijl === 'klassiek' ? 'text-center uppercase tracking-widest !text-xl' : '' }}">Uitgelicht</h2>
                        <div class="m-uitgelicht">
                            @foreach($menu->take(3) as $m)
                                <button type="button" class="b-kaart b-gerecht text-left cursor-pointer menu-item overflow-hidden" data-item="{{ $m->id }}">
                                    <div class="aspect-[16/9] overflow-hidden">
                                        <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                                    </div>
                                    <div class="p-3.5">
                                        <div class="flex items-baseline justify-between gap-3">
                                            <p class="b-kop truncate">{{ $m->naam }}</p>
                                            <span class="b-kop b-prijs shrink-0">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                                        </div>
                                        @if($m->beschrijving)<p class="text-xs opacity-60 mt-0.5 truncate">{{ $m->beschrijving }}</p>@endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endif

                @foreach($menu->groupBy('categorie') as $categorie => $items)
                    <section id="cat-{{ Str::slug($categorie) }}" data-anim data-cat-sectie class="mb-9 scroll-mt-32 {{ $menuStijl === 'klassiek' ? 'max-w-2xl mx-auto' : '' }}">
                        <h2 class="b-kop text-2xl mb-4 {{ $menuStijl === 'klassiek' ? 'text-center uppercase tracking-widest !text-xl' : '' }}">{{ $categorie }}</h2>

                        @if($menuStijl === 'klassiek')
                            <!-- Klassieke kaart: naam, stippellijn, prijs -->
                            <div class="space-y-4">
                                @foreach($items as $m)
                                    <button type="button" class="w-full text-left cursor-pointer menu-item group" data-item="{{ $m->id }}">
                                        <div class="flex items-baseline">
                                            <span class="b-kop text-lg group-hover:underline underline-offset-4">{{ $m->naam }}</span>
                                            <span class="m-stip"></span>
                                            <span class="b-kop b-prijs shrink-0">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                                        </div>
                                        @if($m->beschrijving)
                                            <p class="text-sm opacity-55 italic">{{ $m->beschrijving }}</p>
                                        @elseif($m->ingredienten)
                                            <p class="text-sm opacity-55 italic">{{ implode(', ', $m->ingredienten) }}</p>
                                        @endif
                                        @if(is_array($m->allergenen) && count($m->allergenen))
                                            <p class="b-allergeen mt-0.5">Allergenen: {{ implode(', ', array_map('ucfirst', $m->allergenen)) }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @elseif($menuStijl === 'tegels')
                            <!-- Tegels: fotoblokken in een grid -->
                            <div class="grid grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($items as $m)
                                    <button type="button" class="b-kaart b-gerecht text-center p-3 cursor-pointer menu-item relative" data-item="{{ $m->id }}">
                                        <span class="absolute -top-2.5 -right-2 z-10 b-kop text-sm px-2 py-0.5" style="background: var(--accent); color: #fff; border-radius: var(--knop-radius)">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                                        <div class="b-fotovak aspect-[4/3] mb-2.5">
                                            <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                                        </div>
                                        <p class="b-kop">{{ $m->naam }}</p>
                                        @if($m->beschrijving)
                                            <p class="text-xs opacity-60 mt-1">{{ $m->beschrijving }}</p>
                                        @elseif($m->ingredienten)
                                            <p class="text-xs opacity-60 mt-1">{{ implode(', ', $m->ingredienten) }}</p>
                                        @endif
                                        @if(is_array($m->allergenen) && count($m->allergenen))
                                            <p class="b-allergeen mt-1">Allergenen: {{ implode(', ', array_map('ucfirst', $m->allergenen)) }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <!-- Kaarten: rijen met foto -->
                            <div class="grid sm:grid-cols-2 gap-3.5">
                                @foreach($items as $m)
                                    <button type="button" class="b-kaart b-gerecht text-left p-3 flex items-center gap-4 cursor-pointer menu-item" data-item="{{ $m->id }}">
                                        <div class="b-fotovak w-24 h-20 shrink-0">
                                            <img class="b-foto" src="{{ $fotos[$m->id] }}" alt="{{ $m->naam }}" loading="lazy">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <p class="b-kop text-lg truncate">{{ $m->naam }}</p>
                                                <span class="b-kop b-prijs shrink-0">&euro; {{ number_format($m->prijs / 100, 2, ',', '') }}</span>
                                            </div>
                                            @if($m->beschrijving)
                                                <p class="text-sm opacity-65">{{ $m->beschrijving }}</p>
                                            @elseif($m->ingredienten)
                                                <p class="text-sm opacity-65">{{ implode(', ', $m->ingredienten) }}</p>
                                            @endif
                                            @if(is_array($m->allergenen) && count($m->allergenen))
                                                <p class="b-allergeen mt-0.5">Allergenen: {{ implode(', ', array_map('ucfirst', $m->allergenen)) }}</p>
                                            @endif
                                        </div>
                                        <span class="shrink-0 w-9 h-9 grid place-items-center text-white font-black text-lg" style="background: var(--accent); border-radius: var(--knop-radius)">+</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endforeach
            @endif
        </div>

        <!-- Sticky winkelmandje (desktop) -->
        <aside class="hidden lg:block sticky" style="top: calc(var(--plak-top) + 4rem)">
            <div class="b-kaart p-5">
                <p class="b-kop text-xl mb-3">Winkelmandje</p>
                @unless($alleenAfhalen)
                    <div class="m-typetoggle grid grid-cols-2 gap-1 p-1 mb-4" id="zijType">
                        <button type="button" class="aan" data-zij-type="bezorgen"><i class="fa-solid fa-motorcycle" aria-hidden="true"></i> Bezorgen</button>
                        <button type="button" data-zij-type="afhalen"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Afhalen</button>
                    </div>
                @endunless
                <div id="zijLeeg" class="text-center py-8 opacity-60">
                    <i class="fa-solid fa-basket-shopping text-3xl block mb-2 opacity-70" aria-hidden="true"></i>
                    <p class="b-kop">Vul je mandje</p>
                    <p class="text-xs mt-1">Je winkelmandje is nog leeg</p>
                </div>
                <div id="zijMand" class="space-y-3"></div>
                <div id="zijOnder" class="hidden border-t mt-4 pt-3" style="border-color: color-mix(in srgb, var(--tekst) 14%, transparent)">
                    <div class="flex justify-between font-extrabold"><span>Subtotaal</span><span id="zijTotaal"></span></div>
                    @if(! $alleenAfhalen && $tiers->isNotEmpty())
                        <p class="text-xs opacity-60 mt-1">Bezorgkosten vanaf &euro; {{ number_format($tiers->first()['kosten'] / 100, 2, ',', '') }}, afhalen gratis</p>
                    @endif
                    <button type="button" id="zijAfrekenen" class="b-knop w-full !py-3 mt-3.5">Afrekenen</button>
                </div>
            </div>
        </aside>
    </div>

    <!-- Gerecht-detail -->
    <div id="itemSheetVak" class="hidden">
        <div class="b-sheet-achtergrond" data-sluit-item></div>
        <div class="b-sheet" id="itemSheet"></div>
    </div>

    <!-- Mandje-balk (mobiel) -->
    <div id="mandBalk" class="b-mandbalk hidden lg:!hidden">
        <button type="button" id="mandOpen" class="b-knop w-full !py-3.5 !text-base shadow-xl">
            <span id="mandAantal" class="w-6 h-6 grid place-items-center rounded-full bg-white/25 text-sm"></span>
            Bekijk bestelling
            <span id="mandTotaal" class="ml-auto"></span>
        </button>
    </div>

    <!-- Mandje en checkout -->
    <div id="mandSheetVak" class="hidden">
        <div class="b-sheet-achtergrond" data-sluit-mand></div>
        <div class="b-sheet" id="mandSheet"></div>
    </div>
@endsection

@section('scripts')
    <script>
        window.BESTEL = {
            slug: @json($slug),
            online: @json($online),
            alleenAfhalen: @json($alleenAfhalen),
            plaatsUrl: @json(route('bestel.plaats', $slug)),
            laagsteBezorgkosten: @json($tiers->isNotEmpty() ? (int) $tiers->first()['kosten'] : 0),
            menu: @json($menuJs),
        };
    </script>
    <script src="{{ asset('assets/bestel.js') }}?v={{ filemtime(public_path('assets/bestel.js')) }}"></script>
@endsection
