{{-- Bestelbevestiging volledig in het thema van de zaak. Elk template heeft
     een eigen mail-layout die de opbouw van de bestelpagina spiegelt:
     Presto losse ronde kaarten, Notte een doorlopend menukaart-vel,
     Forza aaneengesloten volle banden, Giro de luchtige witte app-look.
     Bewust nergens MijnPizzeria-kleuren of -branding. --}}
@php
    $ob = $pizzeria->onboarding ?? [];
    $zaakNaam = $ob['name'] ?? 'de pizzeria';
    $palet = \App\Http\Controllers\BestelController::paletVoor($ob['color'] ?? null);
    $primair = $palet['primair'];
    $tekst = $palet['secundair'];
    $gedempt = $palet['secundairLicht'];
    $achtergrond = $palet['witWarm'];
    $extraNamen = ['Bezorgkosten', 'Fooi bezorger', 'Spaarpunten korting'];
    $gerechten = collect($order->items)->filter(fn ($i) => ! in_array($i['naam'], $extraNamen, true));
    $extras = collect($order->items)->filter(fn ($i) => in_array($i['naam'], $extraNamen, true));
    $regelPrijs = fn ($i) => ($i['prijs'] + collect($i['opties'] ?? [])->sum('prijs')) * $i['aantal'];
    $euro = fn ($c) => '€ ' . number_format($c / 100, 2, ',', '.');

    // Het volledige kostenoverzicht: subtotaal, korting, bezorgkosten (ook als
    // ze gratis zijn) en fooi staan er altijd expliciet in
    $vindExtra = fn (string $naam) => $extras->firstWhere('naam', $naam);
    $kostenRegels = [['Subtotaal', $euro($gerechten->sum(fn ($i) => $regelPrijs($i))), false]];
    if ($kortingRegel = $vindExtra('Spaarpunten korting')) {
        $kostenRegels[] = ['Spaarpunten korting', '- ' . $euro(abs($kortingRegel['prijs'])), true];
    }
    if ($order->type === 'bezorgen') {
        $bezorgRegel = $vindExtra('Bezorgkosten');
        $kostenRegels[] = ['Bezorgkosten', $bezorgRegel ? $euro($bezorgRegel['prijs']) : 'Gratis', false];
    }
    if ($fooiRegel = $vindExtra('Fooi bezorger')) {
        $kostenRegels[] = ['Fooi bezorger', $euro($fooiRegel['prijs']), false];
    }

    // FontAwesome-iconen, als PNG ingesloten in de mail (icon-fonts werken niet in mailclients)
    $stappen = [
        ['receipt', 'Ontvangen', true],
        ['kitchen-set', 'Bereiden', false],
        ['fire', 'In de oven', false],
        [$order->type === 'bezorgen' ? 'moped' : 'bag-shopping', $order->type === 'bezorgen' ? 'Onderweg' : 'Klaar om af te halen', false],
    ];
    $icoon = fn (string $naam, bool $wit) => $message->embed(public_path('assets/mail/' . $naam . ($wit ? '-wit' : '-grijs') . '.png'));

    $bevestigingRegels = array_filter([
        ['Bestelnummer', '#' . $order->nummer],
        ['Geplaatst op', $order->created_at->locale('nl')->isoFormat('D MMMM YYYY, HH:mm')],
        ['Naam', $order->klant],
        [$order->type === 'bezorgen' ? 'Bezorgadres' : 'Afhalen bij', $order->type === 'bezorgen' ? $order->adres : $zaakNaam . (($ob['street'] ?? '') !== '' ? ', ' . $ob['street'] . (($ob['city'] ?? '') !== '' ? ' in ' . $ob['city'] : '') : '')],
        ($ob['phone'] ?? '') !== '' ? ['Telefoon zaak', $ob['phone']] : null,
        $order->opmerking ? ['Opmerking', $order->opmerking] : null,
    ]);

    // Fonts per template; clients zonder webfont-steun vallen terug op het systeemfont
    $themaStijlen = [
        'template1' => ['kop' => "'Inter Tight', 'Trebuchet MS', Arial, sans-serif", 'body' => "'Inter Tight', 'Trebuchet MS', Arial, sans-serif", 'import' => 'Inter+Tight:wght@400;700;800', 'variant' => 'presto'],
        'template2' => ['kop' => "'Fraunces', Georgia, serif", 'body' => "'DM Sans', 'Trebuchet MS', Arial, sans-serif", 'import' => 'DM+Sans:wght@400;700&family=Fraunces:wght@600;700', 'variant' => 'notte'],
        'template3' => ['kop' => "'Anton', 'Arial Black', Arial, sans-serif", 'body' => "'Archivo', Arial, sans-serif", 'import' => 'Anton&family=Archivo:wght@400;700;800', 'variant' => 'forza'],
        'template4' => ['kop' => "'Plus Jakarta Sans', 'Trebuchet MS', Arial, sans-serif", 'body' => "'Plus Jakarta Sans', 'Trebuchet MS', Arial, sans-serif", 'import' => 'Plus+Jakarta+Sans:wght@400;700;800', 'variant' => 'giro'],
    ];
    $stijl = $themaStijlen[$ob['theme'] ?? 'template1'] ?? $themaStijlen['template1'];
    $kopFont = $stijl['kop'];
    $bodyFont = $stijl['body'];
    $fontImport = $stijl['import'];
    $voetTekst = 'Vragen over je bestelling? ' . (($ob['phone'] ?? '') !== '' ? 'Bel ' . $zaakNaam . ' via ' . $ob['phone'] . '.' : 'Neem contact op met ' . $zaakNaam . '.');
@endphp
@include('mail.bestel.' . $stijl['variant'])
