<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\Klant;
use App\Models\Medewerker;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Twaalf pizzeria's in alle levensfases van het platform, zodat elk paneel
 * van het beheer gevuld is: overzicht, financieel (grafieken en conversie),
 * sales (aflopende proeven, afgehaakt, stille abonnees) en feedback.
 *
 * Inloggen na het seeden:
 * - HET DEMO-ACCOUNT (dashboard, rijkst gevuld): demo-pizzeria@mijnpizzeria.nl / Demo1234!
 * - Demo-beheer: demo-beheer@mijnpizzeria.nl / Demo1234! (staat in DatabaseSeeder)
 * - Showcase (bestelpagina /bestellen/pizzeriasole): demo@pizzeria.nl / Demo1234!
 * - Alle andere zaken: <slug>@demo.mijnpizzeria.nl / Demo1234!
 * - Teamscherm (/bezorger), bezorgers van het demo-account: youssef@fiorella.demo en lisa@fiorella.demo / Demo1234!
 */
class PizzeriaSeeder extends Seeder
{
    private string $wachtwoord;

    public function run(): void
    {
        $this->wachtwoord = Hash::make('Demo1234!');

        // [naam, persoon, plaats, straat, thema, kleur, staat, wekenGeleden, online]
        // staat: actief | actief-stil | proef-N (N dagen over) | verlopen-N (N dagen geleden)
        $zaken = [
            ['Pizzeria Sole', 'Mario Rossi', 'Arnhem', 'Stationsplein 12', 'template1', '#E63946', 'actief', 8, true],
            ['Bella Roma', 'Giulia Romano', 'Nijmegen', 'Grote Markt 3', 'template4', '#F5B301', 'actief', 7, true],
            ['La Luna', 'Marco Bianchi', 'Doetinchem', 'Hamburgerstraat 22', 'template2', '#7D1D3F', 'actief-stil', 6, false],
            ['Trattoria Milano', 'Antonio Esposito', 'Arnhem', 'Steenstraat 88', 'template3', '#1F2937', 'actief', 3, true],
            ['Pizzeria Napoli', 'Elena Costa', 'Utrecht', 'Oudegracht 145', 'template1', '#E63946', 'proef-3', 4, true],
            ['Da Vinci', 'Peter Jansen', 'Eindhoven', 'Kleine Berg 7', 'template2', '#7D1D3F', 'proef-6', 3, false],
            ['Piccola Italia', 'Sofia Marini', 'Zwolle', 'Melkmarkt 19', 'template1', '#E63946', 'proef-18', 2, false],
            ['Il Forno', 'Luca Ferrari', 'Groningen', 'Vismarkt 41', 'template4', '#F5B301', 'proef-25', 0, false],
            ['Mamma Mia', 'Fatima el Idrissi', 'Rotterdam', 'Witte de Withstraat 55', 'template3', '#1F2937', 'proef-12', 2, true],
            ['Pizzeria Vesuvio', 'Ahmed Yilmaz', 'Den Haag', 'Frederikstraat 30', 'template1', '#E63946', 'verlopen-5', 6, false],
            ['Bella Napoli', 'Daan de Boer', 'Tilburg', 'Heuvelstraat 101', 'template2', '#7D1D3F', 'verlopen-15', 8, false],
            ['Corleone', 'Julia van Dijk', 'Breda', 'Ginnekenstraat 14', 'template4', '#F5B301', 'verlopen-30', 8, false],
        ];

        $coordinaten = [
            'Arnhem' => [51.9851, 5.8987], 'Nijmegen' => [51.8449, 5.8646], 'Doetinchem' => [51.9648, 6.2884],
            'Utrecht' => [52.0907, 5.1214], 'Eindhoven' => [51.4416, 5.4697], 'Zwolle' => [52.5168, 6.0830],
            'Groningen' => [53.2194, 6.5665], 'Rotterdam' => [51.9225, 4.4792], 'Den Haag' => [52.0705, 4.3007],
            'Tilburg' => [51.5606, 5.0919], 'Breda' => [51.5719, 4.7683],
        ];

        foreach ($zaken as $i => [$naam, $persoon, $plaats, $straat, $thema, $kleur, $staat, $wekenGeleden, $online]) {
            $slug = Str::slug($naam);
            $email = $i === 0 ? 'demo@pizzeria.nl' : $slug . '@demo.mijnpizzeria.nl';
            if ($i === 0) {
                $slug = 'pizzeriasole'; // de slug waar de hele marketingsite naar linkt
            }
            $aangemeld = now()->subWeeks($wekenGeleden)->subDays($i % 5)->setTime(rand(9, 21), rand(0, 59));
            $setupKlaar = ! in_array($naam, ['Piccola Italia', 'Il Forno', 'Corleone'], true);

            $user = User::create([
                'name' => $persoon,
                'email' => $email,
                'password' => 'tijdelijk',
                'slug' => $slug,
                'onboarding' => [
                    'name' => $naam,
                    'person' => $persoon,
                    'email' => $email,
                    'phone' => '06 ' . rand(10, 49) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'kvk' => (string) rand(10000000, 99999999),
                    'street' => $straat,
                    'zip' => rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90)),
                    'city' => $plaats,
                    'days' => ['ma' => false, 'di' => true, 'wo' => true, 'do' => true, 'vr' => true, 'za' => true, 'zo' => true],
                    'open' => '16:00',
                    'close' => '21:30',
                    'hoursMode' => 'same',
                    'dayTimes' => [],
                    'spaarpunten' => $i % 3 !== 2,
                    'domainMode' => 'sub',
                    'ownDomain' => '',
                    'color' => $kleur,
                    'theme' => $thema,
                    'setup_compleet' => $setupKlaar,
                    'menu_geimporteerd' => true,
                    'geo_adres' => $straat . ', ' . $plaats,
                ],
            ]);

