<?php

namespace App\Services\Blog;

/**
 * Briefing -> outline -> secties -> assemblage -> validatie.
 * Alles op één sterk model; de outline-regels en schrijfverboden uit de
 * brand-kit staan letterlijk in de prompts (dure lessen, zie config).
 */
class ContentPipeline
{
    public function __construct(
        private LlmClient $llm,
        private InternalLinkScanner $linkScanner,
    ) {
    }

    /* ---------- Stap 1: briefing ---------- */

    public function makeBriefing(string $keyword, string $cluster): array
    {
        /* Echte interne-link-slots: alleen pagina's die bestaan */
        $linkSlots = collect(config('seo-content.internal_pages'))
            ->map(fn ($omschrijving, $pad) => ['pad' => $pad, 'omschrijving' => $omschrijving])
            ->values()->all();

        return [
            'keyword' => $keyword,
            'cluster' => $cluster,
            'factsheet' => config('seo-content.factsheet'),
            'terminology' => config('seo-content.terminology'),
            'writing' => config('seo-content.writing'),
            'link_slots' => $linkSlots,
        ];
    }

    /* ---------- Stap 2: outline ---------- */

    public function aiOutline(array $briefing, int $doelWoorden): array
    {
        $systeem = $this->basisSysteem($briefing)
            . "\nJe maakt een artikel-outline. REGELS:"
            . "\n- Pas het aantal secties aan op de breedte van het onderwerp: een smal onderwerp krijgt 2 tot 4 H2's, een breed onderwerp maximaal 6."
            . "\n- GEEN samenvattende sectie, conclusie-sectie of checklist-sectie die eerdere tips herhaalt."
            . "\n- Elke H2 behandelt een eigen deelonderwerp; geen overlap tussen secties."
            . "\n- Blijf strikt bij het onderwerp; geen zijpaden."
            . "\n- Maximaal 4 FAQ-vragen, alleen vragen die het artikel nog niet beantwoordt.";

        $prompt = 'Onderwerp/keyword: "' . $briefing['keyword'] . '" (cluster: ' . $briefing['cluster'] . ').'
            . "\nDoellengte van het volledige artikel: circa {$doelWoorden} woorden."
            . "\nGeef JSON: {\"h1\": string, \"meta_title\": string (max 60 tekens), \"meta_description\": string (70-155 tekens), \"secties\": [{\"kop\": string, \"kern\": string (1 zin: wat deze sectie behandelt)}], \"faq\": [{\"vraag\": string}]}";

        return $this->llm->json($systeem, $prompt);
    }

    /* ---------- Stap 3: per sectie schrijven ---------- */

    public function writeSection(array $briefing, array $sectie, string $eerdereKoppen): string
    {
        $systeem = $this->basisSysteem($briefing)
            . "\nJe schrijft één artikelsectie in nette HTML (<h2>, <p>, eventueel <ul>/<li> of <h3>). VERBODEN:"
            . "\n- Opvulling, herhaling van andere secties of van de titel."
            . "\n- Keyword-gestufte koppen of de artikeltitel echoën in de kop."
            . "\n- Verwijzen naar pagina's die niet in de link-slots staan."
            . "\n- Prijzen noemen die niet LETTERLIJK in de factsheet staan."
            . "\n- Verzonnen reviews, statistieken of onderzoeken."
            . "\n- De sectie afsluiten met een call-to-action- of verwijszin (zoals \"Benieuwd hoe dat eruitziet? Bekijk...\"): de CTA staat één keer onderaan het artikel, niet per sectie."
            . "\nLinks mogen alleen naar de link-slots, als gewone <a href=\"/pad\">ankertekst</a>, verwerkt in een lopende zin, en alleen waar het de lezer echt helpt. Maximaal één interne link per sectie.";

        $prompt = 'Artikel over: "' . $briefing['keyword'] . "\".\nSectiekop: \"" . ($sectie['kop'] ?? '') . "\"\nKern: " . ($sectie['kern'] ?? '')
            . "\nReeds geschreven koppen (niet herhalen): " . ($eerdereKoppen ?: 'nog geen')
            . "\nSchrijf 150 tot 280 woorden voor deze sectie. Begin met <h2>" . ($sectie['kop'] ?? '') . '</h2>.';

        return trim($this->llm->tekst($systeem, $prompt));
    }

    public function schrijfIntro(array $briefing, array $outline): string
    {
        $systeem = $this->basisSysteem($briefing)
            . "\nJe schrijft de intro van een artikel: 2 korte alinea's (<p>), samen 80-140 woorden. Geen kop, geen samenvatting van alle secties, wel meteen de kern en waarom het de lezer raakt.";

        return trim($this->llm->tekst($systeem, 'Artikel: "' . ($outline['h1'] ?? $briefing['keyword']) . '". Secties: ' . collect($outline['secties'] ?? [])->pluck('kop')->implode(', ') . '.'));
    }

    public function beantwoordFaq(array $briefing, string $vraag): string
    {
        $systeem = $this->basisSysteem($briefing)
            . "\nJe beantwoordt één FAQ-vraag in 40-90 woorden, één <p>. Alleen feiten uit de factsheet of algemeen aanvaarde vakkennis.";

        return trim($this->llm->tekst($systeem, 'Vraag: ' . $vraag));
    }

    /* ---------- Stap 4: assemblage ---------- */

