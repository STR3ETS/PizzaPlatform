<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// De onboarding is tegelijk de registratie
Route::get('/onboarding', function () {
    return view('onboarding');
})->name('onboarding');
Route::post('/onboarding/afronden', [OnboardingController::class, 'finish'])->name('onboarding.finish');
Route::redirect('/registreren', '/onboarding');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/wachtwoord-vergeten', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/wachtwoord-vergeten', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/wachtwoord-herstellen/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/wachtwoord-herstellen', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        // Eenmalig: de menukaart uit de onboarding overnemen naar de echte menukaart
        $onboarding = $user->onboarding ?? [];
        if (empty($onboarding['menu_geimporteerd']) && ! empty($onboarding['menu']) && ! $user->menuItems()->exists()) {
            $cats = collect($onboarding['categories'] ?? [])->keyBy('id');
            foreach (array_values($onboarding['menu']) as $i => $m) {
                if (empty($m['name'])) {
                    continue;
                }
                $user->menuItems()->create([
                    'categorie' => $cats[$m['cat'] ?? '']['name'] ?? 'Menu',
                    'naam' => $m['name'],
                    'prijs' => (int) round(((float) str_replace(',', '.', (string) ($m['price'] ?? 0))) * 100),
                    'icoon' => ($m['icon']['t'] ?? '') === 'e' ? ($m['icon']['v'] ?? null) : null,
                    'volgorde' => $i,
                ]);
            }
            $onboarding['menu_geimporteerd'] = true;
            $user->forceFill(['onboarding' => $onboarding])->save();
        }

        // De zaak automatisch op de kaart zetten op basis van het ingevulde adres.
        // Alleen (opnieuw) opzoeken als er nog geen locatie is of het adres is gewijzigd.
        $straat = trim($onboarding['street'] ?? '');
        $plaats = trim($onboarding['city'] ?? '');
        if ($straat !== '' && $plaats !== '') {
            $adres = $straat . ', ' . trim(($onboarding['zip'] ?? '') . ' ' . $plaats);
            if ($user->lat === null || ($onboarding['geo_adres'] ?? null) !== $adres) {
                try {
                    $vraag = [
                        'street' => $straat,
                        'city' => $plaats,
                        'country' => 'Nederland',
                        'format' => 'json',
                        'limit' => 1,
                    ];
                    if (trim($onboarding['zip'] ?? '') !== '') {
                        $vraag['postalcode'] = trim($onboarding['zip']);
                    }
                    $antwoord = \Illuminate\Support\Facades\Http::withHeaders(['User-Agent' => 'PizzaPlatform (ontwikkeling)'])
                        ->timeout(4)
                        ->get('https://nominatim.openstreetmap.org/search', $vraag);
                    $hit = $antwoord->ok() ? ($antwoord->json()[0] ?? null) : null;
                    if ($hit) {
                        $user->forceFill(['lat' => (float) $hit['lat'], 'lng' => (float) $hit['lon']]);
                    }
                    $onboarding['geo_adres'] = $adres;
                    $user->forceFill(['onboarding' => $onboarding])->save();
                } catch (\Throwable) {
                    // Stil laten falen: dan zet de pizzeria de pin zelf op de kaart
                }
            }
        }

        return view('dashboard.index');
    })->name('dashboard');

    Route::post('/instellingen/gegevens', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:60'],
            'person' => ['sometimes', 'required', 'string', 'max:60'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'kvk' => ['sometimes', 'nullable', 'string', 'max:8'],
            'street' => ['sometimes', 'nullable', 'string', 'max:80'],
            'zip' => ['sometimes', 'nullable', 'string', 'max:10'],
            'city' => ['sometimes', 'nullable', 'string', 'max:60'],
            'days' => ['sometimes', 'array'],
            'open' => ['sometimes', 'date_format:H:i'],
            'close' => ['sometimes', 'date_format:H:i'],
            'hoursMode' => ['sometimes', 'in:same,perday'],
            'dayTimes' => ['sometimes', 'array'],
            'dayTimes.*.open' => ['required', 'date_format:H:i'],
            'dayTimes.*.close' => ['required', 'date_format:H:i'],
            'payment' => ['sometimes', 'in:mollie,stripe,later'],
            'domainMode' => ['sometimes', 'in:sub,own'],
            'ownDomain' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $dagen = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
        if (isset($data['days'])) {
            $data['days'] = array_map(fn ($v) => (bool) $v, array_intersect_key($data['days'], array_flip($dagen)));
        }
        if (isset($data['dayTimes'])) {
            $data['dayTimes'] = array_intersect_key($data['dayTimes'], array_flip($dagen));
        }

        $user = $request->user();
        $vullen = ['onboarding' => array_merge($user->onboarding ?? [], $data)];
        if (isset($data['person'])) {
            $vullen['name'] = $data['person'];
        }
        $user->forceFill($vullen)->save();

        return response()->json(['ok' => true]);
    })->name('instellingen.opslaan');

    Route::post('/instellingen/bezorg', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'tiers' => ['nullable', 'array', 'max:8'],
            'tiers.*.km' => ['required', 'numeric', 'min:0.1', 'max:30'],
            'tiers.*.kosten' => ['required', 'integer', 'min:0', 'max:2500'],
        ]);
        $request->user()->forceFill([
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'bezorgkosten' => $data['tiers'] ?? [],
        ])->save();

        return response()->json(['ok' => true]);
    })->name('bezorg.opslaan');

    Route::post('/menukaart', [MenuController::class, 'store'])->name('menu.store');
    Route::patch('/menukaart/{item}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menukaart/{item}', [MenuController::class, 'destroy'])->name('menu.delete');

    Route::post('/dashboard/intro-gezien', function (\Illuminate\Http\Request $request) {
        $request->user()->forceFill(['intro_seen' => true])->save();

        return response()->json(['ok' => true]);
    })->name('dashboard.intro');

    Route::post('/dashboard/status', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'online' => ['required', 'boolean'],
            'mode' => ['required', 'in:bezorgen_afhalen,alleen_afhalen'],
        ]);
        $request->user()->forceFill(['is_online' => $data['online'], 'order_mode' => $data['mode']])->save();

        return response()->json(['ok' => true]);
    })->name('dashboard.status');

    Route::post('/dashboard/demo-bestelling', function (\Illuminate\Http\Request $request) {
        $gerechten = [['Margherita', 950], ['Salami', 1100], ['Quattro Formaggi', 1250], ['Diavola', 1200], ['Calzone', 1250], ['Cola', 250]];
        $klanten = ['Sanne', 'Ahmed', 'Julia', 'Daan', 'Fatima', 'Ruben', 'Lisa', 'Tom'];
        $extras = [['Extra kaas', 150], ['Extra salami', 200], ['Dubbel champignons', 150], ['Extra knoflooksaus', 100]];
        $zonders = ['Zonder ui', 'Zonder olijven', 'Zonder ansjovis', 'Zonder paprika'];
        // Opmerkingen passend bij het type: een afhaler vraagt niet of je aanbelt
        $opmerkingen = [
            'algemeen' => ['Graag extra servetten erbij', 'Pizza mag goed doorbakken zijn', 'Knoflooksaus graag apart erbij'],
            'bezorgen' => ['Niet aanbellen aub, de baby slaapt', 'Bel even als je voor de deur staat', 'Achterom lopen, de voordeur is kapot'],
            'afhalen' => ['Ik kom iets later ophalen, rond een uur of 7', 'Mijn zoon haalt het op, hij heet Max'],
        ];
        $adressen = ['Kerkstraat 24', 'Lindelaan 7a', 'Molenweg 118', 'Dorpsstraat 3', 'Beukenhof 52', 'Stationsplein 9'];

        // Met vol=1 komt er gegarandeerd een compleet voorbeeld: wijzigingen, opmerking en adres
        $vol = $request->boolean('vol');

        // Heeft de pizzeria een echte menukaart, dan komen de voorbeelden daaruit
        $menuItems = $request->user()->menuItems()->where('actief', true)->get();

        $items = [];
        foreach (range(1, random_int(1, 3)) as $i) {
            if (! $vol && $menuItems->isNotEmpty()) {
                $bron = $menuItems->random();
                $keuzes = collect($bron->opties ?? [])->flatMap(fn ($g) => $g['keuzes'] ?? []);
                $ings = collect($bron->ingredienten ?? []);
                $opties = [];
                if (! random_int(0, 1)) {
                    if ($keuzes->isNotEmpty() && random_int(0, 1)) {
                        $k = $keuzes->random();
                        $opties[] = ['naam' => $k['naam'], 'prijs' => (int) $k['prijs'], 'type' => 'extra'];
                    }
                    if ($ings->isNotEmpty() && random_int(0, 1)) {
                        $opties[] = ['naam' => 'Zonder ' . mb_strtolower($ings->random()), 'prijs' => 0, 'type' => 'zonder'];
                    }
                }
                $items[] = ['naam' => $bron->naam, 'prijs' => $bron->prijs, 'aantal' => random_int(0, 3) ? 1 : 2, 'opties' => $opties];

                continue;
            }

            // Bij een compleet voorbeeld krijgt het eerste gerecht altijd wijzigingen, dus geen Cola
            $keuze = ($vol && $i === 1) ? array_slice($gerechten, 0, 5) : $gerechten;
            [$naam, $prijs] = $keuze[array_rand($keuze)];
            $opties = [];
            if (($vol && $i === 1) || ($naam !== 'Cola' && ! random_int(0, 1))) {
                if (($vol && $i === 1) || random_int(0, 1)) {
                    [$extraNaam, $extraPrijs] = $extras[array_rand($extras)];
                    $opties[] = ['naam' => $extraNaam, 'prijs' => $extraPrijs, 'type' => 'extra'];
                }
                if (($vol && $i === 1) || random_int(0, 1)) {
                    $opties[] = ['naam' => $zonders[array_rand($zonders)], 'prijs' => 0, 'type' => 'zonder'];
                }
            }
            $items[] = ['naam' => $naam, 'prijs' => $prijs, 'aantal' => random_int(0, 3) ? 1 : 2, 'opties' => $opties];
        }

        $totaal = 0;
        foreach ($items as $item) {
            $totaal += ($item['prijs'] + array_sum(array_column($item['opties'], 'prijs'))) * $item['aantal'];
        }
        $type = in_array($request->input('type'), ['bezorgen', 'afhalen'], true)
            ? $request->input('type')
            : (($vol || random_int(0, 2)) ? 'bezorgen' : 'afhalen');
        $opmerkingPool = array_merge($opmerkingen['algemeen'], $opmerkingen[$type]);

        $order = \App\Models\Order::create([
            'user_id' => $request->user()->id,
            'nummer' => ((int) $request->user()->orders()->max('nummer') ?: 411) + 1,
            'klant' => $klanten[array_rand($klanten)],
            'items' => $items,
            'totaal' => $totaal,
            'status' => 'nieuw',
            'type' => $type,
            'adres' => $type === 'bezorgen' ? $adressen[array_rand($adressen)] : null,
            'opmerking' => ($vol || ! random_int(0, 2)) ? $opmerkingPool[array_rand($opmerkingPool)] : null,
            'is_demo' => true,
        ]);

        return response()->json($order);
    })->name('orders.demo');

    Route::post('/bestellingen/{order}/status', function (\Illuminate\Http\Request $request, \App\Models\Order $order) {
        abort_unless($order->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'status' => ['required', 'in:nieuw,geaccepteerd,bereiden,oven,onderweg,bezorgd'],
            'eta' => ['nullable', 'integer', 'min:5', 'max:240'],
        ]);
        $order->update([
            'status' => $data['status'],
            'eta_minuten' => $data['eta'] ?? $order->eta_minuten,
        ]);

        return response()->json(['ok' => true]);
    })->name('orders.status');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
