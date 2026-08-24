<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BestelController extends Controller
{
    /** Kleurpaletten per template, gelijk aan de previews in de onboarding */
    public const THEMAS = [
        'fresco' => ['kopFont' => '"Lilita One", cursive', 'font' => 'family=Lilita+One', 'upper' => false, 'pagina' => '#FFF6E8', 'kaart' => '#FFFFFF', 'tekst' => '#38221A', 'prijs' => 'rgba(56,34,26,.55)', 'radius' => '.7rem', 'knopRadius' => '9999px', 'rand' => 'none', 'accentKop' => true],
        'nero' => ['kopFont' => '"Playfair Display", Georgia, serif', 'font' => 'family=Playfair+Display:wght@500;600;700', 'upper' => false, 'pagina' => '#171310', 'kaart' => '#1C1512', 'tekst' => '#F3EAD9', 'prijs' => '#C9A96A', 'radius' => '.45rem', 'knopRadius' => '.45rem', 'rand' => 'none', 'accentKop' => false],
        'napoli' => ['kopFont' => '"Libre Baskerville", "Times New Roman", serif', 'font' => 'family=Libre+Baskerville:wght@400;700', 'upper' => false, 'pagina' => '#FFF8E7', 'kaart' => '#FFFDF6', 'tekst' => '#3A2A1A', 'prijs' => '#8A6A3B', 'radius' => '.35rem', 'knopRadius' => '.35rem', 'rand' => '1px solid #E8DCC2', 'accentKop' => true],
        'puro' => ['kopFont' => '"Nunito", sans-serif', 'font' => '', 'upper' => false, 'pagina' => '#F4F7F4', 'kaart' => '#FFFFFF', 'tekst' => '#233029', 'prijs' => '#5B6B60', 'radius' => '.7rem', 'knopRadius' => '.7rem', 'rand' => 'none', 'accentKop' => true],
        'blocco' => ['kopFont' => '"Archivo Black", "Arial Black", sans-serif', 'font' => 'family=Archivo+Black', 'upper' => true, 'pagina' => '#FFFFFF', 'kaart' => '#FFFFFF', 'tekst' => '#111111', 'prijs' => '#111111', 'radius' => '0', 'knopRadius' => '0', 'rand' => '2px solid #111111', 'accentKop' => true],
        'retro' => ['kopFont' => '"Alfa Slab One", "Cooper Black", serif', 'font' => 'family=Alfa+Slab+One', 'upper' => false, 'pagina' => '#F5E0B4', 'kaart' => '#FBEED3', 'tekst' => '#5B3A21', 'prijs' => '#8A5A2B', 'radius' => '1.1rem', 'knopRadius' => '2rem', 'rand' => 'none', 'accentKop' => true],
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
        $d = $this->gegevens($this->pizzeria($slug), $slug);
        $d['toppers'] = $d['menu']->take(6);

        return view('bestel.home', $d);
    }

    public function menu(string $slug)
    {
        $d = $this->gegevens($this->pizzeria($slug), $slug);
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
        return view('bestel.contact', $this->gegevens($this->pizzeria($slug), $slug));
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
