<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BestelController extends Controller
{
    /** Kleurpaletten per template, gelijk aan de previews in de onboarding */
    public const THEMAS = [
        'fresco' => ['kopFont' => '"Lilita One", cursive', 'bodyFont' => "'Nunito', sans-serif", 'font' => 'family=Lilita+One', 'upper' => false, 'pagina' => '#FFF6E8', 'kaart' => '#FFFFFF', 'tekst' => '#38221A', 'prijs' => 'rgba(56,34,26,.55)', 'radius' => '.7rem', 'knopRadius' => '9999px', 'rand' => 'none', 'accentKop' => true],
        'nero' => ['kopFont' => '"Playfair Display", Georgia, serif', 'bodyFont' => "Georgia, 'Times New Roman', serif", 'font' => 'family=Playfair+Display:wght@500;600;700', 'upper' => false, 'pagina' => '#171310', 'kaart' => '#1C1512', 'tekst' => '#F3EAD9', 'prijs' => '#C9A96A', 'radius' => '.45rem', 'knopRadius' => '.45rem', 'rand' => 'none', 'accentKop' => false],
        'napoli' => ['kopFont' => '"Libre Baskerville", "Times New Roman", serif', 'bodyFont' => "'Libre Baskerville', Georgia, serif", 'font' => 'family=Libre+Baskerville:ital,wght@0,400;0,700;1,400', 'upper' => false, 'pagina' => '#FFF8E7', 'kaart' => '#FFFDF6', 'tekst' => '#3A2A1A', 'prijs' => '#8A6A3B', 'radius' => '.35rem', 'knopRadius' => '.35rem', 'rand' => '1px solid #E8DCC2', 'accentKop' => true],
        'puro' => ['kopFont' => '"Nunito", sans-serif', 'bodyFont' => "'Nunito', sans-serif", 'font' => '', 'upper' => false, 'pagina' => '#F4F7F4', 'kaart' => '#FFFFFF', 'tekst' => '#233029', 'prijs' => '#5B6B60', 'radius' => '.7rem', 'knopRadius' => '.7rem', 'rand' => 'none', 'accentKop' => true],
        'blocco' => ['kopFont' => '"Archivo Black", "Arial Black", sans-serif', 'bodyFont' => 'Arial, Helvetica, sans-serif', 'font' => 'family=Archivo+Black', 'upper' => true, 'pagina' => '#FFFFFF', 'kaart' => '#FFFFFF', 'tekst' => '#111111', 'prijs' => '#111111', 'radius' => '0', 'knopRadius' => '0', 'rand' => '2px solid #111111', 'accentKop' => true],
        'retro' => ['kopFont' => '"Alfa Slab One", "Cooper Black", serif', 'bodyFont' => "'Nunito', sans-serif", 'font' => 'family=Alfa+Slab+One', 'upper' => false, 'pagina' => '#F5E0B4', 'kaart' => '#FBEED3', 'tekst' => '#5B3A21', 'prijs' => '#8A5A2B', 'radius' => '1.1rem', 'knopRadius' => '2rem', 'rand' => 'none', 'accentKop' => true],
    ];

    private const DAGEN = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
    private const DAGNAMEN = ['ma' => 'Maandag', 'di' => 'Dinsdag', 'wo' => 'Woensdag', 'do' => 'Donderdag', 'vr' => 'Vrijdag', 'za' => 'Zaterdag', 'zo' => 'Zondag'];

    public static function slugVoor(?string $naam): string
    {
        return substr(preg_replace('/[^a-z0-9]+/', '', strtolower(Str::ascii((string) $naam))), 0, 30) ?: 'jouwpizzeria';
    }

    /** Unieke slug: bij naamgenoten komt er een volgnummer achter (romapizza, romapizza2, ...) */
    public static function uniekeSlug(?string $naam): string
    {
        $basis = self::slugVoor($naam);
        $slug = $basis;
        $i = 2;
        while (User::where('slug', $slug)->exists()) {
            $slug = $basis . $i;
            $i++;
        }

        return $slug;
    }

    private function pizzeria(string $slug): User
    {
        $user = User::where('slug', $slug)->first();
        abort_unless($user, 404);

        return $user;
    }

    /**
     * Foto per gerecht: eerst op naam, dan op categorie, anders een pizzafoto
     * op toerbeurt. Zodra gerechten eigen foto's krijgen, winnen die.
     */
    public static function fotoVoor($item): string
    {
        if (!empty($item->foto)) {
            return $item->foto;
        }
        $naam = mb_strtolower($item->naam);
        $opNaam = [
            'margherita' => 'margherita.jpg',
            'salami' => 'pepperoni.jpg', 'pepperoni' => 'pepperoni.jpg', 'diavola' => 'pepperoni.jpg',
            'funghi' => 'rustiek.jpg', 'champignon' => 'rustiek.jpg',
            'formaggi' => 'topdown.jpg', 'kaas' => 'topdown.jpg',
            'calzone' => 'slice.jpg',
            'tonno' => 'donker.jpg', 'salmone' => 'donker.jpg',
            'tiramisu' => 'dessert.jpg',
        ];
        foreach ($opNaam as $trefwoord => $foto) {
            if (str_contains($naam, $trefwoord)) {
                return asset('assets/eten/' . $foto);
            }
        }
        $categorie = mb_strtolower($item->categorie);
        if (str_contains($categorie, 'drank') || str_contains($categorie, 'drink')) {
            return asset('assets/eten/drank.jpg');
        }
        if (str_contains($categorie, 'dessert') || str_contains($categorie, 'zoet') || str_contains($categorie, 'nagerecht')) {
            return asset('assets/eten/dessert.jpg');
        }
        $roulatie = ['pepperoni.jpg', 'margherita.jpg', 'rustiek.jpg', 'slice.jpg', 'topdown.jpg', 'donker.jpg'];

        return asset('assets/eten/' . $roulatie[$item->id % count($roulatie)]);
    }

    /** Alles wat de views nodig hebben: thema, zaakgegevens, tijden en menu */
    private function gegevens(User $user, string $slug): array
    {
        $ob = $user->onboarding ?? [];
        $thema = self::THEMAS[$ob['theme'] ?? 'fresco'] ?? self::THEMAS['fresco'];

        $dagen = [];
        foreach (self::DAGNAMEN as $key => $naam) {
            $open = (bool) (($ob['days'] ?? [])[$key] ?? false);
            $tijd = (($ob['hoursMode'] ?? 'same') === 'perday' && isset($ob['dayTimes'][$key]))
                ? $ob['dayTimes'][$key]
                : ['open' => $ob['open'] ?? '16:00', 'close' => $ob['close'] ?? '21:30'];
            $dagen[$key] = ['naam' => $naam, 'open' => $open, 'van' => $tijd['open'], 'tot' => $tijd['close']];
        }
        $vandaag = $dagen[self::DAGEN[(int) now()->dayOfWeek]];

        $menu = $user->menuItems()->where('actief', true)->orderBy('volgorde')->orderBy('id')->get();
        $tiers = collect($user->bezorgkosten ?? [])->sortBy('km')->values();

        return [
            'slug' => $slug,
            'pizzeria' => $user,
            'ob' => $ob,
            'thema' => $thema,
            'themaId' => isset(self::THEMAS[$ob['theme'] ?? '']) ? ($ob['theme'] ?? 'fresco') : 'fresco',
            'accent' => $ob['color'] ?? '#E63946',
            'naam' => $ob['name'] ?? 'Pizzeria',
            'logo' => $ob['logo'] ?? null,
            'plaats' => $ob['city'] ?? null,
            'telefoon' => $ob['phone'] ?? null,
            'adres' => trim(($ob['street'] ?? '') !== '' ? ($ob['street'] . ', ' . trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? ''))) : ''),
            'dagen' => $dagen,
            'vandaag' => $vandaag,
            'online' => (bool) $user->is_online,
            'alleenAfhalen' => ($user->order_mode ?? 'bezorgen_afhalen') === 'alleen_afhalen',
            'menu' => $menu,
            'fotos' => $menu->mapWithKeys(fn ($m) => [$m->id => self::fotoVoor($m)]),
            'categorieen' => $menu->pluck('categorie')->unique()->values(),
            'tiers' => $tiers,
            'jsonld' => $this->restaurantJsonLd($user, $ob, $dagen, $slug),
        ];
    }

    private function restaurantJsonLd(User $user, array $ob, array $dagen, string $slug): array
    {
        $engels = ['ma' => 'Monday', 'di' => 'Tuesday', 'wo' => 'Wednesday', 'do' => 'Thursday', 'vr' => 'Friday', 'za' => 'Saturday', 'zo' => 'Sunday'];
        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => $ob['name'] ?? 'Pizzeria',
            'url' => route('bestel.home', $slug),
            'servesCuisine' => 'Pizza',
            'priceRange' => '€',
        ];
        if (! empty($ob['phone'])) {
            $jsonld['telephone'] = $ob['phone'];
        }
        if (! empty($ob['street'])) {
            $jsonld['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $ob['street'],
                'postalCode' => $ob['zip'] ?? '',
                'addressLocality' => $ob['city'] ?? '',
                'addressCountry' => 'NL',
            ];
        }
        if ($user->lat !== null && $user->lng !== null) {
            $jsonld['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $user->lat, 'longitude' => $user->lng];
        }
        $spec = [];
        foreach ($dagen as $key => $dag) {
            if ($dag['open']) {
                $spec[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $engels[$key], 'opens' => $dag['van'], 'closes' => $dag['tot']];
            }
        }
        if ($spec) {
            $jsonld['openingHoursSpecification'] = $spec;
        }

        return $jsonld;
    }

    private function menuJsonLd($menu): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Menu',
            'hasMenuSection' => $menu->groupBy('categorie')->map(fn ($items, $categorie) => [
                '@type' => 'MenuSection',
                'name' => $categorie,
                'hasMenuItem' => $items->map(fn ($m) => array_filter([
                    '@type' => 'MenuItem',
                    'name' => $m->naam,
                    'description' => $m->beschrijving ?: (($m->ingredienten ?? []) ? implode(', ', $m->ingredienten) : null),
                    'offers' => ['@type' => 'Offer', 'price' => number_format($m->prijs / 100, 2, '.', ''), 'priceCurrency' => 'EUR'],
                ]))->values()->all(),
            ])->values()->all(),
        ];
    }

    public function home(string $slug)
    {
        $user = $this->pizzeria($slug);
        if (in_array(($user->onboarding ?? [])['theme'] ?? 'template1', ['template1', 'template2', 'template3', 'template4'], true)) {
            return $this->t1Home($user, '/bestellen/' . $slug);
        }
        $d = $this->gegevens($user, $slug);
        $d['toppers'] = $d['menu']->take(6);

        return view('bestel.home', $d);
    }

    public function menu(string $slug)
    {
        $user = $this->pizzeria($slug);
        if (in_array(($user->onboarding ?? [])['theme'] ?? 'template1', ['template1', 'template2', 'template3', 'template4'], true)) {
            return redirect('/bestellen/' . $slug);
        }
        $d = $this->gegevens($user, $slug);
        $d['menuJsonld'] = $this->menuJsonLd($d['menu']);
        $d['menuJs'] = $d['menu']->map(fn ($m) => [
            'id' => $m->id,
            'naam' => $m->naam,
            'icoon' => $m->icoon,
            'beschrijving' => $m->beschrijving,
            'prijs' => $m->prijs,
            'ingredienten' => $m->ingredienten ?? [],
            'allergenen' => $m->allergenen,
            'opties' => $m->opties ?? [],
            'foto' => self::fotoVoor($m),
        ])->values();

        return view('bestel.menu', $d);
    }

    public function contact(string $slug)
    {
        $user = $this->pizzeria($slug);
        if (in_array(($user->onboarding ?? [])['theme'] ?? 'template1', ['template1', 'template2', 'template3', 'template4'], true)) {
            return redirect('/bestellen/' . $slug);
        }

        return view('bestel.contact', $this->gegevens($user, $slug));
    }

    /** Bestelling plaatsen: prijzen worden altijd server-side opnieuw berekend */
    public function plaats(Request $request, string $slug)
    {
        $user = $this->pizzeria($slug);

        $data = $request->validate([
            'klant' => ['required', 'string', 'max:60'],
            'type' => ['required', 'in:bezorgen,afhalen'],
            'adres' => ['required_if:type,bezorgen', 'nullable', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:10'],
            'plaats' => ['required_if:type,bezorgen', 'nullable', 'string', 'max:60'],
            'opmerking' => ['nullable', 'string', 'max:300'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.id' => ['required', 'integer'],
            'items.*.aantal' => ['required', 'integer', 'min:1', 'max:10'],
            'items.*.keuzes' => ['sometimes', 'array', 'max:20'],
            'items.*.zonder' => ['sometimes', 'array', 'max:20'],
        ]);

        if (! $user->is_online) {
            return response()->json(['ok' => false, 'melding' => 'De zaak neemt op dit moment geen bestellingen aan.'], 422);
        }
        if ($data['type'] === 'bezorgen' && ($user->order_mode ?? 'bezorgen_afhalen') === 'alleen_afhalen') {
            return response()->json(['ok' => false, 'melding' => 'Er kan nu alleen afgehaald worden.'], 422);
        }

        $menuItems = $user->menuItems()->where('actief', true)->get()->keyBy('id');
        $orderItems = [];
        $totaal = 0;

        foreach ($data['items'] as $regel) {
            $item = $menuItems->get($regel['id']);
            if (! $item) {
                return response()->json(['ok' => false, 'melding' => 'Een gerecht in je mandje staat niet meer op de kaart.'], 422);
            }
            $opties = [];
            $regelPrijs = $item->prijs;
            foreach ($regel['keuzes'] ?? [] as $paar) {
                $keuze = $item->opties[$paar[0] ?? -1]['keuzes'][$paar[1] ?? -1] ?? null;
                if (! $keuze) {
                    return response()->json(['ok' => false, 'melding' => 'Een gekozen optie bestaat niet meer.'], 422);
                }
                $opties[] = ['naam' => $keuze['naam'], 'prijs' => (int) $keuze['prijs'], 'type' => 'extra'];
                $regelPrijs += (int) $keuze['prijs'];
            }
            $ingredienten = array_map('mb_strtolower', $item->ingredienten ?? []);
            foreach ($regel['zonder'] ?? [] as $zonder) {
                if (in_array(mb_strtolower((string) $zonder), $ingredienten, true)) {
                    $opties[] = ['naam' => 'Zonder ' . mb_strtolower((string) $zonder), 'prijs' => 0, 'type' => 'zonder'];
                }
            }
            $orderItems[] = ['naam' => $item->naam, 'prijs' => $item->prijs, 'aantal' => (int) $regel['aantal'], 'opties' => $opties];
            $totaal += $regelPrijs * (int) $regel['aantal'];
        }

        // Bezorgkosten op basis van afstand en de ingestelde stralen
        $bezorgkosten = 0;
        $volledigAdres = null;
        if ($data['type'] === 'bezorgen') {
            $volledigAdres = trim($data['adres'] . ', ' . trim(($data['postcode'] ?? '') . ' ' . ($data['plaats'] ?? '')));
            $tiers = collect($user->bezorgkosten ?? [])->sortBy('km')->values();
            if ($tiers->isNotEmpty() && $user->lat !== null && $user->lng !== null) {
                $afstand = $this->afstandNaar($user, $data['adres'], $data['postcode'] ?? '', $data['plaats'] ?? '');
                if ($afstand === null) {
                    $bezorgkosten = (int) $tiers->last()['kosten'];
                } else {
                    $tier = $tiers->first(fn ($t) => $afstand <= (float) $t['km']);
                    if (! $tier) {
                        return response()->json(['ok' => false, 'melding' => 'Helaas, dit adres ligt buiten ons bezorggebied (' . number_format($afstand, 1, ',', '') . ' km).'], 422);
                    }
                    $minimum = (int) ($tier['min'] ?? 0);
                    if ($minimum > 0 && $totaal < $minimum) {
                        return response()->json(['ok' => false, 'melding' => 'Voor bezorging op jouw adres geldt een minimaal bestelbedrag van € ' . number_format($minimum / 100, 2, ',', '.') . '.'], 422);
                    }
                    $bezorgkosten = (int) $tier['kosten'];
                }
            }
            if ($bezorgkosten > 0) {
                $orderItems[] = ['naam' => 'Bezorgkosten', 'prijs' => $bezorgkosten, 'aantal' => 1, 'opties' => []];
                $totaal += $bezorgkosten;
            }
        }

        $order = Order::create([
            'user_id' => $user->id,
            'nummer' => ((int) $user->orders()->max('nummer') ?: 411) + 1,
            'klant' => $data['klant'],
            'items' => $orderItems,
            'totaal' => $totaal,
            'status' => 'nieuw',
            'type' => $data['type'],
            'adres' => $volledigAdres,
            'opmerking' => $data['opmerking'] ?? null,
            'is_demo' => false,
        ]);

        return response()->json(['ok' => true, 'nummer' => $order->nummer, 'totaal' => $totaal, 'bezorgkosten' => $bezorgkosten]);
    }

    /* ── Template1 (Presto): de moderne bestelshop ─────────────────────────── */

    /** Alle kleurstijlen: per kleur een handgemaakt palet, plus een stijl-tab om op te filteren */
    public const KLEUREN = [
        // Warm
        ['hex' => '#E63946', 'naam' => 'Tomatenrood', 'stijl' => 'warm', 'palet' => ['primair' => '#E63946', 'primairDonker' => '#B02A35', 'secundair' => '#38151A', 'secundairLicht' => '#54262C', 'witWarm' => '#FFF6F5']],
        ['hex' => '#C65D3B', 'naam' => 'Terracotta', 'stijl' => 'warm', 'palet' => ['primair' => '#C65D3B', 'primairDonker' => '#A04528', 'secundair' => '#33190F', 'secundairLicht' => '#4E2A1B', 'witWarm' => '#FBF4EF']],
        ['hex' => '#F97316', 'naam' => 'Oranje', 'stijl' => 'warm', 'palet' => ['primair' => '#F97316', 'primairDonker' => '#C2410C', 'secundair' => '#3E2716', 'secundairLicht' => '#5B3A21', 'witWarm' => '#FFF7F0']],
        ['hex' => '#F5B301', 'naam' => 'Goudgeel', 'stijl' => 'warm', 'palet' => ['primair' => '#E8A400', 'primairDonker' => '#B57F00', 'secundair' => '#332708', 'secundairLicht' => '#4F3D12', 'witWarm' => '#FFFAEC']],
        ['hex' => '#E8604C', 'naam' => 'Koraal', 'stijl' => 'warm', 'palet' => ['primair' => '#E8604C', 'primairDonker' => '#C24634', 'secundair' => '#3A1712', 'secundairLicht' => '#57291F', 'witWarm' => '#FFF6F4']],
        ['hex' => '#38221A', 'naam' => 'Karamelbruin', 'stijl' => 'warm', 'palet' => ['primair' => '#9C5B2E', 'primairDonker' => '#7A4522', 'secundair' => '#2A1810', 'secundairLicht' => '#44301F', 'witWarm' => '#FAF5F0']],
        // Fris
        ['hex' => '#2F8F46', 'naam' => 'Basilicumgroen', 'stijl' => 'fris', 'palet' => ['primair' => '#2F8F46', 'primairDonker' => '#227235', 'secundair' => '#132B1B', 'secundairLicht' => '#25462F', 'witWarm' => '#F3FAF4']],
        ['hex' => '#12805C', 'naam' => 'Smaragd', 'stijl' => 'fris', 'palet' => ['primair' => '#12805C', 'primairDonker' => '#0D6247', 'secundair' => '#0F291F', 'secundairLicht' => '#1D4534', 'witWarm' => '#F2FAF6']],
        ['hex' => '#0F8A80', 'naam' => 'Zeegroen', 'stijl' => 'fris', 'palet' => ['primair' => '#0F8A80', 'primairDonker' => '#0B6B63', 'secundair' => '#0E2B28', 'secundairLicht' => '#1C4642', 'witWarm' => '#F1FAF9']],
        ['hex' => '#6B7A3A', 'naam' => 'Olijf', 'stijl' => 'fris', 'palet' => ['primair' => '#6B7A3A', 'primairDonker' => '#52602B', 'secundair' => '#22271A', 'secundairLicht' => '#3A4228', 'witWarm' => '#F9FAF2']],
        // Modern
        ['hex' => '#2563EB', 'naam' => 'Kobaltblauw', 'stijl' => 'modern', 'palet' => ['primair' => '#2563EB', 'primairDonker' => '#1A47B8', 'secundair' => '#0F1D3D', 'secundairLicht' => '#1D3263', 'witWarm' => '#F4F8FF']],
        ['hex' => '#4F46E5', 'naam' => 'Indigo', 'stijl' => 'modern', 'palet' => ['primair' => '#4F46E5', 'primairDonker' => '#3730A3', 'secundair' => '#191A3C', 'secundairLicht' => '#2B2C5E', 'witWarm' => '#F5F5FF']],
        ['hex' => '#7C3AED', 'naam' => 'Paars', 'stijl' => 'modern', 'palet' => ['primair' => '#7C3AED', 'primairDonker' => '#5B21B6', 'secundair' => '#221338', 'secundairLicht' => '#37215A', 'witWarm' => '#FAF7FF']],
        ['hex' => '#D6336C', 'naam' => 'Framboos', 'stijl' => 'modern', 'palet' => ['primair' => '#D6336C', 'primairDonker' => '#A82454', 'secundair' => '#380F20', 'secundairLicht' => '#551C33', 'witWarm' => '#FFF5F8']],
        ['hex' => '#33658A', 'naam' => 'Staalblauw', 'stijl' => 'modern', 'palet' => ['primair' => '#33658A', 'primairDonker' => '#274E6B', 'secundair' => '#14222E', 'secundairLicht' => '#24384A', 'witWarm' => '#F3F7FA']],
        ['hex' => '#1F2937', 'naam' => 'Antraciet', 'stijl' => 'modern', 'palet' => ['primair' => '#1F2937', 'primairDonker' => '#111827', 'secundair' => '#111827', 'secundairLicht' => '#2A3441', 'witWarm' => '#F7F8F9']],
        // Klassiek
        ['hex' => '#7D1D3F', 'naam' => 'Bordeaux', 'stijl' => 'klassiek', 'palet' => ['primair' => '#7D1D3F', 'primairDonker' => '#5E1630', 'secundair' => '#260A14', 'secundairLicht' => '#421426', 'witWarm' => '#FBF4F6']],
        ['hex' => '#1F5130', 'naam' => 'Flessengroen', 'stijl' => 'klassiek', 'palet' => ['primair' => '#1F5130', 'primairDonker' => '#173D24', 'secundair' => '#0F2015', 'secundairLicht' => '#1E3626', 'witWarm' => '#F4F8F5']],
        ['hex' => '#1E3A8A', 'naam' => 'Marineblauw', 'stijl' => 'klassiek', 'palet' => ['primair' => '#1E3A8A', 'primairDonker' => '#172C69', 'secundair' => '#101A34', 'secundairLicht' => '#1E2C52', 'witWarm' => '#F4F6FB']],
        ['hex' => '#6F4425', 'naam' => 'Espresso', 'stijl' => 'klassiek', 'palet' => ['primair' => '#6F4425', 'primairDonker' => '#55341C', 'secundair' => '#241509', 'secundairLicht' => '#3C2814', 'witWarm' => '#F9F5F1']],
    ];

    /** Van één hoofdkleur naar het volledige palet; onbekende kleuren worden afgeleid */
    public static function paletVoor(?string $hex): array
    {
        $hex = ($hex && preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) ? $hex : '#F97316';
        foreach (self::KLEUREN as $kleur) {
            if (strcasecmp($kleur['hex'], $hex) === 0) {
                return $kleur['palet'];
            }
        }
        $r = hexdec(substr($hex, 1, 2)) / 255;
        $g = hexdec(substr($hex, 3, 2)) / 255;
        $b = hexdec(substr($hex, 5, 2)) / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0.0;
        if ($max !== $min) {
            $d = $max - $min;
            if ($max === $r) {
                $h = fmod(($g - $b) / $d + ($g < $b ? 6 : 0), 6);
            } elseif ($max === $g) {
                $h = ($b - $r) / $d + 2;
            } else {
                $h = ($r - $g) / $d + 4;
            }
            $h *= 60;
        }
        $maak = function (float $s, float $l) use ($h): string {
            $s /= 100;
            $l /= 100;
            $f = function (float $n) use ($h, $s, $l): float {
                $k = fmod($n + $h / 30, 12);
                $a = $s * min($l, 1 - $l);

                return $l - $a * max(-1, min($k - 3, min(9 - $k, 1)));
            };

            return sprintf('#%02x%02x%02x', (int) round($f(0) * 255), (int) round($f(8) * 255), (int) round($f(4) * 255));
        };

        return [
            'primair' => $hex,
            'primairDonker' => $maak(88, 32),
            'secundair' => $maak(35, 17),
            'secundairLicht' => $maak(45, 24),
            'witWarm' => $maak(100, 97),
        ];
    }

    /** Welk Presto-familielid er getoond wordt: template1 (Presto) of template2 (Notte) */
    private function t1Thema(User $user, ?string $forceer = null): string
    {
        // ?thema= toont een ander template als voorbeeld zonder iets op te slaan (stijl-popup)
        $thema = $forceer ?? request('thema') ?? (($user->onboarding ?? [])['theme'] ?? 'template1');

        return in_array($thema, ['template1', 'template2', 'template3', 'template4'], true) ? $thema : 'template1';
    }

    /** Gedeelde basis voor alle template1-pagina's */
    private function t1Basis(User $user, string $basis): array
    {
        $ob = $user->onboarding ?? [];

        // ?kleur= toont een andere kleurstijl als voorbeeld zonder iets op te slaan (stijl-popup)
        $kleur = request('kleur');
        if (! is_string($kleur) || ! preg_match('/^#[0-9A-Fa-f]{6}$/', $kleur)) {
            $kleur = null;
        }

        return [
            'basis' => $basis,
            'slug' => $user->slug ?: 'demo',
            'logo' => $ob['logo'] ?? null,
            'naam' => $ob['name'] ?? 'Pizzeria',
            'palet' => self::paletVoor($kleur ?? $ob['color'] ?? null),
            'spaarpunten' => (bool) ($ob['spaarpunten'] ?? false),
        ];
    }

    /** Openingstijd van een dag, rekening houdend met tijden per dag */
    private function t1Tijden(array $ob, string $dagKey): array
    {
        return (($ob['hoursMode'] ?? 'same') === 'perday' && isset($ob['dayTimes'][$dagKey]))
            ? $ob['dayTimes'][$dagKey]
            : ['open' => $ob['open'] ?? '16:00', 'close' => $ob['close'] ?? '21:30'];
    }

    /** Open of gesloten, plus voorbestel-sloten zolang de zaak dicht is */
    private function t1OpenInfo(User $user): array
    {
        $ob = $user->onboarding ?? [];
        $nu = now();
        $openNu = false;
        $slots = [];
        for ($i = 0; $i < 7 && $slots === [] && ! $openNu; $i++) {
            $dag = $nu->copy()->addDays($i);
            $key = self::DAGEN[$dag->dayOfWeek];
            if (! (($ob['days'] ?? [])[$key] ?? false)) {
                continue;
            }
            $tijd = $this->t1Tijden($ob, $key);
            $van = $dag->copy()->setTimeFromTimeString($tijd['open']);
            $tot = $dag->copy()->setTimeFromTimeString($tijd['close']);
            if ($i === 0 && $nu->between($van, $tot)) {
                $openNu = true;
                break;
            }
            if ($i === 0 && $nu->gt($tot)) {
                continue;
            }
            $label = $i === 0 ? 'Vandaag' : ($i === 1 ? 'Morgen' : self::DAGNAMEN[$key]);
            for ($slot = $van->copy(); $slot->lte($tot) && count($slots) < 24; $slot->addMinutes(30)) {
                $slots[] = $label . ' ' . $slot->format('H:i');
            }
        }

        // Online bepaalt of er besteld kan worden; de tijden bepalen alleen de voorbestel-sloten.
        // Offline binnen openingstijden is een pauze: dan ook geen voorbestellingen.
        return [
            'open' => (bool) $user->is_online,
            'pauze' => ! $user->is_online && $openNu,
            'slots' => $user->is_online || $openNu ? [] : $slots,
        ];
    }

    /** Sloten voor de gewenste bezorgtijd op de afrekenpagina */
    private function t1AfrekenSlots(User $user): array
    {
        $ob = $user->onboarding ?? [];
        $nu = now();
        $direct = false;
        $slots = [];
        for ($i = 0; $i < 7 && count($slots) < 24; $i++) {
            $dag = $nu->copy()->addDays($i);
            $key = self::DAGEN[$dag->dayOfWeek];
            if (! (($ob['days'] ?? [])[$key] ?? false)) {
                continue;
            }
            $tijd = $this->t1Tijden($ob, $key);
            $van = $dag->copy()->setTimeFromTimeString($tijd['open']);
            $tot = $dag->copy()->setTimeFromTimeString($tijd['close']);
            $begin = $van->copy();
            if ($i === 0) {
                if ($nu->between($van, $tot)) {
                    $direct = true;
                }
                $vroegste = $nu->copy()->addMinutes(30);
                $vroegste->second(0);
                $minuut = $vroegste->minute;
                $vroegste->minute(0);
                if ($minuut > 30) {
                    $vroegste->addHour();
                } elseif ($minuut > 0) {
                    $vroegste->minute(30);
                }
                $begin = $vroegste->gt($van) ? $vroegste : $van;
                if ($begin->gt($tot)) {
                    continue;
                }
            }
            $label = $i === 0 ? 'Vandaag' : ($i === 1 ? 'Morgen' : self::DAGNAMEN[$key]);
            for ($slot = $begin->copy(); $slot->lte($tot) && count($slots) < 24; $slot->addMinutes(30)) {
                $slots[] = $label . ' ' . $slot->format('H:i');
            }
        }

        // "Zo snel mogelijk" kan alleen als de zaak online staat
        return ['direct' => (bool) $user->is_online, 'slots' => $slots];
    }

    /** De shop zelf: menu, tarieven en openingsstatus */
    public function t1Home(User $user, string $basis, ?string $forceer = null)
    {
        $thema = $this->t1Thema($user, $forceer);
        $items = $user->menuItems()->where('actief', true)->orderBy('volgorde')->orderBy('id')->get();
        $tiers = collect($user->bezorgkosten ?? [])->sortBy('km')->values();

        return view("templates.$thema.$thema", [
            ...$this->t1Basis($user, $basis),
            'categorieen' => $items->pluck('categorie')->unique()->values(),
            'menu' => $items->groupBy('categorie')->map(fn ($groep) => $groep->map(fn ($item) => [
                'id' => $item->id,
                'naam' => $item->naam,
                'beschrijving' => $item->beschrijving,
                'prijs' => number_format($item->prijs / 100, 2, ',', '.'),
                'foto' => $item->foto ?: null,
                'allergenen' => $item->allergenen ?? [],
            ])),
            'menuJs' => $items->mapWithKeys(fn ($item) => [$item->id => [
                'naam' => $item->naam,
                'categorie' => $item->categorie,
                'beschrijving' => $item->beschrijving,
                'prijsCenten' => $item->prijs,
                'foto' => $item->foto ?: null,
                'allergenen' => $item->allergenen ?? [],
                'opties' => $item->opties ?? [],
            ]]),
            'bezorg' => ['lat' => $user->lat, 'lng' => $user->lng, 'tiers' => $tiers],
            'laagsteTier' => $tiers->first(),
            'openInfo' => $this->t1OpenInfo($user),
            'overInfo' => $this->t1OverInfo($user),
            'klantInfo' => $this->t1KlantInfo($user),
            'upsellPin' => ($user->onboarding ?? [])['upsellPin'] ?? null,
        ]);
    }

    /** Gegevens voor de Over-popup: adres, openingstijden per dag */
    private function t1OverInfo(User $user): array
    {
        $ob = $user->onboarding ?? [];

        return [
            'straat' => $ob['street'] ?? '',
            'postcode' => $ob['zip'] ?? '',
            'plaats' => $ob['city'] ?? '',
            'dagen' => collect(self::DAGNAMEN)->map(function ($naam, $key) use ($ob) {
                $tijd = $this->t1Tijden($ob, $key);

                return [
                    'naam' => $naam,
                    'open' => (bool) (($ob['days'] ?? [])[$key] ?? false),
                    'van' => $tijd['open'],
                    'tot' => $tijd['close'],
                ];
            })->values(),
        ];
    }

    /** Demo-mandje voor het live voorbeeld in het dashboard (?voorbeeld=1) */
    private function t1VoorbeeldMand(User $user)
    {
        return $user->menuItems()->where('actief', true)->take(3)->get()->values()
            ->map(fn ($item, $i) => [
                'sleutel' => 'voorbeeld-' . $item->id,
                'naam' => $item->naam,
                'prijsCenten' => (int) $item->prijs,
                'aantal' => $i === 0 ? 2 : 1,
                'opties' => [],
                'opmerking' => '',
            ]);
    }

    /** Gerechtfoto's op naam, voor de thumbnails in de bestelsamenvatting */
    private function t1Fotos(User $user)
    {
        return $user->menuItems()->where('actief', true)->get()
            ->mapWithKeys(fn ($item) => [$item->naam => $item->foto ?: null])
            ->filter();
    }

    public function t1Afrekenen(User $user, string $basis, ?string $forceer = null)
    {
        // Zonder account verder? Dat onthouden we voor deze sessie.
        if (request('gast')) {
            session(['gast_' . $user->id => true]);
        }

        // Tussenstap: eerst inloggen of bewust als gast verder (het voorbeeld in het dashboard slaat dit over)
        $klant = $this->t1KlantVan($user);
        if (! $klant && ! session('gast_' . $user->id) && ! request('voorbeeld')) {
            return redirect($basis . '/inloggen');
        }

        $thema = $this->t1Thema($user, $forceer);
        $tijden = $this->t1AfrekenSlots($user);

        return view("templates.$thema.$thema-afrekenen", [
            ...$this->t1Basis($user, $basis),
            'direct' => $tijden['direct'],
            'slots' => $tijden['slots'],
            'bezorg' => ['lat' => $user->lat, 'lng' => $user->lng, 'tiers' => collect($user->bezorgkosten ?? [])->sortBy('km')->values()],
            'fotos' => $this->t1Fotos($user),
            'voorbeeldMand' => request('voorbeeld') ? $this->t1VoorbeeldMand($user) : null,
            'klant' => $klant ? [...$klant->only(['naam', 'email', 'telefoon']), 'adressen' => $klant->adressen ?? [], 'punten' => (int) $klant->punten] : null,
        ]);
    }

    /* ── Klantaccounts: de tussenstap tussen de winkelmand en het afrekenen ── */

    /** De ingelogde klant van deze zaak, of null */
    private function t1KlantVan(User $user): ?Klant
    {
        $id = session('klant_' . $user->id);

        return $id ? Klant::where('user_id', $user->id)->find($id) : null;
    }

    /* Spaarpunten: 1 punt per volle euro aan gerechten; per blok van 100 punten 5 euro korting */
    public const PUNTEN_BLOK = 100;
    public const BLOK_KORTING = 500;

    /** Waar de klant na het inloggen heen wil: afrekenen (standaard) of de account-popup in de shop */
    private function t1InlogDoel(string $basis): string
    {
        return request('verder') === 'account' ? $basis . '?account=1' : $basis . '/afrekenen';
    }

    /** Alles wat de account-popup en accountpagina tonen, of null zonder login */
    private function t1KlantInfo(User $user, int $maxBestellingen = 5): ?array
    {
        $klant = $this->t1KlantVan($user);
        if (! $klant) {
            return null;
        }

        $statusLabels = [
            'nieuw' => 'Geplaatst', 'geaccepteerd' => 'Bevestigd', 'bereiden' => 'Wordt bereid',
            'oven' => 'Wordt bereid', 'onderweg' => 'Onderweg', 'bezorgd' => 'Bezorgd',
        ];

        return [
            'naam' => $klant->naam,
            'email' => $klant->email,
            'telefoon' => $klant->telefoon,
            'punten' => $klant->punten,
            'adressen' => $klant->adressen ?? [],
            'bestellingen' => Order::where('klant_id', $klant->id)->latest()->take($maxBestellingen)->get()
                ->map(fn ($order) => [
                    'nummer' => $order->nummer,
                    'token' => $order->token,
                    'datum' => $order->created_at->format('d-m-Y H:i'),
                    'totaal' => $order->totaal,
                    'status' => $statusLabels[$order->status] ?? $order->status,
                    'afgerond' => $order->status === 'bezorgd',
                    'items' => collect($order->items)
                        ->filter(fn ($i) => ! in_array($i['naam'] ?? '', ['Bezorgkosten', 'Fooi bezorger'], true))
                        ->map(fn ($i) => ($i['aantal'] ?? 1) . 'x ' . ($i['naam'] ?? ''))->implode(', '),
                ])->all(),
        ];
    }

    public function t1Inloggen(User $user, string $basis, ?string $forceer = null)
    {
        // Al ingelogd: meteen door naar waar je heen wilde
        if ($this->t1KlantVan($user)) {
            return redirect($this->t1InlogDoel($basis));
        }
        $thema = $this->t1Thema($user, $forceer);

        return view("templates.$thema.$thema-inloggen", $this->t1Basis($user, $basis));
    }

    public function t1Account(User $user, string $basis, ?string $forceer = null)
    {
        $info = $this->t1KlantInfo($user, 20);
        if (! $info) {
            return redirect($basis . '/inloggen?verder=account');
        }

        $thema = $this->t1Thema($user, $forceer);

        return view("templates.$thema.$thema-account", [
            ...$this->t1Basis($user, $basis),
            'klant' => $this->t1KlantVan($user),
            'bestellingen' => collect($info['bestellingen']),
        ]);
    }

    public function t1KlantLogin(User $user, string $basis, Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'wachtwoord' => ['required', 'string'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet goed uit.',
            'wachtwoord.required' => 'Vul je wachtwoord in.',
        ]);

        $klant = Klant::where('user_id', $user->id)
            ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])->first();
        if (! $klant || ! Hash::check($data['wachtwoord'], $klant->wachtwoord)) {
            return back()->withErrors(['email' => 'E-mailadres of wachtwoord klopt niet.'], 'login')->withInput();
        }

        session(['klant_' . $user->id => $klant->id]);

        return redirect($this->t1InlogDoel($basis));
    }

    public function t1KlantRegistreer(User $user, string $basis, Request $request)
    {
        $data = $request->validate([
            'naam' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'wachtwoord' => ['required', 'string', 'min:8'],
        ], [
            'naam.required' => 'Vul je naam in.',
            'naam.min' => 'Vul je naam in.',
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet goed uit.',
            'wachtwoord.required' => 'Kies een wachtwoord.',
            'wachtwoord.min' => 'Je wachtwoord moet minimaal 8 tekens zijn.',
        ]);

        $bestaat = Klant::where('user_id', $user->id)
            ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])->exists();
        if ($bestaat) {
            return back()->withErrors(['email' => 'Er is al een account met dit e-mailadres. Log hiernaast in.'], 'registratie')->withInput();
        }

        $klant = Klant::create([
            'user_id' => $user->id,
            'naam' => $data['naam'],
            'email' => strtolower($data['email']),
            'telefoon' => $data['telefoon'] ?? null,
            'wachtwoord' => Hash::make($data['wachtwoord']),
        ]);
        session(['klant_' . $user->id => $klant->id]);

        return redirect($this->t1InlogDoel($basis));
    }

    public function t1KlantUitloggen(User $user, string $basis)
    {
        session()->forget(['klant_' . $user->id, 'gast_' . $user->id]);

        return redirect($basis);
    }

    /** Adresboek van de ingelogde klant bewaren (bij elke wijziging in de afreken-popup) */
    public function t1KlantAdressen(User $user, Request $request)
    {
        $klant = $this->t1KlantVan($user);
        if (! $klant) {
            return response()->json(['ok' => false], 401);
        }

        $data = $request->validate([
            'adressen' => ['required', 'array', 'max:10'],
            'adressen.*.label' => ['required', 'string', 'max:160'],
            'adressen.*.lat' => ['nullable', 'numeric'],
            'adressen.*.lng' => ['nullable', 'numeric'],
        ]);

        $klant->update(['adressen' => array_values($data['adressen'])]);

        return response()->json(['ok' => true]);
    }

    public function t1Bestelling(User $user, string $basis, ?string $token = null, ?string $forceer = null)
    {
        $thema = $this->t1Thema($user, $forceer);
        $ob = $user->onboarding ?? [];

        // Demo-bestelling voor het live voorbeeld in het dashboard (?voorbeeld=1)
        $voorbeeld = null;
        if (request('voorbeeld')) {
            $items = $this->t1VoorbeeldMand($user);
            $subtotaal = $items->sum(fn ($r) => $r['prijsCenten'] * $r['aantal']);
            $ob = $user->onboarding ?? [];
            $voorbeeld = [
                'token' => null,
                'nummer' => 241,
                'items' => $items,
                'type' => 'bezorgen',
                'klant' => ['naam' => 'Anna'],
                'adres' => 'Voorbeeldstraat 12, ' . ($ob['city'] ?? 'Arnhem'),
                'lat' => $user->lat !== null ? (float) $user->lat + 0.012 : null,
                'lng' => $user->lng !== null ? (float) $user->lng + 0.006 : null,
                'subtotaal' => $subtotaal,
                'kosten' => 250,
                'fooi' => 0,
                'totaal' => $subtotaal + 250,
                'tijd' => 'Zo snel mogelijk',
                'geplaatst' => now()->getTimestampMs(),
                'opmerking' => '',
            ];
        }

        return view("templates.$thema.$thema-bestelling", [
            ...$this->t1Basis($user, $basis),
            'token' => $token,
            'fotos' => $this->t1Fotos($user),
            'voorbeeldBestelling' => $voorbeeld,
            'bezorg' => ['lat' => $user->lat, 'lng' => $user->lng],
            'adresZaak' => trim(($ob['street'] ?? '') !== '' ? ($ob['street'] . ', ' . trim(($ob['zip'] ?? '') . ' ' . ($ob['city'] ?? ''))) : ''),
            'telefoonZaak' => $ob['phone'] ?? null,
            'mailZaak' => $ob['email'] ?? $user->email,
        ]);
    }

    /** De bestelling terughalen op token, zodat de statuspagina ook zonder lokale opslag werkt */
    public function t1OrderData(User $user, Request $request)
    {
        $order = $user->orders()->where('token', (string) $request->query('token'))->first();
        abort_unless($order, 404);

        $kosten = 0;
        $fooi = 0;
        $korting = 0;
        $items = [];
        foreach ($order->items as $item) {
            if ($item['naam'] === 'Bezorgkosten') {
                $kosten = (int) $item['prijs'];
                continue;
            }
            if ($item['naam'] === 'Fooi bezorger') {
                $fooi = (int) $item['prijs'];
                continue;
            }
            if ($item['naam'] === 'Spaarpunten korting') {
                $korting = -(int) $item['prijs'];
                continue;
            }
            $items[] = [
                'naam' => $item['naam'],
                'prijsCenten' => (int) $item['prijs'],
                'aantal' => (int) $item['aantal'],
                'opties' => array_map(fn ($o) => ['naam' => $o['naam'], 'prijs' => (int) $o['prijs']], $item['opties'] ?? []),
            ];
        }

        return response()->json([
            'nummer' => $order->nummer,
            'token' => $order->token,
            'geplaatst' => $order->created_at->valueOf(),
            'type' => $order->type,
            'adres' => $order->adres,
            'lat' => null,
            'lng' => null,
            'tijd' => '',
            'klant' => ['naam' => $order->klant],
            'opmerking' => '',
            'items' => $items,
            'subtotaal' => $order->totaal - $kosten - $fooi + $korting,
            'kosten' => $kosten,
            'fooi' => $fooi,
            'korting' => $korting,
            'punten' => (int) $order->punten,
            'totaal' => $order->totaal,
        ]);
    }

    public function t1Status(User $user, Request $request)
    {
        $order = $request->query('token')
            ? $user->orders()->where('token', (string) $request->query('token'))->first()
            : $user->orders()->where('nummer', (int) $request->query('nummer'))->orderByDesc('id')->first();
        abort_unless($order, 404);

        return response()->json([
            'status' => $order->status,
            'eta' => $order->eta_minuten,
            'rond' => $order->eta_minuten ? $order->created_at->clone()->addMinutes((int) $order->eta_minuten)->format('H:i') : null,
            'bezorgdOm' => $order->status === 'bezorgd' ? $order->updated_at->format('H:i') : null,
        ]);
    }

    /** Bestelling plaatsen vanuit template1: prijzen altijd server-side opnieuw opgebouwd */
    public function t1Plaats(User $user, Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:bezorgen,afhalen'],
            'klant' => ['required', 'string', 'max:80'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'punten' => ['sometimes', 'boolean'],
            'adres' => ['nullable', 'string', 'max:160'],
            'tijd' => ['nullable', 'string', 'max:40'],
            'opmerking' => ['nullable', 'string', 'max:300'],
            'bezorgkosten' => ['required', 'integer', 'min:0', 'max:2500'],
            'fooi' => ['required', 'integer', 'min:0', 'max:10000'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.id' => ['required', 'integer'],
            'items.*.aantal' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.opties' => ['sometimes', 'array', 'max:15'],
            'items.*.opties.*.naam' => ['required', 'string', 'max:60'],
            'items.*.opties.*.prijs' => ['required', 'integer', 'min:0', 'max:9900'],
            'items.*.opmerking' => ['sometimes', 'nullable', 'string', 'max:140'],
        ]);

        $open = $this->t1OpenInfo($user);
        if (! $open['open'] && ($open['pauze'] || ($data['tijd'] ?? '') === '')) {
            return response()->json(['ok' => false, 'melding' => 'De zaak neemt op dit moment geen bestellingen aan.'], 422);
        }

        $menuItems = $user->menuItems()->where('actief', true)->get()->keyBy('id');
        $orderItems = [];
        $totaal = 0;
        $regelOpmerkingen = [];
        foreach ($data['items'] as $regel) {
            $item = $menuItems->get($regel['id']);
            if (! $item) {
                return response()->json(['ok' => false, 'melding' => 'Een gerecht in je mandje staat niet meer op de kaart.'], 422);
            }
            $keuzes = collect($item->opties ?? [])->flatMap(fn ($groep) => $groep['keuzes'])->keyBy('naam');
            $opties = [];
            $regelPrijs = $item->prijs;
            foreach ($regel['opties'] ?? [] as $optie) {
                $keuze = $keuzes->get($optie['naam']);
                if (! $keuze) {
                    return response()->json(['ok' => false, 'melding' => 'Een gekozen optie bestaat niet meer.'], 422);
                }
                $opties[] = ['naam' => $keuze['naam'], 'prijs' => (int) $keuze['prijs'], 'type' => 'extra'];
                $regelPrijs += (int) $keuze['prijs'];
            }
            $orderItems[] = ['naam' => $item->naam, 'prijs' => $item->prijs, 'aantal' => (int) $regel['aantal'], 'opties' => $opties];
            $totaal += $regelPrijs * (int) $regel['aantal'];
            if (! empty($regel['opmerking'])) {
                $regelOpmerkingen[] = $item->naam . ': ' . $regel['opmerking'];
            }
        }
        // Spaarpunten: inwisselen als korting en sparen over het (gekorte) gerechtenbedrag.
        // 1 punt per volle euro; per 100 punten 5 euro korting.
        $subtotaalGerechten = $totaal;
        $klantAccount = $this->t1KlantVan($user);
        $spaarpuntenAan = (bool) (($user->onboarding ?? [])['spaarpunten'] ?? false);
        $puntenGebruikt = 0;
        $korting = 0;
        if ($klantAccount && $spaarpuntenAan && $request->boolean('punten')) {
            $blokken = min(intdiv($klantAccount->punten, self::PUNTEN_BLOK), intdiv($subtotaalGerechten, self::BLOK_KORTING));
            if ($blokken > 0) {
                $puntenGebruikt = $blokken * self::PUNTEN_BLOK;
                $korting = $blokken * self::BLOK_KORTING;
                $orderItems[] = ['naam' => 'Spaarpunten korting', 'prijs' => -$korting, 'aantal' => 1, 'opties' => []];
                $totaal -= $korting;
            }
        }
        $puntenVerdiend = $klantAccount && $spaarpuntenAan ? intdiv(max(0, $subtotaalGerechten - $korting), 100) : 0;

        if ($data['type'] === 'bezorgen' && $data['bezorgkosten'] > 0) {
            $orderItems[] = ['naam' => 'Bezorgkosten', 'prijs' => (int) $data['bezorgkosten'], 'aantal' => 1, 'opties' => []];
            $totaal += (int) $data['bezorgkosten'];
        }
        if ($data['fooi'] > 0) {
            $orderItems[] = ['naam' => 'Fooi bezorger', 'prijs' => (int) $data['fooi'], 'aantal' => 1, 'opties' => []];
            $totaal += (int) $data['fooi'];
        }

        $opmerking = collect([
            ($data['tijd'] ?? '') !== '' && $data['tijd'] !== 'Zo snel mogelijk' ? 'Gewenste tijd: ' . $data['tijd'] : null,
            $data['opmerking'] ?? null,
            ...$regelOpmerkingen,
        ])->filter()->implode(' | ');

        // Ingelogde klant: gegevens onthouden en het puntensaldo bijwerken
        if ($klantAccount) {
            $bijwerken = [];
            if ($data['type'] === 'bezorgen' && ! empty($data['adres'])) {
                $bijwerken['adres'] = ['adres' => $data['adres']];
            }
            if (! empty($data['telefoon'])) {
                $bijwerken['telefoon'] = $data['telefoon'];
            }
            if ($puntenVerdiend > 0 || $puntenGebruikt > 0) {
                $bijwerken['punten'] = max(0, $klantAccount->punten - $puntenGebruikt + $puntenVerdiend);
            }
            if ($bijwerken !== []) {
                $klantAccount->update($bijwerken);
            }
        }

        $order = Order::create([
            'user_id' => $user->id,
            'klant_id' => $klantAccount?->id,
            'nummer' => ((int) $user->orders()->max('nummer') ?: 411) + 1,
            'token' => (string) Str::uuid(),
            'klant' => $data['klant'],
            'items' => $orderItems,
            'totaal' => $totaal,
            'punten' => $puntenVerdiend,
            'punten_gebruikt' => $puntenGebruikt,
            'status' => 'nieuw',
            'type' => $data['type'],
            'adres' => $data['type'] === 'bezorgen' ? ($data['adres'] ?? null) : null,
            'opmerking' => $opmerking !== '' ? $opmerking : null,
            'is_demo' => false,
        ]);

        return response()->json([
            'ok' => true, 'nummer' => $order->nummer, 'token' => $order->token, 'totaal' => $totaal,
            'punten' => $puntenVerdiend, 'korting' => $korting,
        ]);
    }

    /* Slug-varianten voor de publieke routes */
    public function afrekenen1(string $slug)
    {
        return $this->t1Afrekenen($this->pizzeria($slug), '/bestellen/' . $slug);
    }

    public function inloggen1(string $slug)
    {
        return $this->t1Inloggen($this->pizzeria($slug), '/bestellen/' . $slug);
    }

    public function account1(string $slug)
    {
        return $this->t1Account($this->pizzeria($slug), '/bestellen/' . $slug);
    }

    public function klantlogin1(Request $request, string $slug)
    {
        return $this->t1KlantLogin($this->pizzeria($slug), '/bestellen/' . $slug, $request);
    }

    public function klantregistreer1(Request $request, string $slug)
    {
        return $this->t1KlantRegistreer($this->pizzeria($slug), '/bestellen/' . $slug, $request);
    }

    public function klantuitloggen1(string $slug)
    {
        return $this->t1KlantUitloggen($this->pizzeria($slug), '/bestellen/' . $slug);
    }

    public function klantadressen1(Request $request, string $slug)
    {
        return $this->t1KlantAdressen($this->pizzeria($slug), $request);
    }

    public function bestelling1(string $slug, ?string $token = null)
    {
        return $this->t1Bestelling($this->pizzeria($slug), '/bestellen/' . $slug, $token);
    }

    public function orderdata1(Request $request, string $slug)
    {
        return $this->t1OrderData($this->pizzeria($slug), $request);
    }

    public function status1(Request $request, string $slug)
    {
        return $this->t1Status($this->pizzeria($slug), $request);
    }

    public function plaats1(Request $request, string $slug)
    {
        return $this->t1Plaats($this->pizzeria($slug), $request);
    }

    /** Afstand hemelsbreed in km tussen de zaak en het klantadres, of null als het adres niet gevonden wordt */
    private function afstandNaar(User $user, string $adres, string $postcode, string $plaats): ?float
    {
        try {
            $vraag = ['street' => $adres, 'city' => $plaats, 'country' => 'Nederland', 'format' => 'json', 'limit' => 1];
            if (trim($postcode) !== '') {
                $vraag['postalcode'] = trim($postcode);
            }
            $antwoord = Http::withHeaders(['User-Agent' => 'PizzaPlatform (ontwikkeling)'])
                ->timeout(4)
                ->get('https://nominatim.openstreetmap.org/search', $vraag);
            $hit = $antwoord->ok() ? ($antwoord->json()[0] ?? null) : null;
            if (! $hit) {
                return null;
            }
            $lat1 = deg2rad((float) $user->lat);
            $lat2 = deg2rad((float) $hit['lat']);
            $dLat = $lat2 - $lat1;
            $dLng = deg2rad((float) $hit['lon'] - (float) $user->lng);
            $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLng / 2) ** 2;

            return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
        } catch (\Throwable) {
            return null;
        }
    }
}