    public function assembleWithCtas(string $h1, string $intro, array $secties, array $faq): string
    {
        /* Dedup near-identieke H2's deterministisch */
        $gezien = [];
        $schoon = [];
        foreach ($secties as $sectieHtml) {
            preg_match('/<h2[^>]*>(.*?)<\/h2>/is', $sectieHtml, $m);
            $kop = $this->normaliseerKop($m[1] ?? '');
            if ($kop !== '' && isset($gezien[$kop])) {
                continue;
            }
            $gezien[$kop] = true;
            $schoon[] = $sectieHtml;
        }

        $html = $intro . "\n" . implode("\n", $schoon);

        /* Eén FAQ-blok, nooit dubbel */
        if ($faq && ! str_contains(mb_strtolower($html), 'veelgestelde vragen')) {
            $html .= "\n<h2>Veelgestelde vragen</h2>";
            foreach ($faq as $item) {
                $html .= "\n<h3>" . e($item['vraag'] ?? '') . '</h3>' . "\n" . ($item['antwoord'] ?? '');
            }
        }

        /* Precies één CTA, altijd als afsluiter */
        $cta = config('seo-content.cta');
        $html .= "\n" . '<div class="artikel-cta"><h2>' . e($cta['titel']) . '</h2><p>' . e($cta['tekst']) . '</p><p><a href="' . e($cta['knop_url']) . '">' . e($cta['knop_label']) . '</a></p></div>';

        return $html;
    }

    /* ---------- Stap 5: validatie + reparatie ---------- */

    public function runValidation(string $html, string $keyword, string $metaDescription): array
    {
        $issues = [];

        $woorden = str_word_count(strip_tags($html));
        $vloer = (int) config('seo-content.writing.lengte_vloer');
        if ($woorden < $vloer) {
            $issues[] = "Artikel is te kort ({$woorden} woorden, vloer is {$vloer}).";
        }

        /* Koppen-volgorde: h3 mag nooit vóór de eerste h2 komen */
        preg_match_all('/<(h[23])[^>]*>/i', $html, $koppen);
        $eersteKop = $koppen[1][0] ?? null;
        if ($eersteKop !== null && strtolower($eersteKop) === 'h3') {
            $issues[] = 'Koppenstructuur klopt niet: een H3 komt vóór de eerste H2.';
        }

        $kapot = $this->linkScanner->kapotteLinks($html);
        foreach ($kapot as $link) {
            $issues[] = "Interne link bestaat niet: {$link}";
        }

        if (str_contains($html, '—')) {
            $issues[] = 'Bevat em-dashes; die zijn verboden in de huisstijl.';
        }

        if (preg_match('/\[[^\]]+\]\([^)]+\)/', $html)) {
            $issues[] = 'Bevat Markdown-links ([tekst](url)); links moeten HTML <a>-tags zijn.';
        }

        $lengte = mb_strlen($metaDescription);
        if ($lengte > 0 && ($lengte < 70 || $lengte > 165)) {
            $issues[] = "Meta description heeft afwijkende lengte ({$lengte} tekens).";
        }

        return [
            'woorden' => $woorden,
            'h2_count' => substr_count(strtolower($html), '<h2'),
            'kapotte_links' => $kapot,
            'keyword_in_tekst' => str_contains(mb_strtolower($html), mb_strtolower($keyword)),
            'issues' => $issues,
        ];
    }

    public function aiRefineHtml(string $html, array $issues): string
    {
        $systeem = 'Je repareert HTML van een Nederlands blogartikel. Los UITSLUITEND de genoemde problemen op; verander verder niets aan inhoud, toon of structuur. Verwijder links die niet bestaan (laat de ankertekst als gewone tekst staan). Vervang em-dashes door een komma of dubbele punt. Antwoord met alleen de volledige gerepareerde HTML.';

        return trim($this->llm->tekst($systeem, "Problemen:\n- " . implode("\n- ", $issues) . "\n\nHTML:\n" . $html, 16000));
    }

    /* ---------- Hulpfuncties ---------- */

    private function basisSysteem(array $briefing): string
    {
        $fact = $briefing['factsheet'];

        return 'Je schrijft voor de site van ' . $fact['merk'] . ' (' . $fact['site'] . '). '
            . $fact['omschrijving']
            . "\nTaal en toon: " . $briefing['writing']['taal'] . '.'
            . "\nFACTSHEET (de enige toegestane feiten over het merk):"
            . "\nPrijzen:\n- " . implode("\n- ", $fact['prijzen'])
            . "\nFeatures:\n- " . implode("\n- ", $fact['features'])
            . "\nVERBODEN:\n- " . implode("\n- ", $fact['verboden_claims'])
            . "\n- Verboden woorden/stijl: " . implode(', ', $briefing['terminology']['verboden'])
            . "\nBeschikbare interne link-slots (de ENIGE toegestane interne links):\n"
            . collect($briefing['link_slots'])->map(fn ($s) => '- ' . $s['pad'] . ' (' . $s['omschrijving'] . ')')->implode("\n")
            . "\nAlgemene vakkennis over horeca, marketing en online bestellen mag je gebruiken; merkuitspraken alleen uit de factsheet.";
    }

    private function normaliseerKop(string $kop): string
    {
        return preg_replace('/[^a-z0-9]+/', '', mb_strtolower(strip_tags($kop)));
    }
}
