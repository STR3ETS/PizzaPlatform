<?php

namespace App\Services\Blog;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Kiest per artikel een passende cover. Met een Pexels-key zoekt hij een
 * topische stockfoto; zonder key probeert hij Openverse (rechtenvrij, geen
 * key nodig) en anders genereert hij zelf een cover in de huisstijl.
 */
class CoverService
{
    public function __construct(private LlmClient $llm)
    {
    }

    /** @return array{pad: ?string, credit: ?string, bron: ?string} */
    public function kies(string $slug, string $cluster, int $variatie, string $titel = ''): array
    {
        if ($pexels = $this->vanPexels($slug, $cluster, $variatie, $titel)) {
            return $pexels;
        }
        if ($openverse = $this->vanOpenverse($slug, $cluster, $variatie, $titel)) {
            return $openverse;
        }

        return ['pad' => $this->genereer($slug), 'credit' => null, 'bron' => null];
    }

    /**
     * Alles wat al in gebruik is: bron-URL's uit de posts en inhoud-hashes van
     * de bestaande coverbestanden. Zo komt dezelfde foto nooit twee keer voor,
     * ook niet als hij via een andere URL binnenkomt.
     *
     * @return array{urls: array<string, true>, hashes: array<string, true>}
     */
    private function gebruikteBronnen(string $eigenSlug): array
    {
        $urls = [];
        foreach (Post::where('slug', '!=', $eigenSlug)->pluck('briefing') as $briefing) {
            if (! empty($briefing['cover_bron'])) {
                $urls[$briefing['cover_bron']] = true;
            }
        }
        $hashes = [];
        foreach (glob(public_path('blog-covers/*.jpg')) ?: [] as $bestand) {
            if (basename($bestand) !== $eigenSlug . '.jpg') {
                $hashes[md5_file($bestand)] = true;
            }
        }

        return ['urls' => $urls, 'hashes' => $hashes];
    }

    /**
     * Openverse: rechtenvrije stockfoto's zonder API-key. Voorkeur voor
     * CC0/publiek domein (geen naamsvermelding nodig); anders CC-BY mét
     * bronvermelding die de blogpagina onder de foto toont.
     *
     * @return array{pad: string, credit: ?string, bron: ?string}|null
     */
    private function vanOpenverse(string $slug, string $cluster, int $variatie, string $titel = ''): ?array
    {
        $queries = $this->zoektermenVoor($titel, $cluster);

        foreach ([['cc0,pdm', false], [null, true]] as [$licenties, $creditNodig]) {
            foreach ($queries as $qi => $query) {
                try {
                    $params = [
                        'q' => $query,
                        'license_type' => 'commercial',
                        'categories' => 'photograph',
                        'size' => 'large',
                        'per_page' => 20,
                    ];
                    if ($licenties) {
                        $params['license'] = $licenties;
                    }
                    $resp = Http::retry(2, 3000, throw: false)->timeout(20)
                        ->withHeaders(['User-Agent' => 'MijnPizzeria-blog/1.0'])
                        ->get('https://api.openverse.org/v1/images/', $params)->throw()->json();

                    $gebruikt = $this->gebruikteBronnen($slug);
                    $bruikbaar = array_values(array_filter($resp['results'] ?? [], function ($r) use ($gebruikt) {
                        $bBreed = (int) ($r['width'] ?? 0);
                        $bHoog = (int) ($r['height'] ?? 1);

                        return $bBreed >= 1200 && $bBreed / max(1, $bHoog) >= 1.1
                            && ! empty($r['url']) && ! isset($gebruikt['urls'][$r['url']]);
                    }));
                    if (! $bruikbaar) {
                        continue;
                    }

                    /* Laat het model de best passende foto kiezen op titel; daarna
                       de rest als reserve, voor als de keuze al gebruikt blijkt */
                    $eerste = $this->slimsteKeuze($titel, $bruikbaar) ?? (($variatie + $qi * 7) % count($bruikbaar));
                    $volgorde = array_unique(array_merge([$eerste], range(0, count($bruikbaar) - 1)));

                    $keuringen = 0;
                    foreach ($volgorde as $index) {
                        /* Fouten per kandidaat afvangen: één kapotte download mag
                           de overige kandidaten van deze zoekterm niet meeslepen */
                        try {
                            $foto = $bruikbaar[$index];
                            $binair = Http::timeout(30)->withHeaders(['User-Agent' => 'MijnPizzeria-blog/1.0'])
                                ->get($foto['url'])->throw()->body();
                        } catch (Throwable) {
                            continue;
                        }
                        if (strlen($binair) < 20000 || isset($gebruikt['hashes'][md5($binair)])) {
                            continue;
                        }
                        /* Visuele keuring: maximaal 4 foto's per zoekterm bekijken */
                        if ($keuringen >= 4) {
                            break;
                        }
                        $keuringen++;
                        if (! $this->fotoGoedgekeurd($binair, $titel)) {
                            continue;
                        }

                        $credit = null;
                        if ($creditNodig && ! in_array($foto['license'] ?? '', ['cc0', 'pdm'], true)) {
                            $credit = 'Foto: ' . ($foto['creator'] ?? 'onbekend') . ' via Openverse (' . strtoupper($foto['license'] ?? 'cc') . ')';
                        }

                        return $this->bewaar($slug, $binair, $credit, $foto['url']);
                    }
                    continue;
                } catch (Throwable $e) {
                    Log::debug('cover-zoekterm mislukt', ['slug' => $slug, 'query' => $query, 'fout' => $e->getMessage()]);
                    continue; // volgende query of licentiegroep proberen
                }
            }
        }

        return null;
    }

