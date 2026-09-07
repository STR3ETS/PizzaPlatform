<?php

namespace App\Services\Blog;

use App\Models\Post;

/**
 * Controleert of interne links in gegenereerde content ECHT bestaan:
 * brand-kit internal_pages plus gepubliceerde blogposts.
 */
class InternalLinkScanner
{
    /** @return string[] alle geldige interne paden */
    public function geldigePaden(): array
    {
        $paden = array_keys(config('seo-content.internal_pages'));
        $paden[] = '/blog';
        foreach (Post::published()->pluck('slug') as $slug) {
            $paden[] = '/blog/' . $slug;
        }

        return $paden;
    }

    /** @return string[] interne hrefs uit de html die NIET bestaan */
    public function kapotteLinks(string $html): array
    {
        preg_match_all('/href="([^"]+)"/i', $html, $m);
        $geldig = $this->geldigePaden();
        $kapot = [];

        foreach (array_unique($m[1] ?? []) as $href) {
            if (preg_match('/^(https?:)?\/\//i', $href) || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:') || str_starts_with($href, '#')) {
                continue; // externe of niet-pagina links vallen buiten deze check
            }
            $pad = '/' . ltrim(strtok($href, '#?'), '/');
            $pad = $pad === '/' ? '/' : rtrim($pad, '/');
            if (! in_array($pad, $geldig, true)) {
                $kapot[] = $href;
            }
        }

        return $kapot;
    }
}
