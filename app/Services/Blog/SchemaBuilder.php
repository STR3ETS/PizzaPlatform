<?php

namespace App\Services\Blog;

use App\Models\Post;

/**
 * JSON-LD voor blogposts: Article, FAQPage (indien FAQ aanwezig),
 * BreadcrumbList, Organization en Person/Team (E-E-A-T).
 */
class SchemaBuilder
{
    public function voorPost(Post $post): array
    {
        $site = rtrim(config('seo-content.factsheet.site'), '/');
        $auteur = config('seo-content.author');
        $url = $site . '/blog/' . $post->slug;

        $persoon = array_filter([
            '@type' => 'Organization',
            'name' => $auteur['naam'],
            'description' => $auteur['bio'],
            'url' => $auteur['url'],
            'sameAs' => $auteur['profielen'] ?: null,
        ]);

        $organisatie = [
            '@type' => 'Organization',
            'name' => config('seo-content.factsheet.merk'),
            'url' => $site . '/',
        ];

        $blokken = [[
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => (string) ($post->meta_description ?? ''),
            'url' => $url,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => ($post->last_refreshed_at ?? $post->updated_at)?->toIso8601String(),
            'inLanguage' => 'nl',
            'author' => $persoon,
            'publisher' => $organisatie,
            'image' => $post->cover ? $site . $post->cover : null,
            'mainEntityOfPage' => $url,
        ], [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $site . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $site . '/blog'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $url],
            ],
        ]];

        $faq = $this->faqUitHtml((string) ($post->content ?? ''));
        if ($faq) {
            $blokken[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faq,
            ];
        }

        return $blokken;
    }

    /** Haalt vraag/antwoord-paren uit de FAQ-sectie (h3 = vraag, volgende p = antwoord). */
    private function faqUitHtml(string $html): array
    {
        if ($html === '' || ! str_contains(mb_strtolower($html), 'veelgestelde vragen')) {
            return [];
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>');
        libxml_clear_errors();

        $items = [];
        $inFaq = false;
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//h2|//h3|//p') as $node) {
            if ($node->nodeName === 'h2') {
                $inFaq = str_contains(mb_strtolower($node->textContent), 'veelgestelde vragen');
                continue;
            }
            if ($inFaq && $node->nodeName === 'h3') {
                $vraag = trim($node->textContent);
                $antwoord = '';
                $volgende = $node->nextSibling;
                while ($volgende && $volgende->nodeName !== 'p') {
                    $volgende = $volgende->nextSibling;
                }
                if ($volgende) {
                    $antwoord = trim($volgende->textContent);
                }
                if ($vraag !== '' && $antwoord !== '') {
                    $items[] = [
                        '@type' => 'Question',
                        'name' => $vraag,
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $antwoord],
                    ];
                }
            }
        }

        return $items;
    }
}