    private function vanPexels(string $slug, string $cluster, int $variatie, string $titel = ''): ?array
    {
        $key = (string) config('seo-content.pexels_key');
        if ($key === '') {
            return null;
        }

        try {
            $queries = $this->zoektermenVoor($titel, $cluster);
            $query = $queries[$variatie % count($queries)];
            $resp = Http::timeout(20)->withHeaders(['Authorization' => $key])
                ->get('https://api.pexels.com/v1/search', [
                    'query' => $query,
                    'orientation' => 'landscape',
                    'per_page' => 20,
                ])->throw()->json();

            $fotos = $resp['photos'] ?? [];
            if (! $fotos) {
                return null;
            }

            $gebruikt = $this->gebruikteBronnen($slug);
            $aantal = count($fotos);
            $keuringen = 0;
            for ($i = 0; $i < $aantal; $i++) {
                $foto = $fotos[($variatie + $i) % $aantal];
                $bron = $foto['src']['large2x'] ?? $foto['src']['large'] ?? null;
                if (! $bron || isset($gebruikt['urls'][$bron])) {
                    continue;
                }
                try {
                    $binair = Http::timeout(30)->get($bron)->throw()->body();
                } catch (Throwable) {
                    continue;
                }
                if (isset($gebruikt['hashes'][md5($binair)])) {
                    continue;
                }
                if ($keuringen >= 4) {
                    break;
                }
                $keuringen++;
                if (! $this->fotoGoedgekeurd($binair, $titel)) {
                    continue;
                }

                return $this->bewaar($slug, $binair, null, $bron);
            }

            return null;
        } catch (Throwable) {
            return null; // covers zijn nice-to-have; nooit blokkerend voor het draften
        }
    }

    /**
     * Gegenereerde cover in de huisstijl: warme crema-achtergrond met zachte
     * tomato- en gold-gloed en pepperoni-stipjes. De slug is de seed, dus elk
     * artikel krijgt een eigen, reproduceerbare compositie.
     */
    private function genereer(string $slug): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $b = 1600;
        $h = 900;

        /* Achtergrond + gloed eerst klein tekenen en daarna zacht opschalen:
           het resamplen poetst de ringen van de alpha-lagen weg */
        $kb = 400;
        $kh = 225;
        $klein = imagecreatetruecolor($kb, $kh);
        for ($y = 0; $y < $kh; $y++) {
            $t = $y / $kh;
            /* Van crema (#FFF6E8) naar crema-dark (#F9EBD2) */
            $kleur = imagecolorallocate($klein, (int) (255 - 6 * $t), (int) (246 - 11 * $t), (int) (232 - 22 * $t));
            imageline($klein, 0, $y, $kb, $y, $kleur);
        }

