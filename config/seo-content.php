<?php

/*
 * Brand-kit voor de blog-machine (afgekeken van HalfmanMediaV100).
 * Dit is het ENIGE bestand dat je per merk aanpast; alle services en
 * prompts lezen hieruit.
 */
return [

    /* ---------- Modellen & API's ---------- */
    'llm' => [
        'provider' => env('SEO_LLM_PROVIDER', 'anthropic'), // anthropic | openai
        'key' => env('SEO_LLM_KEY', ''),
        'model' => env('SEO_LLM_MODEL', 'claude-sonnet-5'), // sterk model voor outline, schrijven EN review
        'max_tokens' => 8192, // ruim genoeg voor denkstappen plus het antwoord zelf
    ],
    'pexels_key' => env('SEO_PEXELS_KEY', ''), // gratis via pexels.com/api; zonder key zoekt hij op Openverse of genereert hij zelf een cover

    /* ---------- Guardrails ---------- */
    'review_queue_cap' => 12,  // drafter slaat over zodra er zoveel onbeoordeelde concepten staan
    'mesh' => [
        'min_links' => 2,      // minimaal aantal interne links per gepubliceerde post
        'max_links' => 5,
    ],
    'writing' => [
        'taal' => 'Nederlands (je-vorm, nuchter, persoonlijk, geen jargon zonder uitleg). Doelgroep: eigenaren van pizzeria\'s en kleine horecazaken in Nederland.',
        'lengte_vloer' => 900, // minimaal aantal woorden per artikel
        'doel_woorden' => 1400,
    ],

    /* ---------- Factsheet: de enige waarheid over het merk ---------- */
    'factsheet' => [
        'merk' => 'MijnPizzeria',
        'site' => 'https://mijnpizzeria.nl',
        'omschrijving' => 'Platform waarmee pizzeria\'s in tien minuten een eigen online bestelpagina hebben op zaaknaam.mijnpizzeria.nl. Zonder commissie per bestelling: klanten betalen rechtstreeks aan de zaak.',
        'prijzen' => [
            'Abonnement: 24,95 euro per maand, maandelijks opzegbaar',
            'Eerste 30 dagen gratis uitproberen, zonder betaalgegevens vooraf',
            'Geen commissie per bestelling: de omzet gaat volledig naar de pizzeria',
        ],
        'features' => [
            'Eigen bestelpagina op zaaknaam.mijnpizzeria.nl, eigen domein mogelijk met een actief abonnement',
            'Vier ontwerpstijlen (templates) met eigen kleur en logo',
            'Menukaart zelf samenstellen en aanpassen, zonder technische kennis',
            'Bestellingen beheren op een overzichtelijk dashboard, met live status voor de klant',
            'Spaarprogramma: klanten sparen punten op hun bestellingen voor korting',
            'Bezorgen en afhalen, met bezorgkosten per afstand',
            'Klanten betalen veilig online via Stripe (iDEAL, Apple Pay, Google Pay)',
        ],
        'verboden_claims' => [
            'Geen verzonnen reviews, testimonials, awards of klantcitaten',
            'Geen verzonnen statistieken of onderzoekscijfers zonder bron',
            'Geen garanties over Google-posities of omzetgroei ("gegarandeerd meer bestellingen")',
            'Geen andere prijzen dan de factsheet-prijzen',
            'Geen features of pagina\'s noemen die niet bestaan',
            'Geen superlatieven als "goedkoopste" of "beste van Nederland"',
            'Geen negatieve uitspraken over concrete concurrenten bij naam (Thuisbezorgd en Uber Eats mogen wel feitelijk genoemd worden als bezorgplatforms met commissie)',
        ],
    ],

    /* ---------- Terminologie ---------- */
    'terminology' => [
        'voorkeur' => ['bestelpagina', 'spaarprogramma', 'menukaart', 'zonder commissie', 'proefmaand', 'bezorgplatform'],
        'verboden' => ['em-dash (het teken —, gebruik een komma of dubbele punt)', 'gamechanger', 'naadloos', 'state-of-the-art', 'unlock', 'boost'],
    ],

    /* ---------- Topic-clusters + brede seed-onderwerpen ---------- */
    'clusters' => [
        'Online bestellen zonder commissie' => [
            'Wat kost een bezorgplatform je pizzeria echt: commissie doorgerekend',
            'Een eigen bestelpagina voor je pizzeria: wat heb je nodig',
            'Van telefonisch bestellen naar online: zo maak je de overstap',
            'Eigen bestelsite of bezorgplatform: wat past bij jouw zaak',
        ],
        'Meer bestellingen & marketing' => [
            'Zo krijg je klanten die via Thuisbezorgd bestellen naar je eigen bestelpagina',
            'Lokale marketing voor je pizzeria: gevonden worden in je eigen buurt',
            'Social media voor pizzeria\'s: wat werkt echt en wat kost alleen tijd',
            'Je menukaart als verkoopmachine: prijzen, foto\'s en volgorde',
        ],
        'Klantenbinding & spaarprogramma' => [
            'Vaste klanten zijn goud waard: klantenbinding voor pizzeria\'s',
            'Een spaarprogramma voor je pizzeria: zo werkt het en dit levert het op',
        ],
        'Je zaak runnen' => [
            'Bezorgen of alleen afhalen: de afweging voor een kleine pizzeria',
            'Piekdrukte in de pizzeria: zo houd je de vrijdagavond werkbaar',
            'Bezorgkosten berekenen: wat is redelijk voor jouw bezorggebied',
        ],
        'Online zichtbaarheid' => [
            'Google Bedrijfsprofiel voor je pizzeria: de basis goed neerzetten',
            'Reviews verzamelen en beantwoorden: zo bouw je online vertrouwen op',
        ],
    ],

    /* ---------- Echte, linkbare interne pagina's ---------- */
    'internal_pages' => [
        '/' => 'Homepagina van MijnPizzeria',
        '/onboarding' => 'Gratis starten: registratie en het opzetten van je bestelpagina',
    ],

    /* ---------- Auteur (E-E-A-T) ---------- */
    'author' => [
        'naam' => 'Team MijnPizzeria',
        'functie' => 'Makers van MijnPizzeria',
        'bio' => 'Het team achter MijnPizzeria bouwt bestelpagina\'s zonder commissie voor pizzeria\'s in Nederland en schrijft over online bestellen, marketing en het runnen van een zaak.',
        'url' => 'https://mijnpizzeria.nl',
        'foto' => null,
        'profielen' => [],
    ],

    /* ---------- Cover-zoektermen per cluster (Engels: stocksites zoeken slecht in het Nederlands) ---------- */
    'cover_queries' => [
        'Online bestellen zonder commissie' => ['pizza box delivery', 'ordering food phone', 'pizza restaurant counter'],
        'Meer bestellingen & marketing' => ['pizzeria storefront', 'pizza margherita wood fire', 'restaurant owner smartphone'],
        'Klantenbinding & spaarprogramma' => ['happy customers pizza', 'pizza slice friends sharing', 'loyalty card coffee shop'],
        'Je zaak runnen' => ['pizza chef kitchen', 'pizza oven fire', 'kneading pizza dough'],
        'Online zichtbaarheid' => ['searching maps smartphone', 'pizza restaurant terrace', 'laptop cafe review'],
    ],

    /* ---------- CTA onderaan elk artikel ---------- */
    'cta' => [
        'titel' => 'Klaar voor je eigen bestelpagina?',
        'tekst' => 'Zet je pizzeria in tien minuten online met een eigen bestelpagina, zonder commissie per bestelling. Probeer het 30 dagen gratis.',
        'knop_label' => 'Gratis starten',
        'knop_url' => '/onboarding',
    ],
];
