<!DOCTYPE html>
<html lang="nl" class="js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Stijlgids | Shop &amp; Eat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/systeem.css') }}?v={{ filemtime(public_path('assets/stijl/systeem.css')) }}">

    <style>
        /* Alleen voor deze gids: het raster waarin de voorbeelden staan */
        .gids-kop { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
        .nrs { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--omgekeerd); color: var(--omgekeerd-ink); font-size: 12px; font-weight: 500; }
        .rooster { display: grid; gap: var(--gap); }
        .r2 { grid-template-columns: repeat(2, 1fr); }
        .r3 { grid-template-columns: repeat(3, 1fr); }
        .r4 { grid-template-columns: repeat(4, 1fr); }
        .r6 { grid-template-columns: repeat(6, 1fr); }
        @media (max-width: 980px) { .r4, .r6 { grid-template-columns: repeat(2, 1fr); } .r3 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 620px) { .rooster { grid-template-columns: 1fr !important; } }

        /* Kleurstaal */
        .staal { border-radius: var(--r-blok); overflow: hidden; border: 1px solid var(--line); }
        .staal .vlakje { height: 76px; }
        .staal .uitleg { padding: 10px 12px 12px; background: var(--card); }
        .staal code { font-size: 11px; color: var(--ink3); }

        /* Demo-vak: het voorbeeld zelf, met een label eronder */
        .demo { background: var(--soft); border-radius: var(--r-blok); padding: 22px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .demo.kolom { flex-direction: column; align-items: stretch; }
        .bijschrift { font-size: 12px; color: var(--ink3); margin-top: 8px; }

        /* Codefragment */
        code, .code { font-family: ui-monospace, 'Cascadia Mono', 'Courier New', monospace; }
        .code { display: block; background: var(--soft); border-radius: var(--r-mini); padding: 12px 14px; font-size: 12px; color: var(--ink2); overflow-x: auto; white-space: pre; }

        /* Vergelijkingstabel oud → nieuw */
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        th { text-align: left; font-weight: 500; font-size: 12px; color: var(--ink3); padding: 0 12px 10px 0; border-bottom: 1px solid var(--line); }
        td { padding: 11px 12px 11px 0; border-bottom: 1px solid var(--line); vertical-align: top; color: var(--ink2); }
        td:first-child { color: var(--ink); }
        td b { font-weight: 500; color: var(--ink); }

        .schaalrij { display: flex; align-items: baseline; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--line); flex-wrap: wrap; }
        .schaalrij .meta { font-size: 12px; color: var(--ink3); white-space: nowrap; }
        .radiusblok { height: 64px; background: var(--omgekeerd); }
    </style>
</head>
<body>

