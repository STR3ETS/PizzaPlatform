<?php

namespace App\Services\Blog;

use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Weeft bij publicatie interne links tussen topisch-verwante posts.
 * Min/max per post komt uit de brand-kit; onder het minimum wordt een
 * "Verder lezen"-blok toegevoegd met echte verwante posts.
 */
class SemanticMeshService
{
    public function weave(Post $post): void
    {
        $max = (int) config('seo-content.mesh.max_links');
        $min = (int) config('seo-content.mesh.min_links');

        $verwant = $this->verwanteBerichten($post, $max);
        if ($verwant->isEmpty()) {
            return;
        }

        $html = (string) $post->content;
        $gelegd = 0;

        foreach ($verwant as $ander) {
            if ($gelegd >= $max) {
                break;
            }
            $doel = '/blog/' . $ander->slug;
            if (str_contains($html, 'href="' . $doel . '"')) {
                $gelegd++;
                continue;
            }
            /* Anker: eerste voorkomen van het keyword van de andere post in een alinea */
            $patroon = '/(<p>(?:(?!<\/p>).)*?)(' . preg_quote(e($ander->target_keyword), '/') . ')/isu';
            $nieuw = preg_replace($patroon, '$1<a href="' . $doel . '">$2</a>', $html, 1, $aantal);
            if ($aantal > 0) {
                $html = $nieuw;
                $gelegd++;
            }
        }

        /* Onder het minimum: expliciet "Verder lezen"-blok vóór de CTA */
        if ($gelegd < $min) {
            $lijst = $verwant->take($min)->map(fn ($p) => '<li><a href="/blog/' . e($p->slug) . '">' . e($p->title) . '</a></li>')->implode('');
            $blok = '<h2>Verder lezen</h2><ul>' . $lijst . '</ul>';
            $html = str_contains($html, '<div class="artikel-cta">')
                ? Str::replaceFirst('<div class="artikel-cta">', $blok . '<div class="artikel-cta">', $html)
                : $html . $blok;
        }

        $post->update(['content' => $html]);

        /* Terugweven: verwante posts krijgen waar mogelijk een link naar deze post */
        foreach ($verwant as $ander) {
            $this->legTerugLink($ander, $post, $max);
        }
    }

    private function verwanteBerichten(Post $post, int $limiet)
    {
        $woorden = collect(explode(' ', mb_strtolower($post->target_keyword ?? '')))
            ->filter(fn ($w) => mb_strlen($w) > 3)->values();

        return Post::published()->whereKeyNot($post->id)->get()
            ->map(function ($ander) use ($post, $woorden) {
                $score = $ander->cluster === $post->cluster ? 3 : 0;
                foreach ($woorden as $w) {
                    if (str_contains(mb_strtolower($ander->target_keyword . ' ' . $ander->title), $w)) {
                        $score++;
                    }
                }
                $ander->mesh_score = $score;

                return $ander;
            })
            ->filter(fn ($ander) => $ander->mesh_score > 0)
            ->sortByDesc('mesh_score')
            ->take($limiet)
            ->values();
    }

    private function legTerugLink(Post $bron, Post $doelPost, int $max): void
    {
        $html = (string) $bron->content;
        $doel = '/blog/' . $doelPost->slug;
        if (str_contains($html, 'href="' . $doel . '"') || substr_count($html, 'href="/blog/') >= $max) {
            return;
        }
        $patroon = '/(<p>(?:(?!<\/p>).)*?)(' . preg_quote(e($doelPost->target_keyword), '/') . ')/isu';
        $nieuw = preg_replace($patroon, '$1<a href="' . $doel . '">$2</a>', $html, 1, $aantal);
        if ($aantal > 0) {
            $bron->update(['content' => $nieuw]);
        }
    }
}
