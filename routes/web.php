<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BestelController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

// Subdomein per pizzeria: zaaknaam.mijnpizzeria.nl (lokaal: zaaknaam.localhost).
// Staat boven de gewone routes zodat '/' op een subdomein de bestelpagina is, niet de marketingsite.
// De templates krijgen basis '' mee, dus alle links en formulieren wijzen naar /afrekenen, /bestelling enzovoort op het subdomein zelf.
$centraalDomein = config('app.centraal_domein');
if ($centraalDomein) {
    Route::pattern('subSlug', '(?!www$)[a-z0-9-]+');
    Route::domain('{subSlug}.' . $centraalDomein)->group(function () {
        $zaak = fn (string $slug) => \App\Models\User::where('slug', $slug)->firstOrFail();
        Route::get('/', fn (string $subSlug) => app(BestelController::class)->t1Home($zaak($subSlug), ''));
        Route::get('/afrekenen', fn (string $subSlug) => app(BestelController::class)->t1Afrekenen($zaak($subSlug), ''));
        Route::post('/afrekenen', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1Plaats($zaak($subSlug), $request));
        Route::get('/inloggen', fn (string $subSlug) => app(BestelController::class)->t1Inloggen($zaak($subSlug), ''));
        Route::get('/account', fn (string $subSlug) => app(BestelController::class)->t1Account($zaak($subSlug), ''));
        Route::post('/inloggen', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1KlantLogin($zaak($subSlug), '', $request));
        Route::post('/registreren', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1KlantRegistreer($zaak($subSlug), '', $request));
        Route::post('/klant-uitloggen', fn (string $subSlug) => app(BestelController::class)->t1KlantUitloggen($zaak($subSlug), ''));
        Route::post('/klant/adressen', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1KlantAdressen($zaak($subSlug), $request));
        Route::get('/bestelling/status', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1Status($zaak($subSlug), $request));
        Route::get('/bestelling/data', fn (\Illuminate\Http\Request $request, string $subSlug) => app(BestelController::class)->t1OrderData($zaak($subSlug), $request));
        Route::get('/bestelling/{token}', fn (string $subSlug, string $token) => app(BestelController::class)->t1Bestelling($zaak($subSlug), '', $token));
        Route::get('/bestelling', fn (string $subSlug) => app(BestelController::class)->t1Bestelling($zaak($subSlug), '', null));
    });
}

Route::get('/', function () {
    return view('site.home');
})->name('home');

// De blog op de marketingsite, gevuld door de content-machine in het beheer
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::get('/sitemap.xml', [\App\Http\Controllers\BlogController::class, 'sitemap'])->name('sitemap');

// De onboarding is tegelijk de registratie
Route::get('/onboarding', function () {
    return view('onboarding');
})->name('onboarding');
Route::post('/onboarding/afronden', [OnboardingController::class, 'finish'])->name('onboarding.finish');
Route::post('/onboarding/email-check', [OnboardingController::class, 'emailCheck'])->name('onboarding.emailcheck');

// Het zaakscherm voor aan de muur: ondertekende link, geen login of sessie nodig
Route::get('/scherm', [AdminController::class, 'scherm'])->middleware('signed')->name('admin.scherm');

// Feedbackformulieren vanuit de mails: ondertekende links, geen login nodig
Route::middleware('signed')->group(function () {
    Route::get('/feedback/{user}/{context}', [\App\Http\Controllers\FeedbackController::class, 'formulier'])->name('feedback.formulier');
    Route::post('/feedback/{user}/{context}', [\App\Http\Controllers\FeedbackController::class, 'opslaan'])->name('feedback.opslaan');
    Route::get('/verlenging/{user}', [\App\Http\Controllers\FeedbackController::class, 'verlenging'])->name('feedback.verlenging');
    Route::post('/verlenging/{user}', [\App\Http\Controllers\FeedbackController::class, 'verleng'])->name('feedback.verleng');
});
Route::redirect('/registreren', '/onboarding');