<div class="page">

    {{-- ══════════ KOP ══════════ --}}
    <section class="vlak" id="top">
        <div class="gids-kop">
            <div style="max-width:60ch">
                <span class="tag in">Designsysteem 3.0</span>
                <h1 class="in d1" style="margin-top:18px">De stijl van <b>het hele platform</b>.</h1>
                <p class="lead in d2" style="margin-top:18px">
                    Dit is de stijl uit <code>shop-and-eat.html</code>, vastgelegd als systeem. Rustig, monochroom en zakelijk:
                    een warm vel met witte vlakken, lichte koppen, ronde pillen, één warme hoofdkleur en groen voor status. Alles wat we hierna
                    bouwen of verbouwen, van de bestelpagina tot het dashboard, volgt deze pagina.
                </p>
                <div class="in d2" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:24px">
                    <button type="button" class="btn dark" id="themaKnop">Bekijk in donker <i class="ar fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    <a class="btn line" href="/shop-and-eat.html" target="_blank" rel="noopener">Bekijk de bron</a>
                </div>
            </div>
            <div class="glas" style="width:min(100%,300px);background:var(--omgekeerd);border-color:var(--omgekeerd)">
                <p class="label" style="color:color-mix(in srgb, var(--omgekeerd-ink) 55%, transparent)">In het kort</p>
                <p style="font-size:14px;line-height:1.7;color:color-mix(in srgb, var(--omgekeerd-ink) 85%, transparent)">
                    Eén lettertype (Figtree), drie tekststerktes, drie vlakken, één hoofdkleur, één tweede kleur en groen voor status.
                    Geen kleurexplosies, geen sierlijke letters, geen schaduwen.
                </p>
            </div>
        </div>
    </section>

    {{-- ══════════ 1. KLEUR ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">1</span>
                <h2 style="margin-top:14px">Kleur</h2>
                <p class="lead" style="margin-top:10px">
                    Drie vlakken van buiten naar binnen, drie tekststerktes, één lijn, een hoofdkleur met een tweede kleur, en groen voor status. Meer is er niet,
                    en dat is precies wat het rustig houdt.
                </p>
            </div>
        </div>

        <div class="rooster r4">
            @foreach([
                ['--page', '#f6f2ec', '#1c1c1c', 'De pagina zelf, het warme vel waar de vlakken op liggen'],
                ['--card', '#ffffff', '#282828', 'Kaartvlakken: de witte panelen'],
                ['--soft', '#f4eee6', '#333333', 'Zacht vlak binnen een kaart: blokken, demo-vakken, inactieve tabs'],
                ['--line', '#e7dfd5', '#3f3f3f', 'Randen en scheidingslijnen, nooit zwaarder dan 1px'],
            ] as [$naam, $licht, $donker, $waarvoor])
                <div class="staal">
                    <div class="vlakje" style="background:var({{ $naam }})"></div>
                    <div class="uitleg">
                        <b style="font-weight:500">{{ $naam }}</b>
                        <p class="mini" style="margin-top:4px">{{ $waarvoor }}</p>
                        <code>licht {{ $licht }} · donker {{ $donker }}</code>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rooster r4" style="margin-top:var(--gap)">
            @foreach([
                ['--ink', '#1b1714', '#e8e8e8', 'Koppen en alles wat je echt moet lezen'],
                ['--ink2', '#5e5650', '#a3a3a3', 'Lopende tekst en uitleg'],
                ['--ink3', '#8b837b', '#828282', 'Labels, bijschriften, wat mag wegvallen'],
                ['--green', '#8ee08f', '#8ee08f', 'Alleen voor status: live, betaald, gelukt'],
            ] as [$naam, $licht, $donker, $waarvoor])
                <div class="staal">
                    <div class="vlakje" style="background:var({{ $naam }})"></div>
                    <div class="uitleg">
                        <b style="font-weight:500">{{ $naam }}</b>
                        <p class="mini" style="margin-top:4px">{{ $waarvoor }}</p>
                        <code>licht {{ $licht }} · donker {{ $donker }}</code>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rooster r2" style="margin-top:var(--gap)">
            @foreach([
                ['--accent', '#e8432e', '#f0553f', 'De hoofdkleur "Tomaat": knoppen, actieve navigatie, grafieken, de begroeting. Een zaak die zelf een kleur koos, ziet in het dashboard die kleur hier.'],
                ['--accent-2', '#f5b82e', '#f5b82e', 'De tweede kleur: één blok per scherm dat mag opvallen zonder actie te zijn, zoals de inkoop-oproep'],
            ] as [$naam, $licht, $donker, $waarvoor])
                <div class="staal">
                    <div class="vlakje" style="background:var({{ $naam }})"></div>
                    <div class="uitleg">
                        <b style="font-weight:500">{{ $naam }}</b>
                        <p class="mini" style="margin-top:4px">{{ $waarvoor }}</p>
                        <code>licht {{ $licht }} · donker {{ $donker }}</code>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="demo" style="margin-top:var(--gap);justify-content:space-between">
            <div style="max-width:46ch">
                <b style="font-weight:500">--omgekeerd</b> en <b style="font-weight:500">--omgekeerd-ink</b>
                <p class="mini" style="margin-top:4px">
                    Het tegenovergestelde van de pagina. Sinds de kleurkeuze is dat de hoofdkleur met witte tekst, in beide thema's; bij een lichte zaak-kleur wordt de tekst donker.
                    Hoofdknoppen, actieve navigatie, volle tags en omgekeerde vlakken gebruiken dit paar, zodat ze in
                    beide thema's de sterkste vlek op het scherm blijven. Gebruik <code>--dark</code> alleen als iets
                    echt altijd donker moet zijn, zoals een vlak over een foto.
                </p>
            </div>
            <div style="display:flex;gap:10px;align-items:center">
                <span class="btn dark">Hoofdactie</span>
                <span class="tag vol">Volle tag</span>
            </div>
        </div>

        <p class="bijschrift" style="margin-top:18px">
            Donker komt er gratis bij: de tokens wisselen mee met het systeemthema, of met
            <code>data-theme="dark"</code> op <code>&lt;html&gt;</code>. Schrijf dus nooit een vaste kleur in een component.
        </p>
    </section>

    {{-- ══════════ 2. TYPOGRAFIE ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">2</span>
                <h2 style="margin-top:14px">Typografie</h2>
                <p class="lead" style="margin-top:10px">
                    Figtree, in vier gewichten. Koppen zijn licht (300) en strak gespatieerd; wil je een woord laten opvallen,
                    zet er dan <code>&lt;b&gt;</code> omheen, dat maakt het 500. Nooit vet als hele kop.
                </p>
            </div>
            <span class="tag">Figtree 300 · 400 · 500 · 600</span>
        </div>

        <div class="schaalrij">
            <h1 style="flex:1;min-width:280px">Online bestellen, <b>zonder gedoe</b>.</h1>
            <span class="meta">h1 · 300 · clamp 38 tot 72px · -.03em</span>
        </div>
        <div class="schaalrij">
            <h2 style="flex:1;min-width:280px">Een dashboard dat <b>meedenkt</b></h2>
            <span class="meta">h2 · 300 · clamp 26 tot 42px · -.02em</span>
        </div>
        <div class="schaalrij">
            <h3 style="flex:1;min-width:280px">Klanten bestellen in een paar tikken</h3>
            <span class="meta">h3 · 300 · clamp 20 tot 27px · -.01em</span>
        </div>
        <div class="schaalrij">
            <p class="lead" style="flex:1;min-width:280px">Lopende tekst in een <b>lead</b>: rustig grijs, maximaal 50 tekens breed, zodat een regel prettig leest.</p>
            <span class="meta">.lead · 15px · ink2</span>
        </div>
        <div class="schaalrij">
            <p style="flex:1;min-width:280px">Standaard bodytekst is 15px met regelafstand 1.5. Dit is de maat voor bijna alles in de app.</p>
            <span class="meta">body · 15px · 1.5</span>
        </div>
        <div class="schaalrij">
            <p class="small" style="flex:1;min-width:280px">Kleine tekst voor bijschriften en voetnoten.</p>
            <span class="meta">.small · 13px · ink3</span>
        </div>
        <div class="schaalrij" style="border-bottom:0">
            <p class="label" style="flex:1;min-width:280px">LABEL BOVEN EEN VELD</p>
            <span class="meta">.label · 11px · ink3</span>
        </div>
    </section>

    {{-- ══════════ 3. VORM EN RITME ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">3</span>
                <h2 style="margin-top:14px">Vorm en ritme</h2>
                <p class="lead" style="margin-top:10px">
                    Alles is rond. Hoe groter het vlak, hoe ronder de hoek. Tussen vlakken zit altijd dezelfde 12px,
                    ook aan de rand van de pagina: daardoor lijkt het scherm één samenhangend geheel.
                </p>
            </div>
        </div>

        <div class="rooster r6">
            @foreach([
                ['--r-pill', '999px', 'Knoppen, tags, navigatie'],
                ['--r-vlak', '28px', 'Paginavlakken'],
                ['--r-kaart', '22px', 'Kaarten in een vlak'],
                ['--r-blok', '18px', 'Blokken en foto\'s'],
                ['--r-klein', '14px', 'Tabs, regels, keuzes'],
                ['--r-mini', '12px', 'Chips, kleine foto\'s'],
            ] as [$naam, $waarde, $waarvoor])
                <div>
                    <div class="radiusblok" style="border-radius:var({{ $naam }})"></div>
                    <p style="margin-top:10px;font-weight:500;font-size:13.5px">{{ $waarde }}</p>
                    <p class="mini">{{ $waarvoor }}</p>
                    <code style="font-size:11px;color:var(--ink3)">{{ $naam }}</code>
                </div>
            @endforeach
        </div>

        <div class="demo" style="margin-top:24px;gap:var(--gap)">
            <div style="background:var(--card);border-radius:var(--r-vlak);padding:20px;flex:1;min-width:200px">
                <p style="font-weight:500">Vlak</p>
                <p class="mini">28px, 12px ruimte eromheen</p>
            </div>
            <div style="background:var(--card);border-radius:var(--r-vlak);padding:20px;flex:1;min-width:200px">
                <p style="font-weight:500">Vlak</p>
                <p class="mini">Altijd dezelfde afstand, ook aan de paginarand</p>
            </div>
        </div>
        <p class="bijschrift">Ruimte binnen een vlak schaalt mee met het scherm: <code>clamp(26px, 4.2vh, 56px)</code> verticaal, <code>clamp(24px, 4vw, 60px)</code> horizontaal.</p>
    </section>

    {{-- ══════════ 4. KNOPPEN ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">4</span>
                <h2 style="margin-top:14px">Knoppen</h2>
                <p class="lead" style="margin-top:10px">
                    Pilvormig, 46px hoog, tekst op 500. Eén donkere knop per scherm is de hoofdactie; de rest is een lijn.
                    Bij hover komt een knop één pixel omhoog, meer beweging is niet nodig.
                </p>
            </div>
        </div>

        <div class="rooster r2">
            <div>
                <div class="demo">
                    <button type="button" class="btn dark">Gratis starten <i class="ar fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    <button type="button" class="btn line">Bekijk voorbeeld</button>
                    <button type="button" class="btn line klein">Kleiner</button>
                    <button type="button" class="btn dark uit">Uitgeschakeld</button>
                </div>
                <p class="bijschrift">Op een licht vlak: <code>.btn.dark</code> en <code>.btn.line</code></p>
            </div>
            <div>
                <div class="demo" style="background:var(--dark)">
                    <button type="button" class="btn wh">Gratis starten</button>
                    <button type="button" class="btn glass">Plan een demo</button>
                </div>
                <p class="bijschrift">Op donker of op een foto: <code>.btn.wh</code> en <code>.btn.glass</code></p>
            </div>
        </div>
    </section>

    {{-- ══════════ 5. TAGS EN STATUS ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">5</span>
                <h2 style="margin-top:14px">Tags en status</h2>
                <p class="lead" style="margin-top:10px">
                    Een tag benoemt, een status meldt. Groen is het enige gekleurde signaal in het systeem en betekent altijd
                    hetzelfde: het staat aan, het is binnen, het is gelukt.
                </p>
            </div>
        </div>

        <div class="demo">
            <span class="tag">Designsysteem</span>
            <span class="tag vol">Actief</span>
            <span class="tag ok">Betaald</span>
            <span class="status live"><i aria-hidden="true"></i> Online, neemt bestellingen aan</span>
            <span class="status uit"><i aria-hidden="true"></i> Offline</span>
        </div>
    </section>

    {{-- ══════════ 6. VLAKKEN EN KAARTEN ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">6</span>
                <h2 style="margin-top:14px">Vlakken en kaarten</h2>
                <p class="lead" style="margin-top:10px">
                    Een kaart is een zacht vlak zonder rand en zonder schaduw. Diepte maak je met kleur, niet met slagschaduw:
                    grijs vel, wit vlak, zacht blok, en zwart voor wat eruit moet springen.
                </p>
            </div>
        </div>

        <div class="rooster r3">
            <div class="kaart hover">
                <p style="font-weight:500">Zachte kaart</p>
                <p class="mini">De standaard. Komt omhoog bij hover als hij klikbaar is.</p>
                <code style="font-size:11px;color:var(--ink3)">.kaart.hover</code>
            </div>
            <div class="kaart omgekeerd">
                <p style="font-weight:500">Omgekeerde kaart</p>
                <p class="mini">Voor het cijfer of de actie die eruit moet springen. Hooguit één per scherm.</p>
                <code style="font-size:11px;color:rgba(255,255,255,.45)">.kaart.omgekeerd</code>
            </div>
            <div class="kaart omkeerbaar">
                <p style="font-weight:500">Keert om bij hover</p>
                <p class="mini">Wordt zwart als je erover gaat. Handig voor stappen en keuzeblokken.</p>
                <code style="font-size:11px;color:var(--ink3)">.kaart.omkeerbaar</code>
            </div>
        </div>

        <div class="rooster r2" style="margin-top:var(--gap)">
            <div>
                <div class="scherm">
                    <div class="url"><i aria-hidden="true"></i> pizzeriasole.mijnpizzeria.nl</div>
                    <div class="photo" style="aspect-ratio:16/10;--ph:linear-gradient(160deg,#f8f8f8,#dedede)"></div>
                </div>
                <p class="bijschrift">Schermafbeelding in een browserbalkje: <code>.scherm</code></p>
            </div>
            <div>
                <div class="photo tekst" style="aspect-ratio:16/10">
                    <h3 style="color:#fff;max-width:14ch">Foto met tekst erover</h3>
                    <p style="color:rgba(255,255,255,.85);font-size:13px;margin-top:6px">Het verloop zorgt dat tekst altijd leesbaar blijft, ook op een lichte foto.</p>
                </div>
                <p class="bijschrift">Foto met verloop: <code>.photo.tekst</code>. Zonder afbeelding blijft het kleurvlak staan, dus nooit een wit gat.</p>
            </div>
        </div>
    </section>

    {{-- ══════════ 7. FORMULIER ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">7</span>
                <h2 style="margin-top:14px">Formulier</h2>
                <p class="lead" style="margin-top:10px">
                    Velden zijn een lijn, geen doos. Dat scheelt randen op het scherm en past bij de lichte koppen.
                    Het label staat klein bovenaan, de lijn wordt zwart zodra je in het veld staat.
                </p>
            </div>
        </div>

        <div class="rooster r3">
            <div class="demo kolom">
                <div class="fld">
                    <label for="v1">Naam van je zaak</label>
                    <input id="v1" type="text" placeholder="Pizzeria Sole">
                </div>
                <div class="fld">
                    <label for="v2">E-mailadres</label>
                    <input id="v2" type="email" placeholder="mario@pizzeriasole.nl">
                </div>
                <button type="button" class="btn dark" style="margin-top:4px">Opslaan</button>
            </div>

            <div class="demo kolom op-donker" style="background:var(--dark)">
                <p class="label" style="color:rgba(255,255,255,.55)">Op een donkere ondergrond</p>
                <div class="fld">
                    <label for="v3">Naam</label>
                    <input id="v3" type="text" placeholder="Mario Rossi">
                </div>
                <button type="button" class="btn wh" style="margin-top:4px">Versturen</button>
            </div>

            <div class="demo kolom">
                <button type="button" class="keuze aan">
                    <span class="punt" style="flex-direction:row"><i aria-hidden="true">✓</i></span>
                    <span style="flex:1"><b>Bezorger</b><br><span>Pakt ritten op en vinkt af</span></span>
                </button>
                <button type="button" class="keuze">
                    <span class="punt" style="flex-direction:row"><i aria-hidden="true">·</i></span>
                    <span style="flex:1"><b>Keuken</b><br><span>Zet bestellingen door</span></span>
                </button>
                <p class="bijschrift" style="margin:0">Keuzekaart in plaats van een radio: <code>.keuze</code></p>
            </div>
        </div>
    </section>

    {{-- ══════════ 8. GEGEVENS ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">8</span>
                <h2 style="margin-top:14px">Gegevens tonen</h2>
                <p class="lead" style="margin-top:10px">
                    Cijfers groot en licht, uitleg klein en grijs eronder. Stappen en lijstregels zijn zachte blokken;
                    de actieve stap keert om naar zwart.
                </p>
            </div>
        </div>

        <div class="rooster r4">
            <div class="stat"><span class="n">0%</span><span class="c">commissie per bestelling</span></div>
            <div class="stat"><span class="n">&euro; 24,95</span><span class="c">per maand, alles erin</span></div>
            <div class="stat"><span class="n">10 min</span><span class="c">van menukaart tot live</span></div>
            <div class="stat"><span class="n">1.200+</span><span class="c">bestellingen per maand</span></div>
        </div>

        <div class="rooster r4" style="margin-top:28px">
            <div class="stap aan">
                <div class="h">Aanmelden <span>01</span></div>
                <p>De actieve stap keert om naar zwart.</p>
            </div>
            <div class="stap">
                <div class="h">Inrichten <span>02</span></div>
                <p>Menukaart, openingstijden en stijl.</p>
            </div>
            <div class="stap">
                <div class="h">Live <span>03</span></div>
                <p>Je bestelpagina staat online.</p>
            </div>
            <div class="stap">
                <div class="h">Groeien <span>04</span></div>
                <p>Spaarpunten en vaste klanten.</p>
            </div>
        </div>

        <div class="rooster r2" style="margin-top:28px">
            <div>
                <div class="regel" style="margin-bottom:8px">
                    <span class="status live"><i aria-hidden="true"></i></span>
                    <span class="op"><b>#412 Sanne</b><br><span class="mini">2× Margherita, 1× Cola</span></span>
                    <span style="font-weight:500">&euro; 21,50</span>
                </div>
                <div class="regel">
                    <span class="status uit"><i aria-hidden="true"></i></span>
                    <span class="op"><b>#411 Ahmed</b><br><span class="mini">1× Funghi</span></span>
                    <span style="font-weight:500">&euro; 11,00</span>
                </div>
                <p class="bijschrift">Lijstregel: <code>.regel</code>, de werkvorm voor bestellingen en teamleden</p>
            </div>
            <div>
                <div class="rooster r3" style="gap:16px">
                    <span class="punt"><i aria-hidden="true">◷</i><b>Snel</b>Binnen 10 minuten live</span>
                    <span class="punt"><i aria-hidden="true">◎</i><b>Eigen</b>Jouw klanten, jouw data</span>
                    <span class="punt"><i aria-hidden="true">↗</i><b>Groei</b>Meer vaste klanten</span>
                </div>
                <p class="bijschrift">Icoonpunten: <code>.punt</code>, keert om bij hover</p>
            </div>
        </div>
    </section>

    {{-- ══════════ 9. BEWEGING ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">9</span>
                <h2 style="margin-top:14px">Beweging</h2>
                <p class="lead" style="margin-top:10px">
                    Eén curve voor alles: <code>cubic-bezier(.2, .7, .2, 1)</code>. Blokken komen 18 tot 22px omhoog terwijl ze
                    verschijnen, knoppen bewegen één pixel. Wie in zijn systeem minder beweging heeft aangezet, krijgt niets.
                </p>
            </div>
        </div>

        <div class="demo kolom">
            <span class="code">.in          eenmalig binnenkomen, met .d1 / .d2 / .d3 als vertraging
.rv + .toon  onthullen zodra het blok in beeld scrollt
--soepel     cubic-bezier(.2, .7, .2, 1), de enige curve die we gebruiken</span>
            <button type="button" class="btn line" id="speelKnop" style="align-self:flex-start">Speel de animatie af</button>
            <div class="rooster r3" id="speelVak" style="margin-top:4px">
                <div class="kaart"><p style="font-weight:500">Eerst</p><p class="mini">geen vertraging</p></div>
                <div class="kaart"><p style="font-weight:500">Dan</p><p class="mini">.d1, 120ms later</p></div>
                <div class="kaart"><p style="font-weight:500">Tot slot</p><p class="mini">.d2, 240ms later</p></div>
            </div>
        </div>
    </section>

    {{-- ══════════ 10. OVERSTAP ══════════ --}}
    <section class="vlak">
        <div class="head">
            <div>
                <span class="nrs">10</span>
                <h2 style="margin-top:14px">Wat verandert er</h2>
                <p class="lead" style="margin-top:10px">
                    Het platform draait nu op de warme Italiaanse stijl: ivoor, terracotta en Young Serif. Dat gaat eruit.
                    Hieronder staat waar elk oud onderdeel naartoe gaat.
                </p>
            </div>
            <span class="tag">huisstijl 2.0 → designsysteem 3.0</span>
        </div>

        <table>
            <thead>
                <tr><th style="width:26%">Nu</th><th style="width:26%">Wordt</th><th>Waarom</th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['Young Serif (koppen)', 'Figtree 300', 'Eén lettertype voor alles. Koppen worden licht in plaats van decoratief.'],
                    ['Inter Tight (tekst)', 'Figtree 400 / 500', 'Scheelt een tweede webfont en oogt strakker.'],
                    ['crema #FAF8F4', '--page #ececec', 'Neutraal grijs vel in plaats van ivoor.'],
                    ['wit + crema-dark rand', '--card zonder rand', 'Vlakken scheiden op kleur, niet op lijnen.'],
                    ['tomato #B04A3F', '--ink #141414', 'De hoofdactie wordt zwart. Rood was overal en trok te veel aandacht.'],
                    ['basil #2C7A4B', '--green #8ee08f', 'Alleen nog als signaal: live, betaald, gelukt.'],
                    ['gold #B97F10', 'vervalt', 'Accenten lopen via zwart of groen.'],
                    ['rounded-2xl (16px)', '--r-vlak 28px, --r-blok 18px', 'Rondere vlakken, kleinere binnenblokken.'],
                    ['btn-primary (rood, 10px)', '.btn.dark (pil, 46px)', 'Alle knoppen worden pillen.'],
                    ['schaduw op kaarten', 'geen schaduw', 'Diepte komt uit de kleurtrap page → card → soft.'],
                    ['emoji en mascottes', 'blijven weg', 'Stond al in de huisstijl en blijft zo.'],
                ] as [$oud, $nieuw, $waarom])
                    <tr>
                        <td>{{ $oud }}</td>
                        <td><b>{{ $nieuw }}</b></td>
                        <td>{{ $waarom }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="rooster r3" style="margin-top:32px">
            <div class="kaart omgekeerd">
                <span class="nrs">1</span>
                <p style="font-weight:500;margin-top:8px">Dashboard, overzicht <span class="tag ok" style="margin-left:6px">gedaan</span></p>
                <p class="mini">De schil en het eerste paneel staan om. De rest van het dashboard loopt mee via de brug in assets/stijl/dashboard.css.</p>
            </div>
            <div class="kaart">
                <span class="nrs">2</span>
                <p style="font-weight:500;margin-top:8px">De rest van het dashboard</p>
                <p class="mini">Bestellingen, menukaart, team, instellingen en inkoop, daarna de onboarding. Elk paneel dat om is, mag zijn brugregel kwijt.</p>
            </div>
            <div class="kaart">
                <span class="nrs">3</span>
                <p style="font-weight:500;margin-top:8px">Site en bestelpagina's</p>
                <p class="mini">De marketingsite, en als laatste de bestelpagina's per template. Dat is wat klanten zien, dus daar willen we geen verrassingen.</p>
            </div>
        </div>

        <div class="kaart omgekeerd" style="margin-top:var(--gap)">
            <p style="font-weight:500">Twee regels om te onthouden</p>
            <p class="mini">
                Laad <code style="color:#fff">assets/stijl/systeem.css</code> nooit samen met het oude
                <code style="color:#fff">assets/onboarding/style.css</code> op één pagina: beide kennen een <code style="color:#fff">.step</code>
                en knopklassen. Een pagina gaat in één keer over.
            </p>
            <p class="mini">
                Schrijf in componenten nooit een vaste kleur, alleen tokens. Dan blijft donker werken en kunnen we later
                nog bijstellen zonder alles na te lopen.
            </p>
        </div>
    </section>

    {{-- ══════════ SLOT ══════════ --}}
    <section class="vlak donker">
        <div class="midden" style="padding:clamp(20px,5vh,60px) 0">
            <span class="tag" style="background:transparent;border-color:rgba(255,255,255,.25);color:rgba(255,255,255,.7)">Klaar om te bouwen</span>
            <h2 style="color:#fff;max-width:20ch">Vanaf hier bouwen we <b>alles zo</b>.</h2>
            <p style="color:rgba(255,255,255,.7);max-width:46ch">
                Deze pagina is de bron. Wijkt iets af, dan is deze pagina leidend, niet het bestaande scherm.
            </p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:8px">
                <a class="btn wh" href="#top">Terug naar boven</a>
                <a class="btn glass" href="/shop-and-eat.html" target="_blank" rel="noopener">Bekijk de bron</a>
            </div>
        </div>
    </section>

</div>

<script>
    /* Thema wisselen, zodat je donker kunt controleren zonder je systeem om te zetten */
    const themaKnop = document.querySelector('#themaKnop');
    const wortel = document.documentElement;
    const donkerNu = () => wortel.dataset.theme
        ? wortel.dataset.theme === 'dark'
        : window.matchMedia('(prefers-color-scheme: dark)').matches;
    const zetKnop = () => { themaKnop.firstChild.textContent = donkerNu() ? 'Bekijk in licht ' : 'Bekijk in donker '; };
    themaKnop.addEventListener('click', () => {
        wortel.dataset.theme = donkerNu() ? 'light' : 'dark';
        zetKnop();
    });
    zetKnop();

    /* Blokken onthullen zodra ze in beeld komen */
    const kijker = new IntersectionObserver((regels) => {
        regels.forEach((regel) => { if (regel.isIntersecting) { regel.target.classList.add('toon'); kijker.unobserve(regel.target); } });
    }, { threshold: .12 });
    document.querySelectorAll('.vlak').forEach((vlak, i) => {
        if (i === 0) return;                       /* de kop staat er meteen */
        vlak.querySelectorAll(':scope > .head, :scope > .rooster, :scope > .demo, :scope > table').forEach((el, j) => {
            el.classList.add('rv');
            el.style.setProperty('--d', (j * .08) + 's');
            kijker.observe(el);
        });
    });

    /* De animatiedemo opnieuw afspelen */
    document.querySelector('#speelKnop').addEventListener('click', () => {
        const kaarten = [...document.querySelectorAll('#speelVak .kaart')];
        kaarten.forEach((kaart, i) => {
            kaart.classList.remove('in', 'd1', 'd2');
            void kaart.offsetWidth;                /* herstart de animatie */
            kaart.classList.add('in', ...(i ? ['d' + i] : []));
        });
    });
</script>
</body>
</html>