            [$lat, $lng] = $coordinaten[$plaats];
            $vullen = [
                'password' => $this->wachtwoord,
                'intro_seen' => true,
                'is_online' => $online,
                'order_mode' => $i % 4 === 3 ? 'alleen_afhalen' : 'bezorgen_afhalen',
                'lat' => $lat + (($i % 7) - 3) * 0.003,
                'lng' => $lng + (($i % 5) - 2) * 0.004,
                'bezorgkosten' => [['km' => 3, 'kosten' => 0, 'min' => 1500], ['km' => 8, 'kosten' => 250, 'min' => 2000]],
                'created_at' => $aangemeld,
            ];

            // Abonnement of proefperiode, per levensfase
            if (str_starts_with($staat, 'actief')) {
                $vullen += [
                    'abonnement_actief' => true,
                    'abonnement_sinds' => $aangemeld->copy()->addDays(rand(8, 25)),
                    'proef_tot' => $aangemeld->copy()->addDays(30),
                    'stripe_customer_id' => 'cus_demo' . Str::random(10),
                    'stripe_subscription_id' => 'sub_demo' . Str::random(10),
                ];
            } elseif (str_starts_with($staat, 'proef')) {
                $vullen['proef_tot'] = now()->addDays((int) Str::after($staat, 'proef-'))->endOfDay();
            } else {
                $vullen['proef_tot'] = now()->subDays((int) Str::after($staat, 'verlopen-'))->startOfDay();
            }

            // Beheernotities en snoozes voor de sales-demo
            if ($naam === 'Pizzeria Vesuvio') {
                $vullen['beheer_notitie'] = 'Gebeld op maandag: vond het systeem goed maar wil eerst het hoogseizoen afwachten. In oktober nog eens proberen.';
            }
            if ($naam === 'Bella Napoli') {
                $vullen['beheer_notitie'] = 'Wil eerst overleggen met zijn compagnon. Niet pushen.';
                $vullen['opvolgen_vanaf'] = now()->addDays(5);
            }
            if ($naam === 'Pizzeria Napoli') {
                $vullen['beheer_notitie'] = 'Enthousiast na de demo. Twijfelt alleen nog over de bezorgkosten-instellingen.';
            }

            $user->forceFill($vullen)->save();

            if ($setupKlaar) {
                $this->menuVoor($user, $i === 0);
            }