// Speeltuinen: dezelfde code als de echte bestelpagina, gevuld met de demo-pizzeria.
// /template1 dwingt Presto af, /template2 dwingt Notte af, ongeacht het thema van de demo.
$demoPizzeria = fn () => \App\Models\User::where('email', 'demo@pizzeria.nl')->firstOrFail();
foreach (['template1', 'template2', 'template3', 'template4'] as $speeltuin) {
    Route::get("/$speeltuin", fn () => app(BestelController::class)->t1Home($demoPizzeria(), "/$speeltuin", $speeltuin));
    Route::get("/$speeltuin/afrekenen", fn () => app(BestelController::class)->t1Afrekenen($demoPizzeria(), "/$speeltuin", $speeltuin));
    Route::post("/$speeltuin/afrekenen", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1Plaats($demoPizzeria(), $request));
    Route::get("/$speeltuin/inloggen", fn () => app(BestelController::class)->t1Inloggen($demoPizzeria(), "/$speeltuin", $speeltuin));
    Route::get("/$speeltuin/account", fn () => app(BestelController::class)->t1Account($demoPizzeria(), "/$speeltuin", $speeltuin));
    Route::post("/$speeltuin/inloggen", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1KlantLogin($demoPizzeria(), "/$speeltuin", $request));
    Route::post("/$speeltuin/registreren", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1KlantRegistreer($demoPizzeria(), "/$speeltuin", $request));
    Route::post("/$speeltuin/klant-uitloggen", fn () => app(BestelController::class)->t1KlantUitloggen($demoPizzeria(), "/$speeltuin"));
    Route::post("/$speeltuin/klant/adressen", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1KlantAdressen($demoPizzeria(), $request));
    Route::get("/$speeltuin/bestelling/status", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1Status($demoPizzeria(), $request));
    Route::get("/$speeltuin/bestelling/data", fn (\Illuminate\Http\Request $request) => app(BestelController::class)->t1OrderData($demoPizzeria(), $request));
    Route::get("/$speeltuin/bestelling/{token}", fn (string $token) => app(BestelController::class)->t1Bestelling($demoPizzeria(), "/$speeltuin", $token, $speeltuin));
    Route::get("/$speeltuin/bestelling", fn () => app(BestelController::class)->t1Bestelling($demoPizzeria(), "/$speeltuin", null, $speeltuin));
}

