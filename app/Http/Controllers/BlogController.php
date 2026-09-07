<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\View\View;

/** De publieke blogpagina's op de marketingsite, plus de sitemap */
class BlogController extends Controller
{
    public function index(): View
    {
        return view('site.blog.index', [
            'posts' => Post::published()->orderByDesc('published_at')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $verwant = Post::published()->whereKeyNot($post->id)
            ->where('cluster', $post->cluster)
            ->orderByDesc('published_at')->limit(3)->get();

        return view('site.blog.show', ['post' => $post, 'verwant' => $verwant, 'preview' => false]);
    }

    public function sitemap(): Response
    {
        $site = rtrim(config('seo-content.factsheet.site'), '/');
        $urls = collect(array_keys(config('seo-content.internal_pages')))
            ->map(fn ($pad) => ['loc' => $site . ($pad === '/' ? '/' : $pad), 'lastmod' => null]);

        $posts = Post::published()->get();
        if ($posts->isNotEmpty()) {
            $urls->push(['loc' => $site . '/blog', 'lastmod' => $posts->max('published_at')?->toDateString()]);
            foreach ($posts as $post) {
                $urls->push(['loc' => $site . '/blog/' . $post->slug, 'lastmod' => ($post->last_refreshed_at ?? $post->published_at)?->toDateString()]);
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . e($url['loc']) . '</loc>' . ($url['lastmod'] ? '<lastmod>' . $url['lastmod'] . '</lastmod>' : '') . "</url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