        mt_srand(crc32($slug));

        /* Twee zachte gloedvlekken: één tomato, één gold */
        foreach ([[230, 57, 70], [245, 179, 1]] as $gloed) {
            $cx = mt_rand((int) ($kb * 0.15), (int) ($kb * 0.85));
            $cy = mt_rand((int) ($kh * 0.15), (int) ($kh * 0.85));
            $straal = mt_rand(90, 140);
            $kleur = imagecolorallocatealpha($klein, $gloed[0], $gloed[1], $gloed[2], 115);
            for ($i = 9; $i >= 1; $i--) {
                imagefilledellipse($klein, $cx, $cy, (int) ($straal * 2 * $i / 9), (int) ($straal * 1.5 * $i / 9), $kleur);
            }
        }

        for ($i = 0; $i < 8; $i++) {
            imagefilter($klein, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $img = imagecreatetruecolor($b, $h);
        imagecopyresampled($img, $klein, 0, 0, 0, 0, $b, $h, $kb, $kh);

        /* Pepperoni-stipjes: verspreide cirkels in tomato met een lichte rand */
        $stippen = mt_rand(6, 9);
        for ($s = 0; $s < $stippen; $s++) {
            $cx = mt_rand(80, $b - 80);
            $cy = mt_rand(80, $h - 80);
            $r = mt_rand(18, 52);
            $vulling = imagecolorallocatealpha($img, 230, 57, 70, mt_rand(88, 108));
            $rand = imagecolorallocatealpha($img, 183, 31, 46, 95);
            imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $vulling);
            imageellipse($img, $cx, $cy, $r * 2, $r * 2, $rand);
        }

        /* Sparkle-motief: één grote ster plus een strooisel kleintjes, in gold */
        $sterren = mt_rand(4, 6);
        for ($s = 0; $s < $sterren; $s++) {
            $cx = mt_rand(80, $b - 80);
            $cy = mt_rand(80, $h - 80);
            $r = $s === 0 ? mt_rand(110, 170) : mt_rand(14, 60);
            $hoek = deg2rad(mt_rand(-25, 25));
            $dekking = $s === 0 ? mt_rand(78, 90) : mt_rand(88, 108);
            $kleur = imagecolorallocatealpha($img, 245, 179, 1, $dekking);
            $basis = [[0, -1], [0.18, -0.18], [1, 0], [0.18, 0.18], [0, 1], [-0.18, 0.18], [-1, 0], [-0.18, -0.18]];
            $punten = [];
            foreach ($basis as [$x, $y]) {
                $punten[] = (int) ($cx + $r * ($x * cos($hoek) - $y * sin($hoek)));
                $punten[] = (int) ($cy + $r * ($x * sin($hoek) + $y * cos($hoek)));
            }
            imagefilledpolygon($img, $punten, $kleur);
        }
        mt_srand();

        $map = public_path('blog-covers');
        if (! is_dir($map)) {
            @mkdir($map, 0775, true);
        }
        $pad = '/blog-covers/' . $slug . '.jpg';
        imagejpeg($img, public_path($pad), 85);

        return $pad;
    }

    /** @return array{pad: string, credit: ?string, bron: ?string} */
    private function bewaar(string $slug, string $binair, ?string $credit, ?string $bron): array
    {
        $map = public_path('blog-covers');
        if (! is_dir($map)) {
            @mkdir($map, 0775, true);
        }
        $pad = '/blog-covers/' . $slug . '.jpg';
        file_put_contents(public_path($pad), $binair);

        return ['pad' => $pad, 'credit' => $credit, 'bron' => $bron];
    }