// De publieke bestelpagina per pizzeria (straks op het eigen (sub)domein)
Route::prefix('/bestellen/{slug}')->group(function () {
    Route::get('/', [BestelController::class, 'home'])->name('bestel.home');
    Route::get('/menu', [BestelController::class, 'menu'])->name('bestel.menu');
    Route::get('/contact', [BestelController::class, 'contact'])->name('bestel.contact');
    Route::post('/plaatsen', [BestelController::class, 'plaats'])->name('bestel.plaats');

    // Template1 (Presto): afrekenen en de statuspagina van de klant
    Route::get('/afrekenen', [BestelController::class, 'afrekenen1'])->name('bestel.afrekenen');
    Route::post('/afrekenen', [BestelController::class, 'plaats1'])->name('bestel.plaats1');
    Route::get('/inloggen', [BestelController::class, 'inloggen1'])->name('bestel.inloggen');
    Route::get('/account', [BestelController::class, 'account1'])->name('bestel.account');
    Route::post('/inloggen', [BestelController::class, 'klantlogin1'])->name('bestel.klantlogin');
    Route::post('/registreren', [BestelController::class, 'klantregistreer1'])->name('bestel.klantregistreer');
    Route::post('/klant-uitloggen', [BestelController::class, 'klantuitloggen1'])->name('bestel.klantuitloggen');
    Route::post('/klant/adressen', [BestelController::class, 'klantadressen1'])->name('bestel.klantadressen');
    Route::get('/bestelling/status', [BestelController::class, 'status1'])->name('bestel.bestelstatus');
    Route::get('/bestelling/data', [BestelController::class, 'orderdata1'])->name('bestel.besteldata');
    Route::get('/bestelling/{token}', [BestelController::class, 'bestelling1'])->name('bestel.bestelling.token');
    Route::get('/bestelling', [BestelController::class, 'bestelling1'])->name('bestel.bestelling');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/wachtwoord-vergeten', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/wachtwoord-vergeten', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/wachtwoord-herstellen/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/wachtwoord-herstellen', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // Platformbeheer (alleen voor beheerders); het paneel staat in de url, wisselen is client-side
    Route::get('/admin/{paneel?}', [AdminController::class, 'index'])
        ->whereIn('paneel', ['overzicht', 'pizzerias', 'financieel', 'sales', 'blog'])
        ->name('admin.index');
    Route::get('/admin/pizzeria/{user}', [AdminController::class, 'pizzeria'])->name('admin.pizzeria');
    Route::post('/admin/notitie/{user}', [AdminController::class, 'notitie'])->name('admin.notitie');
    Route::post('/admin/verleng/{user}', [AdminController::class, 'verlengProef'])->name('admin.verleng');
    Route::post('/admin/inloggen-als/{user}', [AdminController::class, 'inloggenAls'])->name('admin.inloggenals');

    // De blog-machine: onderwerpen, schrijven, de review-gate en publiceren
    Route::post('/admin/blog/topic', [\App\Http\Controllers\BlogBeheerController::class, 'topicToevoegen'])->name('blog.topic');
    Route::delete('/admin/blog/topic/{topic}', [\App\Http\Controllers\BlogBeheerController::class, 'topicVerwijderen'])->name('blog.topic.weg');
    Route::post('/admin/blog/plan', [\App\Http\Controllers\BlogBeheerController::class, 'plan'])->name('blog.plan');
    Route::post('/admin/blog/schrijf', [\App\Http\Controllers\BlogBeheerController::class, 'schrijf'])->name('blog.schrijf');
    Route::get('/admin/blog/voorbeeld/{post}', [\App\Http\Controllers\BlogBeheerController::class, 'voorbeeld'])->name('blog.voorbeeld');
    Route::post('/admin/blog/hergate/{post}', [\App\Http\Controllers\BlogBeheerController::class, 'hergate'])->name('blog.hergate');
    Route::post('/admin/blog/publiceer/{post}', [\App\Http\Controllers\BlogBeheerController::class, 'publiceer'])->name('blog.publiceer');
    Route::post('/admin/blog/afwijzen/{post}', [\App\Http\Controllers\BlogBeheerController::class, 'afwijzen'])->name('blog.afwijzen');
    Route::post('/admin/blog/offline/{post}', [\App\Http\Controllers\BlogBeheerController::class, 'offline'])->name('blog.offline');

    // Het paneel staat in de url (/dashboard/bestellingen); de wissel zelf blijft client-side
    Route::get('/dashboard/{paneel?}', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        // Beheerders hebben geen eigen zaak: door naar het platformbeheer
        if ($user->is_admin) {
            return redirect()->route('admin.index');
        }

        // Accounts van voor het slug-veld krijgen er alsnog een
        if ($user->slug === null) {
            $user->forceFill(['slug' => BestelController::uniekeSlug($user->onboarding['name'] ?? null)])->save();
        }

        // Zonder actief abonnement rendert het dashboard met de abonnement-overlay
        // (zie dashboard/abonnement-overlay.blade.php); de actie-routes hieronder
        // weigeren dan ook server-side via de 'abonnement'-middleware.

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
                    'foto' => ($m['icon']['t'] ?? '') === 'p' ? ($m['icon']['v'] ?? null) : null,
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
    })->whereIn('paneel', ['overzicht', 'bestellingen', 'menukaart', 'webshop', 'instellingen'])->name('dashboard');

    // Abonnement via Stripe Checkout (test-mode); zonder Stripe-config activeert het direct
    Route::post('/abonnement/starten', [\App\Http\Controllers\AbonnementController::class, 'starten'])->name('abonnement.starten');
    Route::get('/abonnement/klaar', [\App\Http\Controllers\AbonnementController::class, 'klaar'])->name('abonnement.klaar');

    // Alles hieronder is pas bruikbaar met een actief abonnement (beheerders uitgezonderd)
    Route::middleware('abonnement')->group(function () {

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
            'spaarpunten' => ['sometimes', 'boolean'],
            'domainMode' => ['sometimes', 'in:sub,own'],
            'ownDomain' => ['sometimes', 'nullable', 'string', 'max:100'],
            'color' => ['sometimes', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:300000', 'regex:/^data:image\/(png|jpeg|webp);base64,/'],
            'theme' => ['sometimes', 'in:template1,template2,template3,template4'],
            'upsellPin' => ['sometimes', 'nullable', 'integer'],
        ]);

        // Eigen domein hosten kan alleen met een betaald abonnement, niet in de proefperiode
        if (($data['domainMode'] ?? null) === 'own' && ! $request->user()->abonnement_actief) {
            return response()->json(['errors' => ['domainMode' => ['Een eigen domein kan zodra je abonnement actief is.']]], 422);
        }

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
            'tiers.*.min' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50000'],
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

    }); // einde abonnement-middleware

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
