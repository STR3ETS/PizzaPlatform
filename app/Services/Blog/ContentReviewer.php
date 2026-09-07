<?php

namespace App\Services\Blog;

use App\Models\Post;

/**
 * De AI-review-gate vóór publicatie, GEGROND met de factsheet.
 * Blokkeert verzinsels; laat legitieme marktuitspraken en échte
 * productfeiten expliciet door (carve-outs tegen vals alarm).
 */
class ContentReviewer
{
    public function __construct(private LlmClient $llm, private InternalLinkScanner $linkScanner)
    {
    }

    /** @return array{verdict: string, reasons: string[]} */
    public function review(Post $post): array
    {
        $fact = config('seo-content.factsheet');

        $auteur = config('seo-content.author');
        $systeem = 'Je bent een strenge maar eerlijke eindredacteur voor ' . $fact['merk'] . '.'
            . "\nFACTSHEET (waarheid over het merk):"
            . "\nOver het merk: " . $fact['omschrijving']
            . "\nMaker/auteur: " . $auteur['naam'] . ' (' . $auteur['functie'] . '). ' . $auteur['bio']
            . "\nPrijzen:\n- " . implode("\n- ", $fact['prijzen'])
            . "\nFeatures:\n- " . implode("\n- ", $fact['features'])
            . "\nEchte interne pagina's: " . implode(', ', $this->linkScanner->geldigePaden())
            . "\n\nBLOKKEER het artikel bij:"
            . "\n- Verzonnen statistieken, onderzoeken, reviews, testimonials of awards."
            . "\n- Prijzen die afwijken van de factsheet."
            . "\n- Features, diensten of pagina's die niet bestaan."
            . "\n- Duidelijke herhaling tussen secties of interne tegenspraak."
            . "\n- Garanties over Google-posities of omzetgroei."
            . "\n\nLAAT EXPLICIET DOOR (geen reden om te blokkeren):"
            . "\n- Algemene, redelijke marktuitspraken zonder cijfers (\"veel pizzeria's merken...\", \"een eigen bestelpagina helpt bij...\")."
            . "\n- Het feit dat bezorgplatforms als Thuisbezorgd commissie per bestelling rekenen (algemeen bekend), zolang er geen verzonnen percentages of bedragen staan."
            . "\n- Feiten die letterlijk of parafraserend uit de factsheet komen."
            . "\n- Adviezen en vakkennis over horeca, marketing en online bestellen."
            . "\n- Stijlkwesties en smaakverschillen; daar gaat deze gate niet over.";

        $prompt = "Beoordeel dit artikel.\nTitel: " . $post->title
            . "\nMeta description: " . ($post->meta_description ?? '')
            . "\n\nHTML:\n" . mb_substr((string) $post->content, 0, 30000)
            . "\n\nGeef JSON: {\"verdict\": \"pass\"|\"fail\", \"reasons\": [string, ...]}. Bij pass mag reasons leeg zijn of positieve opmerkingen bevatten. Bij fail: concrete, aanwijsbare redenen met citaat of sectiekop.";

        $resultaat = $this->llm->json($systeem, $prompt);

        return [
            'verdict' => ($resultaat['verdict'] ?? 'fail') === 'pass' ? 'pass' : 'fail',
            'reasons' => array_values(array_filter(array_map('strval', $resultaat['reasons'] ?? []))),
        ];
    }
}
