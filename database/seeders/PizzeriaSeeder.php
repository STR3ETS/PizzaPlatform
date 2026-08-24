<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Complete demo-pizzeria zodat je na migrate:fresh direct kunt inloggen
 * en alles gevuld is: menu, bezorgkosten, locatie en wat bestellingen.
 *
 * Inloggen: demo@pizzeria.nl / geheim123
 * Bestelpagina: /bestellen/pizzeriaroma
 */
class PizzeriaSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'demo@pizzeria.nl')->exists()) {
            return;
        }

        $user = User::create([
            'name' => 'Mario Rossi',
            'email' => 'demo@pizzeria.nl',
            'password' => Hash::make('geheim123'),
            'slug' => 'pizzeriaroma',
            'onboarding' => [
                'name' => 'Pizzeria Roma',
                'person' => 'Mario Rossi',
                'email' => 'demo@pizzeria.nl',
                'phone' => '026 123 45 67',
                'kvk' => '87654321',
                'street' => 'Velperweg 49',
                'zip' => '6824 BG',
                'city' => 'Arnhem',
                'days' => ['ma' => false, 'di' => true, 'wo' => true, 'do' => true, 'vr' => true, 'za' => true, 'zo' => true],
                'open' => '16:00',
                'close' => '21:30',
                'hoursMode' => 'same',
                'dayTimes' => [],
                'categories' => [['id' => 'klassiekers', 'emoji' => '🍕', 'name' => 'Klassiekers']],
                'menu' => [],
                'menu_geimporteerd' => true,
                'payment' => 'mollie',
                'domainMode' => 'sub',
                'ownDomain' => '',
                'color' => '#E63946',
                'theme' => 'fresco',
                'geo_adres' => 'Velperweg 49, 6824 BG Arnhem',
            ],
        ]);

        $user->forceFill([
            'intro_seen' => true,
            'is_online' => true,
            'order_mode' => 'bezorgen_afhalen',
            'lat' => 51.9926,
            'lng' => 5.9312,
            'bezorgkosten' => [['km' => 2, 'kosten' => 100], ['km' => 4, 'kosten' => 250], ['km' => 7, 'kosten' => 400]],
        ])->save();

        $formaat = ['naam' => 'Formaat', 'type' => 'een', 'keuzes' => [['naam' => '25 cm', 'prijs' => 0], ['naam' => '30 cm', 'prijs' => 250], ['naam' => '35 cm', 'prijs' => 450]]];
        $extras = ['naam' => 'Extra ingrediënten', 'type' => 'meerdere', 'keuzes' => [['naam' => 'Extra kaas', 'prijs' => 150], ['naam' => 'Champignons', 'prijs' => 100], ['naam' => 'Salami', 'prijs' => 150], ['naam' => 'Rode ui', 'prijs' => 75]]];
        $sauzen = ['naam' => 'Saus erbij', 'type' => 'meerdere', 'keuzes' => [['naam' => 'Knoflooksaus', 'prijs' => 100], ['naam' => 'Sambal', 'prijs' => 50]]];

        $menu = [
            ['Klassiekers', '🍕', 'Margherita', 'De klassieker: tomatensaus, mozzarella en verse basilicum.', 950, ['tomatensaus', 'mozzarella', 'basilicum'], ['gluten', 'melk'], [$formaat, $extras, $sauzen]],
            ['Klassiekers', '🍕', 'Salami', 'Rijk belegd met Italiaanse salami.', 1100, ['tomatensaus', 'mozzarella', 'salami'], ['gluten', 'melk'], [$formaat, $extras, $sauzen]],
            ['Klassiekers', '🍄', 'Funghi', 'Verse champignons en een vleugje knoflook.', 1050, ['tomatensaus', 'mozzarella', 'champignons', 'knoflook'], ['gluten', 'melk'], [$formaat, $extras, $sauzen]],
            ['Klassiekers', '🧀', 'Quattro Formaggi', 'Vier kazen: mozzarella, gorgonzola, parmezaan en provolone.', 1250, ['mozzarella', 'gorgonzola', 'parmezaan', 'provolone'], ['gluten', 'melk'], [$formaat, $extras]],
            ['Speciaal', '🌶️', 'Diavola', 'Pittige salami, rode ui en verse pepers.', 1200, ['tomatensaus', 'mozzarella', 'pittige salami', 'rode ui', 'peper'], ['gluten', 'melk'], [$formaat, $extras, $sauzen]],
            ['Speciaal', '🐟', 'Tonno', 'Tonijn, rode ui en kappertjes.', 1200, ['tomatensaus', 'mozzarella', 'tonijn', 'rode ui', 'kappertjes'], ['gluten', 'melk', 'vis'], [$formaat, $extras]],
            ['Speciaal', '🥟', 'Calzone Roma', 'Dichtgevouwen pizza met ham, ricotta en champignons.', 1250, ['tomatensaus', 'ricotta', 'ham', 'champignons'], ['gluten', 'melk'], [$extras]],
            ['Dranken', '🥤', 'Cola', null, 250, [], [], []],
            ['Dranken', '💧', 'Spa blauw', null, 200, [], [], []],
            ['Desserts', '🍰', 'Tiramisu', 'Huisgemaakt, met echte mascarpone.', 550, ['mascarpone', 'lange vingers', 'koffie', 'cacao'], ['gluten', 'melk', 'ei'], []],
        ];
        foreach ($menu as $i => [$categorie, $icoon, $naam, $beschrijving, $prijs, $ingredienten, $allergenen, $opties]) {
            $user->menuItems()->create([
                'categorie' => $categorie,
                'icoon' => $icoon,
                'naam' => $naam,
                'beschrijving' => $beschrijving,
                'prijs' => $prijs,
                'ingredienten' => $ingredienten,
                'allergenen' => $allergenen,
                'opties' => $opties,
                'actief' => true,
                'volgorde' => $i,
            ]);
        }

        // Een paar bestellingen zodat het dashboard leeft
        $orders = [
            [412, 'Sanne', 'nieuw', 'bezorgen', 'Bronbeeklaan 12, 6824 PE Arnhem', 'Graag aanbellen bij de achterdeur', null, [
                ['naam' => 'Margherita', 'prijs' => 950, 'aantal' => 1, 'opties' => [['naam' => '30 cm', 'prijs' => 250, 'type' => 'extra']]],
                ['naam' => 'Cola', 'prijs' => 250, 'aantal' => 2, 'opties' => []],
                ['naam' => 'Bezorgkosten', 'prijs' => 100, 'aantal' => 1, 'opties' => []],
            ]],
            [413, 'Ahmed', 'geaccepteerd', 'afhalen', null, null, 30, [
                ['naam' => 'Diavola', 'prijs' => 1200, 'aantal' => 2, 'opties' => [['naam' => 'Extra kaas', 'prijs' => 150, 'type' => 'extra'], ['naam' => 'zonder rode ui', 'prijs' => 0, 'type' => 'zonder']]],
            ]],
            [414, 'Julia', 'oven', 'bezorgen', 'Velperweg 102, 6824 HL Arnhem', null, 45, [
                ['naam' => 'Quattro Formaggi', 'prijs' => 1250, 'aantal' => 1, 'opties' => []],
                ['naam' => 'Tiramisu', 'prijs' => 550, 'aantal' => 2, 'opties' => []],
                ['naam' => 'Bezorgkosten', 'prijs' => 100, 'aantal' => 1, 'opties' => []],
            ]],
            [415, 'Daan', 'bezorgd', 'afhalen', null, null, 20, [
                ['naam' => 'Funghi', 'prijs' => 1050, 'aantal' => 1, 'opties' => []],
            ]],
        ];
        foreach ($orders as [$nummer, $klant, $status, $type, $adres, $opmerking, $eta, $items]) {
            $totaal = 0;
            foreach ($items as $item) {
                $totaal += ($item['prijs'] + array_sum(array_column($item['opties'], 'prijs'))) * $item['aantal'];
            }
            Order::create([
                'user_id' => $user->id,
                'nummer' => $nummer,
                'klant' => $klant,
                'items' => $items,
                'totaal' => $totaal,
                'status' => $status,
                'type' => $type,
                'adres' => $adres,
                'opmerking' => $opmerking,
                'eta_minuten' => $eta,
                'is_demo' => true,
            ]);
        }
    }
}