    /**
     * Zoektermen per artikel: het model bedenkt 2 specifieke Engelse termen
     * bij de titel; de clustertermen dienen als aanvulling en vangnet.
     *
     * @return string[]
     */
    private function zoektermenVoor(string $titel, string $cluster): array
    {
        $basis = config('seo-content.cover_queries')[$cluster] ?? ['pizza restaurant'];
        if ($titel === '' || ! $this->llm->isGeconfigureerd()) {
            return $basis;
        }

        try {
            $resp = $this->llm->json(
                'Je bedenkt zoektermen voor stockfotosites (Engels). Termen moeten CONCREET en visueel zijn: objecten, scenes of handelingen die fotografeerbaar zijn. Geen abstracties, geen jargon.',
                'Blogartikel voor pizzeria-eigenaren: "' . $titel . '". Geef JSON: {"termen": ["...", "..."]} met precies 2 brede Engelse zoektermen van 2 tot 4 woorden.',
                1500
            );
            $termen = array_values(array_filter(array_map('strval', $resp['termen'] ?? [])));

            return array_values(array_unique(array_merge(array_slice($termen, 0, 2), $basis)));
        } catch (Throwable) {
            return $basis;
        }
    }

    /**
     * Visuele keuring: het model ZIET de foto en keurt hem goed of af.
     * Bij twijfel of een fout in de keuring laten we de foto door, zodat
     * covers nooit blokkerend zijn voor het draften.
     */
    private function fotoGoedgekeurd(string $binair, string $titel): bool
    {
        if ($titel === '' || ! $this->llm->isGeconfigureerd()) {
            return true;
        }

        try {
            $oordeel = $this->llm->beoordeelAfbeelding(
                $this->maakMiniatuur($binair),
                'Je keurt stockfoto\'s voor covers van blogartikelen voor pizzeria-eigenaren, over online bestellen, marketing en het runnen van een pizzeria. Een foto is ALLEEN geschikt als hij visueel logisch past bij het onderwerp. Ongeschikt: beelden uit een ander vakgebied (medisch, kantoortorens, sport, dieren), zichtbare merknamen of gezichten prominent in beeld, rommelige of gedateerde beelden.',
                'Artikel: "' . $titel . '". Is deze foto geschikt als cover? Geef JSON: {"geschikt": true|false, "reden": "kort"}'
            );

            return (bool) ($oordeel['geschikt'] ?? true);
        } catch (Throwable) {
            return true;
        }
    }

    /** Verkleinde JPEG voor de visuele keuring (klein houden = snel en goedkoop) */
    private function maakMiniatuur(string $binair): string
    {
        if (! function_exists('imagecreatefromstring')) {
            return $binair;
        }
        $bron = @imagecreatefromstring($binair);
        if (! $bron) {
            return $binair;
        }
        $breed = imagesx($bron);
        $doelBreed = min(800, $breed);
        $doelHoog = (int) round(imagesy($bron) * $doelBreed / $breed);
        $klein = imagecreatetruecolor($doelBreed, $doelHoog);
        imagecopyresampled($klein, $bron, 0, 0, 0, 0, $doelBreed, $doelHoog, $breed, imagesy($bron));
        ob_start();
        imagejpeg($klein, null, 72);
        $resultaat = (string) ob_get_clean();

        return $resultaat !== '' ? $resultaat : $binair;
    }

    /** Vraagt het LLM welke fototitel het best bij het artikel past. */
    private function slimsteKeuze(string $titel, array $kandidaten): ?int
    {
        if ($titel === '' || ! $this->llm->isGeconfigureerd()) {
            return null;
        }

        try {
            $lijst = collect($kandidaten)->map(fn ($r, $i) => $i . '. ' . mb_substr((string) ($r['title'] ?? 'zonder titel'), 0, 90))->implode("\n");
            $keuze = $this->llm->json(
                'Je kiest een coverfoto voor een blogartikel voor pizzeria-eigenaren. Kies de foto die er het best bij past. Vermijd foto\'s die duidelijk over een ander vakgebied gaan (medisch, sport, dieren, enzovoort).',
                "Artikel: \"{$titel}\"\nKandidaten:\n{$lijst}\nGeef JSON: {\"index\": nummer}",
                2000
            );
            $index = $keuze['index'] ?? null;

            return (is_int($index) && $index >= 0 && $index < count($kandidaten)) ? $index : null;
        } catch (Throwable) {
            return null;
        }
    }
}