            match ($staat) {
                'actief' => $this->bestellingenVoor($user, $i === 0 ? 90 : ($naam === 'Trattoria Milano' ? 25 : 50), vandaag: $online),
                'actief-stil' => $this->bestellingenVoor($user, 30, stopDagenGeleden: 16),
                default => str_starts_with($staat, 'proef') && $setupKlaar ? $this->bestellingenVoor($user, rand(4, 14)) : null,
            };
        }

        // De showcase krijgt ook een bezorger; die zaak laten we het vaakst zien
        $showcase = User::where('email', 'demo@pizzeria.nl')->first();
        if ($showcase) {
            $this->teamVoor($showcase, [
                ['Sam Hendriks', 'sam@sole.demo', 'bezorger', true],
                ['Ilias Yilmaz', 'ilias@sole.demo', 'bezorger', true],
            ]);
        }

        $this->demoAccount();
        $this->feedbackVullen();
    }

    /**
     * Het vaste demo-pizzeria-account: de zaak die je aan geïnteresseerden laat
     * zien. Alles staat aan en alles is gevuld: volle menukaart, spaarklanten,
     * een flinke bestelgeschiedenis en open bestellingen van vandaag.
     */
    private function demoAccount(): void
    {
        $user = User::create([
            'name' => 'Nina de Vries',
            'email' => 'demo-pizzeria@mijnpizzeria.nl',
            'password' => 'tijdelijk',
            'slug' => 'pizzeria-fiorella',
            'onboarding' => [
                'name' => 'Pizzeria Fiorella',
                'person' => 'Nina de Vries',
                'email' => 'demo-pizzeria@mijnpizzeria.nl',
                'phone' => '06 24 681 357',
                'kvk' => '77665544',
                'street' => 'Prinsengracht 88',
                'zip' => '1015 EA',
                'city' => 'Amsterdam',
                'days' => ['ma' => false, 'di' => true, 'wo' => true, 'do' => true, 'vr' => true, 'za' => true, 'zo' => true],
                'open' => '16:00',
                'close' => '22:00',
                'hoursMode' => 'same',
                'dayTimes' => [],
                'spaarpunten' => true,
                'domainMode' => 'sub',
                'ownDomain' => '',
                'color' => '#E63946',
                'theme' => 'template1',
                'setup_compleet' => true,
                'menu_geimporteerd' => true,
                'geo_adres' => 'Prinsengracht 88, Amsterdam',
            ],
        ]);

        $user->forceFill([
            'password' => $this->wachtwoord,
            'intro_seen' => true,
            'is_online' => true,
            'order_mode' => 'bezorgen_afhalen',
            'lat' => 52.3745,
            'lng' => 4.8840,
            'bezorgkosten' => [['km' => 3, 'kosten' => 0, 'min' => 1500], ['km' => 6, 'kosten' => 200, 'min' => 2000], ['km' => 10, 'kosten' => 350, 'min' => 2500]],
            'abonnement_actief' => true,
            'abonnement_sinds' => now()->subMonths(3)->subDays(4),
            'proef_tot' => now()->subMonths(3)->addDays(26),
            'stripe_customer_id' => 'cus_demo' . Str::random(10),
            'stripe_subscription_id' => 'sub_demo' . Str::random(10),
            'created_at' => now()->subMonths(4),
        ])->save();

        $this->menuVoor($user, volledig: true);
        $this->bestellingenVoor($user, 120, vandaag: true);
        $this->teamVoor($user, [
            ['Youssef El Amrani', 'youssef@fiorella.demo', 'bezorger', true],
            ['Lisa Bergsma', 'lisa@fiorella.demo', 'bezorger', true],
            ['Marco Rossi', 'marco@fiorella.demo', 'keuken', true],
            ['Tim de Groot', 'tim@fiorella.demo', 'bezorger', false],   // nog niet geactiveerd
        ]);
    }

    /**
     * Teamleden van een zaak. Wie \$actief is heeft zijn uitnodiging al geaccepteerd
     * en kan inloggen op /bezorger; de rest staat nog op uitnodiging.
     * Bezorgde ritten van vandaag zetten we op naam, zodat de historie klopt.
     */
    private function teamVoor(User $user, array $leden): void
    {
        $bezorgers = [];
        foreach ($leden as [$naam, $email, $rol, $geactiveerd]) {
            $medewerker = Medewerker::create([
                'user_id' => $user->id,
                'naam' => $naam,
                'email' => $email,
                'telefoon' => '06 ' . rand(10, 59) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'wachtwoord' => $geactiveerd ? $this->wachtwoord : null,
                'rol' => $rol,
                'actief' => true,
                'uitnodiging_token' => $geactiveerd ? null : Str::random(64),
                'uitgenodigd_op' => now()->subDays($geactiveerd ? rand(20, 90) : 2),
                'laatst_actief_op' => $geactiveerd ? now()->subMinutes(rand(5, 240)) : null,
            ]);
            if ($geactiveerd && $rol === 'bezorger') {
                $bezorgers[] = $medewerker;
            }
        }
        if ($bezorgers === []) {
            return;
        }

        // De bezorgde ritten van vandaag verdelen over de bezorgers
        $user->orders()
            ->where('type', 'bezorgen')->where('status', 'bezorgd')->whereDate('created_at', today())
            ->get()
            ->each(function (Order $order) use ($bezorgers) {
                $bezorger = $bezorgers[array_rand($bezorgers)];
                $order->forceFill([
                    'bezorger_id' => $bezorger->id,
                    'bezorgd_om' => $order->created_at->copy()->addMinutes(rand(25, 45)),
                ])->save();
            });

        // En eentje die nu onderweg is, zodat het teamscherm meteen iets te doen heeft
        $user->orders()->where('type', 'bezorgen')->where('status', 'onderweg')->first()
            ?->forceFill(['bezorger_id' => $bezorgers[0]->id])->save();
    }

    /** Menukaart: de showcase krijgt de volle kaart, de rest een compacte */
    private function menuVoor(User $user, bool $volledig): void
    {
        $kaart = [
            'Klassiekers' => [
                ['Margherita', 950, '🍕', ['tomatensaus', 'fior di latte', 'verse basilicum'], ['gluten', 'lactose']],
                ['Pepperoni', 1150, '🍕', ['tomatensaus', 'pittige pepperoni', 'mozzarella', 'oregano'], ['gluten', 'lactose']],
                ['Salami', 1150, '🍕', ['tomatensaus', 'milde salami', 'mozzarella'], ['gluten', 'lactose']],
                ['Funghi', 1100, '🍄', ['tomatensaus', 'kastanjechampignons', 'knoflook', 'peterselie'], ['gluten', 'lactose']],
                ['Quattro Formaggi', 1300, '🧀', ['mozzarella', 'gorgonzola', 'parmezaan', 'taleggio'], ['gluten', 'lactose']],
            ],
            "Speciale pizza's" => [
                ['Diavola', 1250, '🌶️', ['nduja', 'rode peper', 'zwarte olijven', 'mozzarella'], ['gluten', 'lactose']],
                ['Parma e Rucola', 1400, '🥓', ['parmaham', 'rucola', 'parmezaan na de oven'], ['gluten', 'lactose']],
                ['Burrata Speciale', 1450, '🧀', ['romige burrata', 'cherrytomaat', 'basilicumolie'], ['gluten', 'lactose']],
                ['Tonno', 1250, '🐟', ['tonijn', 'rode ui', 'kappertjes'], ['gluten', 'vis']],
                ['Frutti di Mare', 1500, '🦐', ['garnalen', 'mosselen', 'inktvis', 'knoflook'], ['gluten', 'schaaldieren']],
            ],
            'Calzones' => [
                ['Calzone Sole', 1350, '🥟', ['dubbelgevouwen', 'ham', 'ricotta', 'mozzarella'], ['gluten', 'lactose']],
                ['Calzone Verdure', 1300, '🥦', ['gegrilde groenten', 'ricotta', 'spinazie'], ['gluten', 'lactose']],
            ],
            'Dranken' => [
                ['Cola', 300, '🥤', [], []],
                ['Cola Zero', 300, '🥤', [], []],
                ['San Pellegrino', 350, '💧', [], []],
                ['Peroni', 400, '🍺', [], []],
                ['Huiswijn rood', 550, '🍷', [], ['sulfiet']],
            ],
            'Desserts' => [
                ['Tiramisu', 650, '🍰', ['huisgemaakt', 'mascarpone', 'espresso'], ['gluten', 'lactose', 'ei']],
                ['Panna Cotta', 550, '🍨', ['vanille', 'rood fruit'], ['lactose']],
            ],
        ];

        if (! $volledig) {
            $kaart = [
                'Klassiekers' => array_slice($kaart['Klassiekers'], 0, 4),
                "Speciale pizza's" => array_slice($kaart["Speciale pizza's"], 0, 2),
                'Dranken' => array_slice($kaart['Dranken'], 0, 2),
            ];
        }

        $pizzaOpties = [[
            'naam' => "Extra's",
            'keuzes' => [
                ['naam' => 'Extra kaas', 'prijs' => 150],
                ['naam' => 'Extra salami', 'prijs' => 200],
                ['naam' => 'Verse knoflook', 'prijs' => 100],
                ['naam' => 'Extra knoflooksaus', 'prijs' => 100],
            ],
        ]];

        $volgorde = 0;
        foreach ($kaart as $categorie => $items) {
            foreach ($items as [$itemNaam, $prijs, $icoon, $ingredienten, $allergenen]) {
                $user->menuItems()->create([
                    'categorie' => $categorie,
                    'naam' => $itemNaam,
                    'prijs' => $prijs,
                    'icoon' => $icoon,
                    'foto' => $this->gerechtFoto($itemNaam, $categorie),
                    'ingredienten' => $ingredienten,
                    'allergenen' => $allergenen,
                    'opties' => str_contains($categorie, 'izza') || $categorie === 'Klassiekers' || $categorie === 'Calzones' ? $pizzaOpties : [],
                    'actief' => true,
                    'volgorde' => $volgorde++,
                ]);
            }
        }
    }

    /** Elke demozaak toont gerechten met foto: eerst op naam, dan op categorie, anders een pizzafoto op toerbeurt */
    private function gerechtFoto(string $naam, string $categorie): string
    {
        static $beurt = 0;
        $naam = mb_strtolower($naam);
        $opNaam = [
            'margherita' => 'margherita.jpg',
            'salami' => 'pepperoni.jpg', 'pepperoni' => 'pepperoni.jpg', 'diavola' => 'pepperoni.jpg',
            'funghi' => 'rustiek.jpg', 'champignon' => 'rustiek.jpg',
            'formaggi' => 'koken.jpg', 'kaas' => 'koken.jpg',
            'calzone' => 'oven.jpg',
            'tonno' => 'donker.jpg', 'mare' => 'donker.jpg',
            'tiramisu' => 'dessert.jpg', 'panna' => 'dessert.jpg',
        ];
        foreach ($opNaam as $trefwoord => $foto) {
            if (str_contains($naam, $trefwoord)) {
                return asset('assets/eten/' . $foto);
            }
        }
        $categorie = mb_strtolower($categorie);
        if (str_contains($categorie, 'drank')) {
            return asset('assets/eten/drank.jpg');
        }
        if (str_contains($categorie, 'dessert')) {
            return asset('assets/eten/dessert.jpg');
        }
        $roulatie = ['pepperoni.jpg', 'margherita.jpg', 'rustiek.jpg', 'oven.jpg', 'ingredienten.jpg'];

        return asset('assets/eten/' . $roulatie[$beurt++ % count($roulatie)]);
    }

    /** Bestellingen verspreid over de laatste acht weken, met klanten en spaarpunten */
    private function bestellingenVoor(User $user, int $aantal, bool $vandaag = false, int $stopDagenGeleden = 0): void
    {
        $menu = $user->menuItems()->where('actief', true)->get();
        if ($menu->isEmpty()) {
            return;
        }

        $klantNamen = ['Sanne', 'Ahmed', 'Julia', 'Daan', 'Fatima', 'Ruben', 'Lisa', 'Tom', 'Anna', 'Max', 'Noor', 'Bram'];
        $adressen = ['Kerkstraat 24', 'Lindelaan 7a', 'Molenweg 118', 'Dorpsstraat 3', 'Beukenhof 52', 'Stationsplein 9', 'Esdoornlaan 31'];

        // Vaste klanten van de zaak, met spaarpunten (alleen als spaarpunten aanstaan)
        $klanten = collect();
        if ($user->onboarding['spaarpunten'] ?? false) {
            foreach (array_slice($klantNamen, 0, 8) as $kNaam) {
                $klanten->push(Klant::create([
                    'user_id' => $user->id,
                    'naam' => $kNaam,
                    'email' => Str::slug($kNaam) . rand(2, 99) . '@voorbeeld.nl',
                    'wachtwoord' => $this->wachtwoord,
                    'punten' => rand(15, 460),
                    'adressen' => [['label' => 'Thuis', 'adres' => $adressen[array_rand($adressen)]]],
                ]));
            }
        }

        $nummer = 411;
        for ($i = 0; $i < $aantal; $i++) {
            // Recente dagen wegen zwaarder, en een stille zaak stopt op een dag in het verleden
            $dagenTerug = $stopDagenGeleden + (int) floor(pow(rand(0, 1000) / 1000, 1.6) * (54 - $stopDagenGeleden));
            $moment = now()->subDays($dagenTerug)->setTime(rand(16, 21), rand(0, 59));

            $items = [];
            $totaal = 0;
            foreach (range(1, rand(1, 3)) as $r) {
                $bron = $menu->random();
                $opties = [];
                $keuzes = collect($bron->opties ?? [])->flatMap(fn ($g) => $g['keuzes'] ?? []);
                if ($keuzes->isNotEmpty() && rand(0, 2) === 0) {
                    $keuze = $keuzes->random();
                    $opties[] = ['naam' => $keuze['naam'], 'prijs' => (int) $keuze['prijs'], 'type' => 'extra'];
                }
                $aantalStuks = rand(0, 3) ? 1 : 2;
                $items[] = ['naam' => $bron->naam, 'prijs' => $bron->prijs, 'aantal' => $aantalStuks, 'opties' => $opties];
                $totaal += ($bron->prijs + array_sum(array_column($opties, 'prijs'))) * $aantalStuks;
            }

            $type = rand(0, 3) ? 'bezorgen' : 'afhalen';
            $klant = $klanten->isNotEmpty() && rand(0, 1) ? $klanten->random() : null;

            Order::create([
                'user_id' => $user->id,
                'klant_id' => $klant?->id,
                'nummer' => ++$nummer,
                'token' => Str::lower(Str::random(24)),
                'klant' => $klant?->naam ?? $klantNamen[array_rand($klantNamen)],
                'items' => $items,
                'totaal' => $totaal,
                'punten' => intdiv($totaal, 100),
                'status' => 'bezorgd',
                'type' => $type,
                'adres' => $type === 'bezorgen' ? $adressen[array_rand($adressen)] : null,
                'is_demo' => false,
                'created_at' => $moment,
                'updated_at' => $moment,
            ]);
        }

        // Een paar bestellingen van vandaag, deels nog open (voor het wallboard en de tellers)
        if ($vandaag) {
            foreach ([['nieuw', -10], ['nieuw', -25], ['bereiden', -40], ['onderweg', -55], ['bezorgd', -95]] as [$status, $minuten]) {
                $bron = $menu->random();
                $moment = now()->addMinutes($minuten);
                Order::create([
                    'user_id' => $user->id,
                    'nummer' => ++$nummer,
                    'token' => Str::lower(Str::random(24)),
                    'klant' => $klantNamen[array_rand($klantNamen)],
                    'items' => [['naam' => $bron->naam, 'prijs' => $bron->prijs, 'aantal' => 1, 'opties' => []]],
                    'totaal' => $bron->prijs,
                    'punten' => intdiv($bron->prijs, 100),
                    'status' => $status,
                    'type' => rand(0, 1) ? 'bezorgen' : 'afhalen',
                    'adres' => $adressen[array_rand($adressen)],
                    'eta_minuten' => in_array($status, ['bereiden', 'onderweg'], true) ? 35 : null,
                    'is_demo' => false,
                    'created_at' => $moment,
                    'updated_at' => $moment,
                ]);
            }
        }
    }

    /** Feedback uit de formulieren, verspreid over de contexten voor de sales-pagina */
    private function feedbackVullen(): void
    {
        $per = fn (string $naam) => User::where('onboarding', 'like', '%' . $naam . '%')->first();

        $reacties = [
            ['Pizzeria Vesuvio', 'winback', 'Te weinig bestellingen via de pagina', 'De pagina zag er goed uit, maar mijn klanten bellen toch liever. Misschien later nog eens.', 8],
            ['Bella Napoli', 'winback', 'Het kostte me te veel tijd', 'Ik kwam er niet aan toe om de menukaart af te maken. Als iemand dat met me doet, wil ik het nog een keer proberen.', 12],
            ['Pizzeria Napoli', 'proef', 'Ik twijfel nog over de prijs', 'Het werkt goed, ik wil alleen eerst zien hoeveel bestellingen er echt binnenkomen.', 2],
            ['Mamma Mia', 'proef', 'Alles duidelijk, ik ben tevreden', null, 5],
            ['Bella Roma', 'hulp', 'Hoe stel ik bezorgkosten per wijk in?', 'Ik wil een lagere bezorgkosten voor het centrum.', 9],
            ['La Luna', 'hulp', 'Vraag over de spaarpunten', 'Kan ik zelf bepalen hoeveel punten een euro waard is?', 16],
            ['Da Vinci', 'verlenging', 'Twee weken extra aangevraagd', 'Druk met de verbouwing, wil daarna echt beginnen.', 1],
        ];

        foreach ($reacties as [$zaak, $context, $antwoord, $toelichting, $dagenGeleden]) {
            $user = $per($zaak);
            if (! $user) {
                continue;
            }
            Feedback::create([
                'user_id' => $user->id,
                'context' => $context,
                'antwoord' => $antwoord,
                'toelichting' => $toelichting,
            ])->forceFill(['created_at' => now()->subDays($dagenGeleden)->setTime(rand(10, 20), rand(0, 59))])->save();
        }
    }
}
